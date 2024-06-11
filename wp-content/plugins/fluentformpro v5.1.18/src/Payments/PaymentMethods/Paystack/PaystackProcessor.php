<?php

namespace FluentFormPro\Payments\PaymentMethods\Paystack;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FluentForm\App\Helpers\Helper;
use FluentForm\Framework\Helpers\ArrayHelper;
use FluentFormPro\Payments\PaymentHelper;
use FluentFormPro\Payments\PaymentMethods\BaseProcessor;

class PaystackProcessor extends BaseProcessor
{
    public $method = 'paystack';

    protected $form;

    public function init()
    {
        add_action('fluentform/process_payment_' . $this->method, array($this, 'handlePaymentAction'), 10, 6);

        add_action('fluentform/ipn_razorpay_action_paid', array($this, 'handlePaid'), 10, 2);
        add_action('fluentform/ipn_razorpay_action_refunded', array($this, 'handleRefund'), 10, 3);

        add_filter('fluentform/validate_payment_items_' . $this->method, [$this, 'validateSubmittedItems'], 10, 4);


        add_action('fluentform/rendering_payment_method_' . $this->method, array($this, 'addCheckoutJs'), 10, 3);

        add_action('wp_ajax_fluentform_paystack_confirm_payment', array($this, 'confirmModalPayment'));
        add_action('wp_ajax_nopriv_fluentform_paystack_confirm_payment', array($this, 'confirmModalPayment'));

    }

    public function handlePaymentAction($submissionId, $submissionData, $form, $methodSettings, $hasSubscriptions, $totalPayable)
    {
        $this->setSubmissionId($submissionId);
        $this->form = $form;
        $submission = $this->getSubmission();

        if ($hasSubscriptions) {
            do_action('fluentform/log_data', [
                'parent_source_id' => $submission->form_id,
                'source_type'      => 'submission_item',
                'source_id'        => $submission->id,
                'component'        => 'Payment',
                'status'           => 'info',
                'title'            => __('Skip Subscription Item', 'fluentformpro'),
                'description'      => __('PayStack does not support subscriptions right now!', 'fluentformpro')
            ]);
        }

        $uniqueHash = md5($submission->id . '-' . $form->id . '-' . time() . '-' . mt_rand(100, 999));

        $transactionId = $this->insertTransaction([
            'transaction_type' => 'onetime',
            'transaction_hash' => $uniqueHash,
            'payment_total'    => $this->getAmountTotal(),
            'status'           => 'pending',
            'currency'         => PaymentHelper::getFormCurrency($form->id),
            'payment_mode'     => $this->getPaymentMode()
        ]);

        $transaction = $this->getTransaction($transactionId);
        $this->maybeShowModal($transaction, $submission, $form, $methodSettings);
    }


    protected function getPaymentMode($formId = false)
    {
        $isLive = PaystackSettings::isLive($formId);
        if ($isLive) {
            return 'live';
        }
        return 'test';
    }

    public function handlePaid($submission, $transaction, $vendorTransaction)
    {
        $this->setSubmissionId($submission->id);

        // Check if actions are fired
        if ($this->getMetaData('is_form_action_fired') == 'yes') {
            return $this->completePaymentSubmission(false);
        }

        $status = 'paid';

        // Let's make the payment as paid
        $updateData = [
            'payment_note'  => maybe_serialize($vendorTransaction),
            'charge_id'     => sanitize_text_field($vendorTransaction['id']),
            'payer_email'   => $vendorTransaction['customer']['email'],
            'payment_total' => intval($vendorTransaction['amount']),
            'currency'      => strtoupper($vendorTransaction['currency'])
        ];

        if ($cardBrand = ArrayHelper::get($vendorTransaction, 'authorization.brand')) {
            $updateData['card_brand'] = $cardBrand;
        }

        if ($last4 = ArrayHelper::get($vendorTransaction, 'authorization.last4')) {
            $updateData['card_last_4'] = $last4;
        }

        $this->updateTransaction($transaction->id, $updateData);
        $this->changeSubmissionPaymentStatus($status);
        $this->changeTransactionStatus($transaction->id, $status);
        $this->recalculatePaidTotal();
        $returnData = $this->getReturnData();
        $this->setMetaData('is_form_action_fired', 'yes');

        if(intval($vendorTransaction['amount']) != $transaction->payment_total || strtoupper($transaction->currency) != strtoupper($vendorTransaction['currency'])) {
            $logData = [
                'parent_source_id' => $submission->form_id,
                'source_type'      => 'submission_item',
                'source_id'        => $submission->id,
                'component'        => 'Payment',
                'status'           => 'error',
                'title'            => __('Transaction Amount Mismatch - PayStack', 'fluentformpro'),
                'description' => __('Transaction Amount should be '.PaymentHelper::formatMoney($transaction->payment_total, $transaction->currency).' but received '.PaymentHelper::formatMoney(intval($vendorTransaction['amount']), strtoupper($vendorTransaction['currency'])), 'fluentformpro')
            ];
            do_action('fluentform/log_data', $logData);
        }

        return $returnData;
    }

    public function handleRefund($refundAmount, $submission, $vendorTransaction)
    {
        $this->setSubmissionId($submission->id);
        $transaction = $this->getLastTransaction($submission->id);
        $this->updateRefund($refundAmount, $transaction, $submission, $this->method);
    }

    public function validateSubmittedItems($errors, $paymentItems, $subscriptionItems, $form)
    {
        $singleItemTotal = 0;
        foreach ($paymentItems as $paymentItem) {
            if ($paymentItem['line_total']) {
                $singleItemTotal += $paymentItem['line_total'];
            }
        }
        if (count($subscriptionItems) && !$singleItemTotal) {
            $errors[] = __('PayStack Error: PayStack does not support subscriptions right now!', 'fluentformpro');
        }
        return $errors;
    }

    public function addCheckoutJs($methodElement, $element, $form)
    {
        wp_enqueue_script('paystack', 'https://js.paystack.co/v1/inline.js', [], FLUENTFORMPRO_VERSION);
        wp_enqueue_script('ff_paystack_handler', FLUENTFORMPRO_DIR_URL . 'public/js/paystack_handler.js', ['jquery'], FLUENTFORMPRO_VERSION);
    }

    public function maybeShowModal($transaction, $submission, $form, $methodSettings)
    {
        // check if the currency is valid
        $currency = strtoupper($transaction->currency);
        if (!in_array($currency, ['NGN', 'GHS', 'ZAR', 'USD', 'KES'])) {
            wp_send_json([
                'errors'      => $currency . __('is not supported by PayStack payment method', 'fluentformpro'),
                'append_data' => [
                    '__entry_intermediate_hash' => Helper::getSubmissionMeta($submission->id, '__entry_intermediate_hash')
                ]
            ], 423);
        }

        $keys = PaystackSettings::getApiKeys($form->id);

        $modalData = [
            'key'      => $keys['api_key'],
            'email'    => $transaction->payer_email,
            'ref'      => $transaction->transaction_hash,
            'amount'   => intval($transaction->payment_total),
            'currency' => $currency, //
            'label'    => $form->title,
            'metadata' => [
                'payment_handler' => 'Fluent Forms',
                'form_id'         => $form->id,
                'transaction_id'  => $transaction->id,
                'form'            => $form->title
            ]
        ];

        $logData = [
            'parent_source_id' => $submission->form_id,
            'source_type'      => 'submission_item',
            'source_id'        => $submission->id,
            'component'        => 'Payment',
            'status'           => 'info',
            'title'            => __('PayStack Modal is initiated', 'fluentformpro'),
            'description'      => __('PayStack Modal is initiated to complete the payment', 'fluentformpro')
        ];

        do_action('fluentform/log_data', $logData);

        # Tell the client to handle the action
        wp_send_json_success([
            'nextAction'       => 'paystack',
            'actionName'       => 'initPaystackModal',
            'submission_id'    => $submission->id,
            'modal_data'       => $modalData,
            'transaction_hash' => $transaction->transaction_hash,
            'message'          => __('Payment Modal is opening, Please complete the payment', 'fluentformpro'),
            'confirming_text'  => __('Confirming Payment, Please wait...', 'fluentformpro'),
            'result'           => [
                'insert_id' => $submission->id
            ],
            'append_data'      => [
                '__entry_intermediate_hash' => Helper::getSubmissionMeta($transaction->submission_id, '__entry_intermediate_hash')
            ]
        ], 200);
    }

    public function confirmModalPayment()
    {
        $data = $_REQUEST;
        $transactionHash = sanitize_text_field(ArrayHelper::get($data, 'trxref'));
        $transaction = $this->getTransaction($transactionHash, 'transaction_hash');

        if (!$transaction || $transaction->status != 'pending') {
            wp_send_json([
                'errors' => __('Payment Error: Invalid Request', 'fluentformpro'),
            ], 423);
        }

        $paymentReference = sanitize_text_field(ArrayHelper::get($data, 'reference'));
        $vendorPayment = (new API())->makeApiCall('transaction/verify/' . $paymentReference, [], $transaction->form_id);

        if (is_wp_error($vendorPayment)) {
            $logData = [
                'parent_source_id' => $transaction->form_id,
                'source_type'      => 'submission_item',
                'source_id'        => $transaction->submission_id,
                'component'        => 'Payment',
                'status'           => 'failed',
                'title'            => __('PayStack Payment is failed to verify', 'fluentformpro'),
                'description'      => $vendorPayment->get_error_message()
            ];
            do_action('fluentform/log_data', $logData);

            wp_send_json([
                'errors'      => __('Payment Error: ', 'fluentformpro') . $vendorPayment->get_error_message(),
                'append_data' => [
                    '__entry_intermediate_hash' => Helper::getSubmissionMeta($transaction->submission_id, '__entry_intermediate_hash')
                ]
            ], 423);
        }

        $vendorData = $vendorPayment['data'];

        if ($vendorData['status'] == 'success') {
            $logData = [
                'parent_source_id' => $transaction->form_id,
                'source_type'      => 'submission_item',
                'source_id'        => $transaction->submission_id,
                'component'        => 'Payment',
                'status'           => 'success',
                'title'            => __('Payment Success by PayStack', 'fluentformpro'),
                'description'      => __('PayStack payment has been marked as paid. TransactionId: ', 'fluentformpro') . $vendorData['id']
            ];

            do_action('fluentform/log_data', $logData);
            $this->setSubmissionId($transaction->submission_id);
            $submission = $this->getSubmission();
            $returnData = $this->handlePaid($submission, $transaction, $vendorData);
            $returnData['vendor_data'] = $vendorData;
            wp_send_json_success($returnData, 200);
        }

        wp_send_json([
            'errors'      => __('Payment could not be verified. Please contact site admin', 'fluentformpro'),
            'append_data' => [
                '__entry_intermediate_hash' => Helper::getSubmissionMeta($transaction->submission_id, '__entry_intermediate_hash')
            ]
        ], 423);

    }
}
