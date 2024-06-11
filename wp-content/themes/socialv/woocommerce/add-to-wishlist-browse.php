<?php

/**
 * Add to wishlist button template - Browse list
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist_url              string Url to wishlist page
 * @var $exists                    bool Whether current product is already in wishlist
 * @var $show_exists               bool Whether to show already in wishlist link on multi wishlist
 * @var $product_id                int Current product id
 * @var $product_type              string Current product type
 * @var $label                     string Button label
 * @var $browse_wishlist_text      string Browse wishlist text
 * @var $already_in_wishslist_text string Already in wishlist text
 * @var $product_added_text        string Product added text
 * @var $icon                      string Icon for Add to Wishlist button
 * @var $link_classes              string Classed for Add to Wishlist button
 * @var $available_multi_wishlist  bool Whether add to wishlist is available or not
 * @var $disable_wishlist          bool Whether wishlist is disabled or not
 * @var $template_part             string Template part
 * @var $loop_position             string Loop position
 */

if (!defined('YITH_WCWL')) {
	exit;
} // Exit if accessed directly

global $product;
?>

<!-- BROWSE WISHLIST MESSAGE -->
<div class="yith-wcwl-wishlistexistsbrowse">
	<span class="feedback">
		<a href="<?php echo esc_url($wishlist_url); ?>" rel="nofollow" data-title="<?php echo esc_attr($browse_wishlist_text); ?>">
		    <?php
			$wishlist = YITH_WCWL()->get_products( [ 'wishlist_id' => 'all' , 'user_id' => get_current_user_id() ] );
			if($wishlist){  
				?> <i class="iconly-Heart icbo"></i> <?php
			} else {
				?> <i class="iconly-Heart icli"></i> <?php
			}
			?>
			<span class="socialv-wihslist-btn"><?php echo wp_kses($label,'post'); ?></span>
		</a>
	</span>
</div>