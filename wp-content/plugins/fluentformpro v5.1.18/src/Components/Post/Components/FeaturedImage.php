<?php

namespace FluentFormPro\Components\Post\Components;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FluentForm\App\Helpers\Helper;
use FluentForm\App\Services\FormBuilder\BaseFieldManager;
use FluentForm\Framework\Helpers\ArrayHelper;

class FeaturedImage extends BaseFieldManager
{
    public function __construct()
    {
        parent::__construct(
            'featured_image',
            'Featured Image',
            ['image', 'featured_image'],
            'post'
        );

        add_filter('fluentform/response_render_featured_image', function ($response, $field, $form_id, $isHtml = false) {
            return \FluentForm\App\Modules\Form\FormDataParser::formatImageValues($response, $isHtml);
        }, 10, 4);

    }

    function getComponent()
    {
        return [
            'index'          => 3,
            'element'        => $this->key,
            'attributes'     => [
                'name'        => $this->key,
                'class'       => '',
                'value'       => '',
                'type'        => 'file',
                'placeholder' => '',
                'accept' => 'image/*'
            ],
            'settings'       => [
                'container_class'    => '',
                'placeholder'        => '',
                'label'              => $this->title,
                'label_placement'    => '',
                'help_message'       => '',
                'admin_field_label'  => '',
                'btn_text' => __('Choose File', 'fluentformpro'),
                'validation_rules'   => [
                    'required'            => [
                        'value'          => false,
                        'global'         => true,
                        'message'        => Helper::getGlobalDefaultMessage('required'),
                        'global_message' => Helper::getGlobalDefaultMessage('required'),
                    ],
                    'max_file_size'       => [
                        'value'          => 1048576,
                        '_valueFrom'     => 'MB',
                        'global'         => true,
                        'message'        => Helper::getGlobalDefaultMessage('max_file_size'),
                        'global_message' => Helper::getGlobalDefaultMessage('max_file_size'),
                    ],
                    'max_file_count'      => [
                        'value'          => 1,
                        'global'         => true,
                        'message'        => Helper::getGlobalDefaultMessage('max_file_count'),
                        'global_message' => Helper::getGlobalDefaultMessage('max_file_count'),
                    ],
                    'allowed_image_types' => [
                        'value'          => array(),
                        'global'         => true,
                        'message'        => Helper::getGlobalDefaultMessage('allowed_image_types'),
                        'global_message' => Helper::getGlobalDefaultMessage('allowed_image_types'),
                    ]
                ],
                'conditional_logics' => []
            ],
            'editor_options' => [
                'title' => $this->title,
                'icon_class' => 'ff-edit-images',
                'template' => 'inputFile'
            ],
        ];
    }

    public function getGeneralEditorElements()
    {
        return [
            'label',
            'admin_field_label',
            'btn_text',
            'placeholder',
            'value',
            'label_placement',
            'validation_rules',
        ];
    }

    public function render($data, $form)
    {
        $elementName = $data['element'];

        $data = apply_filters_deprecated(
            'fluentform_rendering_field_data_'.$elementName,
            [
                $data,
                $form
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/rendering_field_data_'.$elementName,
            'Use fluentform/rendering_field_data_'.$elementName . ' instead of fluentform_rendering_field_data_'.$elementName
        );

        $data = apply_filters('fluentform/rendering_field_data_'.$elementName, $data, $form);

        $data['attributes']['class'] = @trim(
            'ff-el-form-control '. $data['attributes']['class'].' ff-screen-reader-element'
        );

        $data['attributes']['id'] = $this->makeElementId($data, $form);

        $data['attributes']['multiple'] = false;

        if($tabIndex = \FluentForm\App\Helpers\Helper::getNextTabIndex()) {
            $data['attributes']['tabindex'] = $tabIndex;
        }
        
        $btnText = ArrayHelper::get($data, 'settings.btn_text');

        if(!$btnText) {
            $btnText = __('Choose File', 'fluentformpro');
        }

        $elMarkup = "<label for='".$data['attributes']['id']."' class='ff_file_upload_holder'><span class='ff_upload_btn ff-btn'>".$btnText."</span> <input %s></label>";

        $elMarkup = sprintf($elMarkup, $this->buildAttributes($data['attributes'], $form));

        $html = $this->buildElementMarkup($elMarkup, $data, $form);

        $html = apply_filters_deprecated(
            'fluentform_rendering_field_html_' . $elementName,
            [
                $html,
                $data,
                $form
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/rendering_field_html_' . $elementName,
            'Use fluentform/rendering_field_html_' . $elementName . ' instead of fluentform_rendering_field_html_'. $elementName
        );

        echo apply_filters('fluentform/rendering_field_html_' . $elementName, $html, $data, $form);
        
        $this->enqueueProScripts();
    }

    public function enqueueProScripts()
    {
        wp_enqueue_script('fluentform-uploader-jquery-ui-widget');
        wp_enqueue_script('fluentform-uploader-iframe-transport');
        wp_enqueue_script('fluentform-uploader');
    }

    public function pushTags($tags, $form)
    {
        if ($form->type != 'post') {
            return $tags;
        }
        $tags[$this->key] = $this->tags;
        return $tags;
    }
}
