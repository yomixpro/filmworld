<?php

/**
 * BuddyPress - Activity Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

/**
 * Fires before the start of the activity loop.
 *
 * @since 1.2.0
 */

do_action('bp_before_activity_loop');
?>

<?php if (bp_has_activities(bp_ajax_querystring('activity'))) : ?>

	<?php if (empty($_POST['page'])) : ?>
		<ul id="activity-stream" class="activity-list  socialv-list-post">
			
			<?php endif; ?>
			
			<?php do_action("socialv_before_activity_loop"); ?>
			
			<?php while (bp_activities()) : bp_the_activity(); ?>
		
			<?php bp_get_template_part('activity/entry'); ?>

		<?php endwhile; ?>
		<?php if (bp_activity_has_more_items()) : ?>

			<li class="load-more">
				<a class="socialv-loader" href="<?php bp_activity_load_more_link() ?>"></a>
			</li>

		<?php endif; ?>

		<?php if (empty($_POST['page'])) : ?>

		</ul>

	<?php endif; ?>

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e('Sorry, there was no activity found. Please try a different filter.', 'socialv'); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the finish of the activity loop.
 *
 * @since 1.2.0
 */
do_action('bp_after_activity_loop'); ?>

<?php if (empty($_POST['page'])) : ?>

	<form name="activity-loop-form" id="activity-loop-form" method="post">

		<?php wp_nonce_field('activity_filter', '_wpnonce_activity_filter'); ?>

	</form>

<?php endif;
