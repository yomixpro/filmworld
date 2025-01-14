<?php
/**
 * Template for displaying order comment.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/checkout/order-comment.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.1
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="learn-press-checkout-comment">
	<h4><?php esc_html_e( 'Additional Information', 'socialv' ); ?></h4>
	<textarea name="order_comments" class="order-comments_info" placeholder="<?php esc_attr_e( 'Note to administrator', 'socialv' ); ?>"></textarea>
</div>
 