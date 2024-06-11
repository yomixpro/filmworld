<?php
// Exit if the file is accessed directly over web
if (!defined('ABSPATH')) {
	exit;
}
?>
<?php while (mpp_have_media()) : mpp_the_media(); ?>
	<?php
	$type = mpp_get_media_type();
	$id = mpp_get_media_id();
	?>
	<div class="socialv-media-inner <?php mpp_media_class('mpp-u-12-24'); ?>" data-mpp-type="<?php echo esc_attr($type); ?>">

		<?php do_action('mpp_before_media_item'); ?>

		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-top">
			<?php do_action('mpp_media_meta_top'); ?>
		</div>

		<?php

		$args = array(
			'src'      => mpp_get_media_src(),
			'loop'     => false,
			'autoplay' => false,
			'poster'   => mpp_get_media_src('thumbnail'),
			'width'    => 320,
			'height'   => 180,
		);

		?>

		<div class="socialv-media-container mpp-item-content mpp-video-content mpp-video-player">
			<?php if (mpp_is_oembed_media(mpp_get_media_id())) : ?>
				<?php echo mpp_get_oembed_content(mpp_get_media_id(), 'mid'); ?>
			<?php else : ?>
				<a href="<?php mpp_media_permalink(); ?>" <?php mpp_media_html_attributes(array('class' => 'mpp-item-title mpp-media-title mpp-video-title')); ?> data-mpp-type="<?php echo esc_attr($type); ?>">
					<img src="<?php mpp_media_src('thumbnail'); ?>" alt="<?php echo esc_attr(mpp_get_media_title()); ?> " loading="lazy" />
				</a>
				<?php if (mpp_user_can_delete_media($id)) : ?>
					<span class="delete-media-icon">
						<a href="javascript:void(0);" class="socialv-delete-media" data-id="<?php echo esc_attr($id); ?>">
							<i class="iconly-Delete icli"></i>
						</a>
					</span>

					<span class="socialv-check select-media-icon">
						<label>
							<input type="checkbox" class="select-media-checkbox single" value="<?php echo esc_attr($id); ?>">
						</label>
					</span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		
		<a href="<?php mpp_media_permalink(); ?>" <?php mpp_media_html_attributes(array('class' => 'socialv-video-single mpp-item-title mpp-media-title mpp-video-title')); ?> data-mpp-type="<?php echo esc_attr($type); ?>">
			<?php mpp_media_title(); ?>
		</a>

		<div class="mpp-type-icon"><?php do_action('mpp_type_icon', mpp_get_media_type(), mpp_get_media()); ?></div>

		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-bottom">
			<?php do_action('mpp_media_meta'); ?>
		</div>

		<?php do_action('mpp_after_media_item'); ?>
	</div>

<?php endwhile; ?>