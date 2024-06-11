<?php

namespace FluentFormPro\Payments\PaymentMethods\PayPal;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FluentForm\Framework\Helpers\ArrayHelper;
use FluentFormPro\Payments\PaymentMethods\PayPal\API\IPN;

class PayPalHandler
{
    protected $key = 'paypal';

    public function init()
    {

        add_filter('fluentform/payment_settings_' . $this->key, function () {
            return PayPalSettings::getSettings();
        });

        add_filter('fluentform/payment_method_settings_validation_' . $this->key, array($this, 'validateSettings'), 10, 2);

        if(!$this->isEnabled()) {
            return;
        }

        add_filter('fluentform/transaction_data_' . $this->key, array($this, 'modifyTransaction'), 10, 1);

        add_filter(
            'fluentform/available_payment_methods',
            [$this, 'pushPaymentMethodToForm']
        );

	    (new IPN())->init();
        (new PayPalProcessor())->init();
    }

    public function pushPaymentMethodToForm($methods)
    {
        $methods[$this->key] = [
            'title' => __('PayPal', 'fluentformpro'),
            'enabled' => 'yes',
            'method_value' => $this->key,
            'settings' => [
                'option_label' => [
                    'type' => 'text',
                    'template' => 'inputText',
                    'value' => 'Pay with PayPal',
                    'label' => __('Method Label', 'fluentformpro'),
                ],
                'require_shipping_address' => [
                    'type' => 'checkbox',
                    'template' => 'inputYesNoCheckbox',
                    'value' => 'no',
                    'label' => __('Require Shipping Address', 'fluentformpro'),
                ]
            ]
        ];

        return $methods;
    }

    public function validateSettings($errors, $settings)
    {
        if (ArrayHelper::get($settings, 'is_active') == 'no') {
            return [];
        }

        if (!ArrayHelper::get($settings, 'paypal_email')) {
            $errors['paypal_email'] = __('PayPal Email Address is required', 'fluentformpro');
        }

        if (!ArrayHelper::get($settings, 'payment_mode')) {
            $errors['payment_mode'] = __('Please select Payment Mode', 'fluentformpro');
        }

        return $errors;
    }

    public function modifyTransaction($transaction)
    {
        if ($transaction->charge_id) {
            $sandbox = 'test' == $transaction->payment_mode ? 'sandbox.' : '';
            $transaction->action_url =  'https://www.' . $sandbox . 'paypal.com/activity/payment/' . $transaction->charge_id;
        }

        if ($transaction->status == 'requires_capture') {
            $transaction->additional_note = __('<b>Action Required: </b> The payment has been authorized but not captured yet. Please <a target="_blank" rel="noopener" href="' . $transaction->action_url . '">Click here</a> to capture this payment in stripe.com', 'fluentformpro');
        }

        return $transaction;
    }

    public function isEnabled()
    {
        $settings = PayPalSettings::getSettings();
        return $settings['is_active'] == 'yes';
    }
}
