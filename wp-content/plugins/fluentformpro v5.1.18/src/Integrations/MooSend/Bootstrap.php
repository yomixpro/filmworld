<?php

namespace FluentFormPro\Integrations\MooSend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FluentForm\App\Http\Controllers\IntegrationManagerController;
use FluentForm\Framework\Foundation\Application;
use FluentForm\Framework\Helpers\ArrayHelper;

class Bootstrap extends IntegrationManagerController
{
    public function __construct(Application $app)
    {
        parent::__construct(
            $app,
            'MooSend',
            'moosend',
            '_fluentform_moosend_settings',
            'moosend_feeds',
            20
        );

        $this->description = 'Connect MooSend with Fluent Forms and subscribe a contact when a form is submitted.';

        $this->logo = fluentFormMix('img/integrations/moosend_logo.png');
        $this->registerAdminHooks();

//        add_filter('fluentform/notifying_async_moosend', '__return_false');
    }

    public function getGlobalFields($fields)
    {
        return [
            'logo'             => $this->logo,
            'menu_title'       => __('MooSend API Settings', 'fluentformpro'),
            'menu_description' => __('MooSend is email marketing software. Use Fluent Forms to collect customer information and automatically add it as MooSend subscriber list.', 'fluentformpro'),
            'valid_message'    => __('Your MooSend API Key is valid', 'fluentformpro'),
            'invalid_message'  => __('Your MooSend API Key is not valid', 'fluentformpro'),
            'save_button_text' => __('Save Settings', 'fluentformpro'),
            'fields'           => [
                'apiKey' => [
                    'type'        => 'password',
                    'placeholder' => 'API Key',
                    'label_tips'  => __("Enter your MooSend API Key, if you do not have <br>Please log in to your MooSend account and go to<br>Account -> Api Key", 'fluentformpro'),
                    'label'       => __('MooSend API Key', 'fluentformpro'),
                ]
            ],
            'hide_on_valid'    => true,
            'discard_settings' => [
                'section_description' => __('Your MooSend API integration is up and running', 'fluentformpro'),
                'button_text'         => __('Disconnect MooSend', 'fluentformpro'),
                'data'                => [
                    'apiKey' => ''
                ],
                'show_verify'         => true
            ]
        ];
    }

    public function getGlobalSettings($settings)
    {
        $globalSettings = get_option($this->optionKey);
        if (!$globalSettings) {
            $globalSettings = [];
        }
        $defaults = [
            'apiKey' => '',
            'status' => ''
        ];

        return wp_parse_args($globalSettings, $defaults);
    }

    public function saveGlobalSettings($settings)
    {
        if (!$settings['apiKey']) {
            $integrationSettings = [
                'apiKey' => '',
                'status' => false
            ];
            // Update the reCaptcha details with siteKey & secretKey.
            update_option($this->optionKey, $integrationSettings, 'no');
            wp_send_json_success([
                'message' => __('Your settings has been updated', 'fluentformpro'),
                'status'  => false
            ], 200);
        }

        //  Verify API key now
        try {
            $api = new API($settings['apiKey']);
            $result = $api->auth_test();
            if (!empty($result['Error'])) {
                throw new \Exception($result['Error']);
            }
        } catch (\Exception $exception) {
            wp_send_json_error([
                'message' => $exception->getMessage(),
                'status'  => false
            ], 400);
        }

        // Integration key is verified now, Proceed now

        $integrationSettings = [
            'apiKey' => sanitize_text_field($settings['apiKey']),
            'status' => true
        ];

        // Update the reCaptcha details with siteKey & secretKey.
        update_option($this->optionKey, $integrationSettings, 'no');

        wp_send_json_success([
            'message' => __('Your MooSend api key has been verified and successfully set', 'fluentformpro'),
            'status'  => true
        ], 200);
    }


    public function pushIntegration($integrations, $formId)
    {
        $integrations[$this->integrationKey] = [
            'title'                 => $this->title . ' Integration',
            'logo'                  => $this->logo,
            'is_active'             => $this->isConfigured(),
            'configure_title'       => __('Configuration required!', 'fluentformpro'),
            'global_configure_url'  => admin_url('admin.php?page=fluent_forms_settings#general-moosend-settings'),
            'configure_message'     => __('MooSend is not configured yet! Please configure your MooSend api first', 'fluentformpro'),
            'configure_button_text' => __('Set MooSend API', 'fluentformpro')
        ];
        return $integrations;
    }

    public function getIntegrationDefaults($settings, $formId)
    {
        return [
            'name'                   => '',
            'SubscriberName'         => '', // Name in MooSend
            'Email'                  => '',
            'HasExternalDoubleOptIn' => false,
            'CustomFields'           => (object)[],
            'list_id'                => '', // MailingListID
            'conditionals'           => [
                'conditions' => [],
                'status'     => false,
                'type'       => 'all'
            ],
            'enabled'                => true
        ];
    }

    public function getSettingsFields($settings, $formId)
    {
        return [
            'fields'              => [
                [
                    'key'         => 'name',
                    'label'       => __('Name', 'fluentformpro'),
                    'required'    => true,
                    'placeholder' => __('Your Feed Name', 'fluentformpro'),
                    'component'   => 'text'
                ],
                [
                    'key'         => 'list_id',
                    'label'       => __('MooSend Mailing Lists', 'fluentformpro'),
                    'placeholder' => __('Select MooSend Mailing List', 'fluentformpro'),
                    'tips'        => __('Select the MooSend Mailing List you would like to add your contacts to.', 'fluentformpro'),
                    'component'   => 'list_ajax_options',
                    'options'     => $this->getLists(),
                ],
                [
                    'key'                => 'CustomFields',
                    'require_list'       => true,
                    'label'              => __('Map Fields', 'fluentformpro'),
                    'tips'               => __('Associate your MooSend merge tags to the appropriate Fluent Forms fields by selecting the appropriate form field from the list.', 'fluentformpro'),
                    'component'          => 'map_fields',
                    'field_label_remote' => __('MooSend Field', 'fluentformpro'),
                    'field_label_local'  => __('Form Field', 'fluentformpro'),
                    'primary_fileds'     => [
                        [
                            'key'           => 'Email',
                            'label'         => __('Email Address', 'fluentformpro'),
                            'required'      => true,
                            'input_options' => 'emails'
                        ],
                        [
                            'key'   => 'SubscriberName',
                            'label' => __('Name', 'fluentformpro')
                        ]
                    ]
                ],
                [
                    'key'             => 'HasExternalDoubleOptIn',
                    'require_list'    => true,
                    'label'           => __('Double Opt-in', 'fluentformpro'),
                    'tips'            => __('When the double opt-in option is enabled,<br />MooSend will send a confirmation email<br />to the user and will only add them to your <br /MooSend list upon confirmation.', 'fluentformpro'),
                    'component'       => 'checkbox-single',
                    'checkbox_label' => __('Enable Double Opt-in', 'fluentformpro')
                ],
                [
                    'require_list' => true,
                    'key'          => 'conditionals',
                    'label'        => __('Conditional Logics', 'fluentformpro'),
                    'tips'         => __('Allow MooSend integration conditionally based on your submission values', 'fluentformpro'),
                    'component'    => 'conditional_block'
                ],
                [
                    'require_list'    => true,
                    'key'             => 'enabled',
                    'label'           => __('Status', 'fluentformpro'),
                    'component'       => 'checkbox-single',
                    'checkbox_label' => __('Enable This feed', 'fluentformpro')
                ]
            ],
            'button_require_list' => true,
            'integration_title'   => $this->title
        ];
    }

    public function getMergeFields($list, $listId, $formId)
    {
        $api = $this->getApiInstance();
        $remoteList = $api->getList($listId);

        $mergetags = [];

        if (!$remoteList) {
            return $mergetags;
        }


        foreach ($remoteList['CustomFieldsDefinition'] as $tag) {
            $mergetags[$tag['Name']] = $tag['Name'];
        }

        return $mergetags;
    }

    protected function getLists()
    {
        $api = $this->getApiInstance();
        $lists = $api->getLists();
        $formattedLists = [];
        foreach ($lists as $list) {
            $formattedLists[$list['ID']] = $list['Name'];
        }
        return $formattedLists;
    }

    /*
     * Form Submission Hooks Here
     */
    public function notify($feed, $formData, $entry, $form)
    {
        $feedData = $feed['processedValues'];

        if (!is_email($feedData['Email'])) {
            $feedData['Email'] = ArrayHelper::get($formData, $feedData['Email']);
        }

        if (!is_email($feedData['Email'])) {
            do_action('fluentform/integration_action_result', $feed, 'failed', __('Moosend API call has been skipped because no valid email available', 'fluentformpro'));
            // No Valid email found
            return;
        }

        $subscriber = [
            'Name'  => $feedData['SubscriberName'],
            'Email' => $feedData['Email'],
        ];

        $customFields = ArrayHelper::get($feedData, 'CustomFields', []);

        $subscriber['CustomFields'] = $customFields;

        $subscriber = array_filter($subscriber);
    
        $subscriber = apply_filters_deprecated(
            'fluentform_integration_data_' . $this->integrationKey,
            [
                $subscriber,
                $feed,
                $entry
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/integration_data_' . $this->integrationKey,
            'Use fluentform/integration_data_' . $this->integrationKey . ' instead of fluentform_integration_data_' . $this->integrationKey
        );
        $subscriber = apply_filters('fluentform/integration_data_' . $this->integrationKey, $subscriber, $feed, $entry);

        $api = $this->getApiInstance();
        // ! is required for Moosend. Their API is bit confusing
        $isDoubleOptIn = !ArrayHelper::isTrue($feedData, 'HasExternalDoubleOptIn');
        $result = $api->subscribe($feedData['list_id'], $subscriber, $isDoubleOptIn);

        if (is_wp_error($result)) {
            do_action('fluentform/integration_action_result', $feed, 'failed', $result->get_error_message());
            return;
        }
        do_action('fluentform/integration_action_result', $feed, 'success', __('Moosend feed has been successfully initialed and pushed data', 'fluentformpro'));

    }

    protected function getApiInstance()
    {
        $settings = $this->getApiSettings();
        return new API($settings['apiKey']);
    }
}
