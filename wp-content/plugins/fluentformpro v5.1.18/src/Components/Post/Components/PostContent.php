<?php

namespace FluentFormPro\Components\Post\Components;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FluentForm\App\Helpers\Helper;
use FluentForm\App\Services\FormBuilder\BaseFieldManager;
use FluentForm\Framework\Helpers\ArrayHelper;

class PostContent extends BaseFieldManager
{
    public function __construct($key, $title, $tags = [], $position = 'post')
    {
        parent::__construct(
            $key, $title, $tags, $position
        );

        add_action('wp_enqueue_scripts', function () {
            wp_register_script(
                'fluentform_tiny_mce_editor',
                FLUENTFORMPRO_DIR_URL . 'public/js/tinyMceInit.js',
                ['jquery'],
                FLUENTFORMPRO_VERSION,
                true
            );
        });


    }

    function getComponent()
    {
        return [
            'index'          => 1,
            'element'        => $this->key,
            'attributes'     => [
                'name' => 'post_content',
                'value' => '',
                'id' => '',
                'class' => '',
                'placeholder' => '',
                'rows' => 3,
                'cols' => 2,
                'maxlength' => ''
            ],
            'settings'       => [
                'container_class'    => '',
                'placeholder'        => '',
                'label'              => $this->title,
                'label_placement'    => '',
                'help_message'       => '',
                'admin_field_label'  => '',
                'validation_rules'   => [
                    'required' => [
                        'value'          => false,
                        'global'         => true,
                        'message'        => Helper::getGlobalDefaultMessage('required'),
                        'global_message' => Helper::getGlobalDefaultMessage('required'),
                    ]
                ],
                'conditional_logics' => []
            ],
            'editor_options' => [
                'title'      => $this->title,
                'icon_class' => 'ff-edit-textarea',
                'template'   => 'inputTextarea'
            ],
        ];
    }

    public function getGeneralEditorElements()
    {
        return [
            'label',
            'label_placement',
            'admin_field_label',
            'placeholder',
            'rows',
            'cols',
            'validation_rules',
        ];
    }

    public function getAdvancedEditorElements()
    {
        return [
            'value',
            'container_class',
            'class',
            'help_message',
            'name',
            'maxlength',
            'conditional_logics',
        ];
    }

    public function render($data, $form)
    {
        if (function_exists('wp_enqueue_editor')) {
            add_filter('user_can_richedit', '__return_true');
            wp_enqueue_editor();
          //  wp_enqueue_media();
            wp_enqueue_script('fluentform_tiny_mce_editor');
        }

        $elementName = $data['element'];

        $data = apply_filters_deprecated(
            'fluentform_rendering_field_data_' . $elementName,
            [
                $data,
                $form
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/rendering_field_data_' . $elementName,
            'Use fluentform/rendering_field_data_' . $elementName . ' instead of fluentform_rendering_field_data_' . $elementName
        );

        $data = apply_filters('fluentform/rendering_field_data_' . $elementName, $data, $form);

        $textareaValue = $this->extractValueFromAttributes($data);

        $data['attributes']['class'] = @trim(
            'ff-el-form-control fluentform-post-content ' . $data['attributes']['class']
        );

        $data['attributes']['id'] = $this->makeElementId($data, $form);

        $data['attributes']['tabindex'] = \FluentForm\App\Helpers\Helper::getNextTabIndex();

        $elMarkup = '<textarea %s>%s</textarea>';

        $elMarkup = sprintf(
            $elMarkup,
            $this->buildAttributes($data['attributes']),
            $textareaValue
        );

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
            'Use fluentform/rendering_field_html_' . $elementName . ' instead of fluentform_rendering_field_html_' . $elementName
        );

        echo apply_filters('fluentform/rendering_field_html_' . $elementName, $html, $data, $form);
    }

    public function pushTags($tags, $form)
    {
        if ($form->type != 'post' && $this->key  == 'post_content') {
            return $tags;
        }
        $tags[$this->key] = $this->tags;
        return $tags;
    }
}
