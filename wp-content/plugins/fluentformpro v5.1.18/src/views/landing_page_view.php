<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <?php if($settings['featured_image']): ?>
        <meta property="og:image" content="<?php echo $settings['featured_image']; ?>">
    <?php endif; ?>
    <?php if($settings['description']): ?>
        <meta property="og:description" content="<?php echo strip_tags($settings['description']); ?>">
    <?php endif; ?>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Imagetoolbar" content="No"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
        wp_head();
    ?>
    <style type="text/css">
        <?php if($isEmbeded): ?>
        body.ff_landing_page_body {
            font-family: system-ui, -asystem, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif;
        }
        .ff_landing_wrapper {
            padding: 10px 20px !important;
        }
        .ff_landing_page_body.ff_landing_iframe .ff_landing_wrapper .ff_landing_form {
            max-width: 100%;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        <?php endif; ?>
        <?php if($settings['background_image'] && !$isEmbeded): ?>
        body.ff_landing_page_body {
            background-image: url("<?php echo  $settings['background_image']; ?>") !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            background-position: center center !important;
            background-attachment: fixed;
        }
        body.ff_landing_page_body::after {
            background-color: #6f6f6f;
            content: "";
            display: block;
            position: fixed;
            top: 0px;
            left: 0px;
            width: 100%;
            z-index: -1;
            opacity: 0.4;
            bottom: 0;
            right: 0;
        }
        <?php endif; ?>
        body.ff_landing_page_body {
            line-height: 1.65714285714286;
        }
    </style>
    <style id="ff_landing_css" type="text/css">
        body.ff_landing_page_body {
            border-top-color: <?php echo $bg_color; ?> !important;
            background-color: <?php echo $bg_color; ?>;
        }
    </style>
    <style id="ff_landing_page_settings"></style>
</head>
<body class="ff_landing_page_body ff_landing_page_<?php echo $form_id; ?> <?php if($isEmbeded) { echo 'ff_landing_iframe'; } ?>">

<div class="ff_landing_wrapper ff_landing_design_<?php echo $settings['design_style']; ?> ff_landing_layout_<?php echo $settings['layout']; ?>">
    <div class="ff_landing_form_wrapper">
        <?php if(isset($settings['form_shadow']) && is_array($settings['form_shadow'])):
            // check have shadow or not if have make shadow
            $shadow = array_map(function ($s) {
                return $s['position'] . ' ' .
                    $s['horizontal'] . "px " .
                    $s['vertical'] . 'px ' .
                    $s['blur'] . "px " .
                    $s['spread'] . 'px ' .
                    $s['color'];
            }, $settings['form_shadow']);
            $shadow = join(',' , $shadow);
        ?>
        <div class="ff_landing_form" style="box-shadow: <?php echo $shadow ?>;">
        <?php endif;?>
            <?php if($has_header): ?>
                <div class="ff_landing_header">
                    <?php if($settings['logo']):?>
                        <div class="ff_landing-custom-logo">
                            <img src="<?php echo $settings['logo']; ?>" alt="Form Logo">
                        </div>
                    <?php endif; ?>
                    <?php if($settings['title']): ?>
                        <h1><?php echo $settings['title']; ?></h1>
                    <?php endif; ?>
                    <?php if($settings['description']): ?>
                        <div class="ff_landing_desc">
                            <?php echo $settings['description']; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="ff_landing_body">
                <?php echo do_shortcode($landing_content); ?>
            </div>
        </div>
    </div>
    <?php if($settings['layout'] != 'default'):
        $brightness = isset($settings['brightness']) ? intval($settings['brightness']) : 0;
        $brightnessCss = '';
    
        if ($brightness > 0) {
            $brightnessCss .= 'contrast(' . ((100 - $brightness) / 100) . ') ';
        }
        $brightnessCss .= ' brightness(' . (1 + $brightness / 100) . ')';
    
        $layout = isset($settings['layout']) ? $settings['layout'] : 'default';
    ?>
    <div class="ff_landing_media_holder">
        <div style=" filter: <?php echo $brightnessCss ?>;" class="fcc_block_media_attachment fc_i_layout_<?php echo $layout ?>" >
        <?php
        if(!empty($settings['media'])):
            if ($settings['layout'] == 'media_right_full' || $settings['layout'] == 'media_left_full') {
                $imagePositionCSS = $settings['media_x_position'] .'% '. $settings['media_y_position'] . '%';
            }else{
                $imagePositionCSS = '';
            }
        ?>

        <div class="fc_image_holder">
                <img style=" object-position: <?php echo  $imagePositionCSS ;?> "
                     alt="<?php echo  $settings['alt_text'] ;?>"
                     src="<?php echo  $settings['media'] ;?>"
                />
            </div>
        </div>
        <?php endif;?>
    </div>
    <?php endif;?>


</div>
<?php
wp_footer();
?>
</body>
</html>

