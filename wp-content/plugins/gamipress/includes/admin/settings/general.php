<?php
/**
 * Admin General Settings
 *
 * @package     GamiPress\Admin\Settings\General
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.7
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * General Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_settings_general_meta_boxes( $meta_boxes ) {

    $meta_boxes['general-settings'] = array(
        'title' => gamipress_dashicon( 'admin-generic' ) . __( 'General Settings', 'gamipress' ),
        'fields' => apply_filters( 'gamipress_general_settings_fields', array(
            'minimum_role' => array(
                'name' => __( 'Minimum role to administer GamiPress', 'gamipress' ),
                'desc' => __( 'Minimum role a user needs to access to GamiPress management areas.', 'gamipress' ),
                'type' => 'select',
                'options' => gamipress_get_allowed_manager_capabilities(),
            ),
            'points_image_size' => array(
                'name' => __( 'Points Image Size', 'gamipress' ),
                'desc' => __( 'Maximum dimensions for the points featured image.', 'gamipress' ),
                'type' => 'size',
                'default' => array(
                    'width' => 50,
                    'height' => 50,
                ),
            ),
            'achievement_image_size' => array(
                'name' => __( 'Achievement Image Size', 'gamipress' ),
                'desc' => __( 'Maximum dimensions for the achievements featured image.', 'gamipress' ),
                'type' => 'size',
            ),
            'rank_image_size' => array(
                'name' => __( 'Rank Image Size', 'gamipress' ),
                'desc' => __( 'Maximum dimensions for ranks featured image.', 'gamipress' ),
                'type' => 'size',
            ),
            'disable_admin_bar_menu' => array(
                'name' => __( 'Disable Top Bar Menu', 'gamipress' ),
                'desc' => __( 'Check this option to disable the GamiPress top bar menu.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'disable_shortcodes_editor' => array(
                'name' => __( 'Disable Shortcodes Editor', 'gamipress' ),
                'desc' => __( 'Check this option to disable the shortcodes editor.', 'gamipress' ) . '<br>'
                . '<small>' . __( 'Check this option if you are experiencing black screens in your theme settings or in your page builder forms.', 'gamipress' ) . '</small>',
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            'debug_mode' => array(
                'name' => __( 'Debug Mode', 'gamipress' ),
                'desc' => __( 'Check this option to enable the debug mode.', 'gamipress' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_general_meta_boxes', 'gamipress_settings_general_meta_boxes' );

/**
 * Register custom WordPress image size(s)
 *
 * @since 1.0.0
 */
function gamipress_register_image_sizes() {

    // Register points image size
    $points_image_size = gamipress_get_option( 'points_image_size', array( 'width' => 50, 'height' => 50 ) );

    add_image_size( 'gamipress-points', absint( $points_image_size['width'] ), absint( $points_image_size['height'] ) );

    // Register achievement image size
    $achievement_image_size = gamipress_get_option( 'achievement_image_size', array( 'width' => 100, 'height' => 100 ) );

    add_image_size( 'gamipress-achievement', absint( $achievement_image_size['width'] ), absint( $achievement_image_size['height'] ) );

    // Register rank image size
    $rank_image_size = gamipress_get_option( 'rank_image_size', array( 'width' => 100, 'height' => 100 ) );

    add_image_size( 'gamipress-rank', absint( $rank_image_size['width'] ), absint( $rank_image_size['height'] ) );

}
add_action( 'init', 'gamipress_register_image_sizes' );

/**
 * Get capability required for GamiPress administration.
 *
 * @since  1.0.0
 *
 * @return string User capability.
 */
function gamipress_get_manager_capability() {

    $minimum_role = gamipress_get_option( 'minimum_role', 'manage_options' );
    $allowed_capabilities = array_keys( gamipress_get_allowed_manager_capabilities() );
    // Do not allow to bypass subscribers capability in any way
    $excluded_capabilities = array( 'read' );

    // Check if capability is allowed
    if ( ! in_array( $minimum_role, $allowed_capabilities ) || in_array( $minimum_role, $excluded_capabilities ) ) {
        // If not allowed, manually update the settings
        $update_capability = get_option( 'gamipress_settings' );
        $update_capability['minimum_role'] = 'manage_options';
        update_option( 'gamipress_settings',  $update_capability );

        // Set minimum role to manage_options
        $minimum_role = 'manage_options';
    }
       
    return $minimum_role;

}

/**
 * Allowed capabilities
 *
 * @since 6.0.0
 *
 * @return array
 */
function gamipress_get_allowed_manager_capabilities() {

    $allowed_capabilities = array(
        'manage_options' => __( 'Administrator', 'gamipress' ),
        'delete_others_posts' => __( 'Editor', 'gamipress' ),
        'publish_posts' => __( 'Author', 'gamipress' ), 
    );
    
    return apply_filters( 'gamipress_allowed_manager_capabilities', $allowed_capabilities );
}