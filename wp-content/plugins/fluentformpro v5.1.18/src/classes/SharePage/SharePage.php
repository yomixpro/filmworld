<?php

namespace FluentFormPro\classes\SharePage;

use FluentForm\App\Helpers\Helper;
use FluentForm\App\Modules\Acl\Acl;
use FluentForm\Framework\Helpers\ArrayHelper;

class SharePage
{
    public $metaKey = '_landing_page_settings';

    public function boot()
    {
        $enabled = $this->isEnabled();

        add_action('wp', [$this, 'renderLandingForm']);

        add_filter('fluentform/global_addons', function ($addOns) use ($enabled) {
            $addOns['sharePages'] = [
                'title'       => 'Landing Pages',
                'description' => __('Create completely custom "distraction-free" form landing pages to boost conversions', 'fluentformpro'),
                'logo'        => fluentFormMix('img/integrations/landing_pages.png'),
                'enabled'     => ($enabled) ? 'yes' : 'no',
                'config_url'  => '',
                'category'    => ''
            ];
            return $addOns;
        }, 9);

        if (!$enabled) {
            return;
        }

        add_filter('fluentform/form_settings_menu', function ($menu) {
            $menu['landing_pages'] = [
                'title' => __('Landing Page', 'fluentformpro'),
                'slug'  => 'form_settings',
                'hash'  => 'landing_pages',
                'route' => '/landing_pages'
            ];
            return $menu;
        });

        add_action('wp_ajax_ff_get_landing_page_settings', [$this, 'getSettingsAjax']);
        add_action('wp_ajax_ff_store_landing_page_settings', [$this, 'saveSettingsAjax']);

    }

    public function getSettingsAjax()
    {
        $formId = intval($_REQUEST['form_id']);
        Acl::verify('fluentform_forms_manager', $formId);
        $settings = $this->getSettings($formId);

        $shareUrl = '';
        if ($settings['status'] == 'yes') {
            $params = [
                'ff_landing' => $formId
            ];
            $shareUrl = add_query_arg($params, site_url('/'));
        }

        wp_send_json_success([
            'settings'  => $settings,
            'share_url' => $shareUrl
        ]);
    }

    public function saveSettingsAjax()
    {
        $formId = intval($_REQUEST['form_id']);
        Acl::verify('fluentform_forms_manager', $formId);
        $settings = $_REQUEST['settings'];
        $formattedSettings = wp_unslash($settings);
        $formattedSettings['description'] = wp_kses_post(wp_unslash($settings['description']));
        Helper::setFormMeta($formId, $this->metaKey, $formattedSettings);

        $shareUrl = '';
        if ($formattedSettings['status'] == 'yes') {
            $shareUrl = add_query_arg(['ff_landing' => $formId], site_url('/'));
        }

        wp_send_json_success([
            'message'   => __('Settings successfully updated'),
            'share_url' => $shareUrl
        ]);
    }

    public function getSettings($formId)
    {
        $settings = Helper::getFormMeta($formId, $this->metaKey, []);
    
        $defaults = [
            'status'           => 'no',
            'logo'             => '',
            'title'            => '',
            'description'      => '',
            'color_schema'     => '#4286c4',
            'custom_color'     => '#4286c4',
            'design_style'     => 'modern',
            'featured_image'   => '',
            'background_image' => '',
            'layout'           => 'default',
            'media'            => fluentFormGetRandomPhoto(),
            'brightness'       => 0,
            'alt_text'         => '',
            'media_x_position' => 50,
            'media_y_position' => 50
        ];

        return wp_parse_args($settings, $defaults);
    }

    public function renderLandingForm()
    {
        $ff_landing = ArrayHelper::get($_GET, 'ff_landing');

        if (!$ff_landing || is_admin()) {
            return;
        }

        $hasConfirmation = false;
        if (isset($_REQUEST['entry_confirmation'])) {
            do_action_deprecated(
                'fluentformpro_entry_confirmation',
                [
                    $_REQUEST
                ],
                FLUENTFORM_FRAMEWORK_UPGRADE,
                'fluentform/entry_confirmation',
                'Use fluentform/entry_confirmation instead of fluentformpro_entry_confirmation.'
            );
            do_action('fluentform/entry_confirmation', $_REQUEST);
            $hasConfirmation = true;
        }

        $formId = intval($_GET['ff_landing']);

        $form = wpFluent()->table('fluentform_forms')->where('id', $formId)->first();

        if (!$form) {
            return;
        }

        $settings = $this->getSettings($formId);


        if (ArrayHelper::get($settings, 'status') != 'yes') {
            return;
        }

        $pageTitle = $form->title;

        if ($settings['title']) {
            $pageTitle = $settings['title'];
        }

        add_action('wp_enqueue_scripts', function () use ($formId) {
            $theme = Helper::getFormMeta($formId, '_ff_selected_style');
            $styles = $theme ? [$theme] : [];

            do_action('fluentform/load_form_assets', $formId, $styles);
            wp_enqueue_style('fluent-form-styles');
            wp_enqueue_style('fluentform-public-default');
            wp_enqueue_script('fluent-form-submission');
        });

        $backgroundColor = ArrayHelper::get($settings, 'color_schema');

        if ($backgroundColor == 'custom') {
            $backgroundColor = ArrayHelper::get($settings, 'custom_color');
        }


        $landingContent = '[fluentform id="' . $formId . '"]';
        if(!$hasConfirmation) {
            $salt = ArrayHelper::get($settings, 'share_url_salt');
            if($salt && $salt != ArrayHelper::get($_REQUEST, 'form')) {
                $landingContent = __('Sorry, You do not have access to this form', 'fluentformpro');
                $pageTitle = __('No Access', 'fluentformpro');
                $settings['title'] = '';
                $settings['description'] = '';
            }
        }

        $data = [
            'settings'        => $settings,
            'title'           => $pageTitle,
            'form_id'         => $formId,
            'form'            => $form,
            'bg_color'        => $backgroundColor,
            'landing_content' => $landingContent,
            'has_header'      => $settings['logo'] || $settings['title'] || $settings['description'],
            'isEmbeded' => !!ArrayHelper::get($_GET, 'embedded')
        ];
    
        $data = apply_filters_deprecated(
            'fluentform_landing_vars',
            [
                $data,
                $formId
            ],
            FLUENTFORM_FRAMEWORK_UPGRADE,
            'fluentform/landing_vars',
            'Use fluentform/landing_vars instead of fluentform_landing_vars.'
        );

        $landingVars = apply_filters('fluentform/landing_vars', $data, $formId);

        $this->loadPublicView($landingVars);
    }

    public function loadPublicView($landingVars)
    {
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_style(
                'fluent-form-landing',
                FLUENTFORMPRO_DIR_URL . 'public/css/form_landing.css',
                [],
                FLUENTFORMPRO_VERSION
            );
        });

        add_filter('pre_get_document_title', function ($title) use ($landingVars) {
            $separator = apply_filters('document_title_separator', '-');
            return $landingVars['title'] . ' ' . $separator . ' ' . get_bloginfo('name', 'display');
        });

        // let's deregister all the style and scripts here
        add_action('wp_print_scripts', function () {
            global $wp_scripts;
            $contentUrl = content_url();
            if ($wp_scripts) {
                foreach ($wp_scripts->queue as $script) {
                    
                    if (!isset($wp_scripts->registered[$script])) {
                        continue;
                    }
                    
                    $src = $wp_scripts->registered[$script]->src;
                    $shouldLoad = strpos($src, $contentUrl) !== false && (
                            strpos($src, 'fluentform') !== false ||
                            strpos($src, 'AffiliateWP') !== false
                        );
        
                    if (!$shouldLoad) {
                        wp_dequeue_script($wp_scripts->registered[$script]->handle);
                        // wp_deregister_script($wp_scripts->registered[$script]->handle);
                    }
                }
            }
        },1);

        if(isset($_GET['embedded'])) {
            add_action('wp_print_styles', function () {
                global $wp_styles;
                if($wp_styles) {
                    foreach ($wp_styles->queue as $style) {
                        $src = $wp_styles->registered[$style]->src;
                        if (!strpos($src, 'fluentform') !== false) {
                            wp_dequeue_style($wp_styles->registered[$style]->handle);
                        }
                    }
                }
            }, 1);
            if($landingVars['settings']['design_style'] == 'modern') {
                $landingVars['settings']['design_style'] = 'classic';
                $landingVars['bg_color'] = '#fff';
            }
        }
        
        status_header(200);
        echo $this->loadView('landing_page_view', $landingVars);
        exit(200);
    }

    public function loadView($view, $data = [])
    {
        $file = FLUENTFORMPRO_DIR_PATH . 'src/views/' . $view . '.php';
        extract($data);
        ob_start();
        include($file);
        return ob_get_clean();
    }

    public function isEnabled()
    {
        $globalModules = get_option('fluentform_global_modules_status');

        $sharePages = ArrayHelper::get($globalModules, 'sharePages');

        if (!$sharePages || $sharePages == 'yes') {
            return true;
        }

        return false;
    }
}
