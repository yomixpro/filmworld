<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

namespace SocialV\Utility;

defined('ABSPATH') || exit;
get_header('shop');
$post_section = socialv()->post_style();
$is_sidebar = (bool)is_active_sidebar('product_sidebar');
$product_style = ($_COOKIE['product_view']['is_grid'] == '2') ? 'product-grid-style ' : 'product-list-style ';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');
?>
<div class="container">
	<div class="row <?php echo esc_attr($post_section['row_reverse']); ?>">

		<?php
		if ($is_sidebar) {
			get_sidebar();
			echo '<div class="col-xl-9 col-sm-12 socialv-product-main-list ' . esc_attr($product_style) . ' " data-pagedno="' . esc_attr($paged) . '" >';
		} else {
			echo '<div class="col-xl-12 col-sm-12 socialv-product-main-list ' . esc_attr($product_style) . ' " data-pagedno="' . esc_attr($paged) . '" >';
		}
		?>

		<?php
		if (woocommerce_product_loop()) {

			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action('woocommerce_before_shop_loop');

			woocommerce_product_loop_start();

			if (wc_get_loop_prop('total')) {
				while (have_posts()) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 */
					do_action('woocommerce_shop_loop');

					wc_get_template_part('content', 'product');
				}
			}
			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action('woocommerce_after_shop_loop');
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action('woocommerce_no_products_found');
		}
		echo '</div>'; ?>

	</div>
</div>


<?php
do_action('woocommerce_after_main_content');
get_footer('shop');
