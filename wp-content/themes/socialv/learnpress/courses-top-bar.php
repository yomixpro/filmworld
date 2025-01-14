<?php

/**
 * Template for displaying top-bar in archive course page.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.1
 */

defined('ABSPATH') || exit;

$layouts = learn_press_courses_layouts();
$active  = learn_press_get_courses_layout();
$s       = LP_Request::get('c_search');
?>

<div class="lp-courses-bar <?php echo esc_attr($active); ?>">
	<div class="socialv-bp-searchform">
		<form class="search-courses" method="get" action="<?php echo esc_url_raw(learn_press_get_page_link('courses')); ?>">
			<div class="search-input">
				<input type="hidden" name="post_type" value="<?php echo esc_attr(LP_COURSE_CPT); ?>">
				<input type="hidden" name="taxonomy" value="<?php echo esc_attr(get_queried_object()->taxonomy ?? $_GET['taxonomy'] ?? ''); ?>">
				<input type="hidden" name="term_id" value="<?php echo esc_attr(get_queried_object()->term_id ?? $_GET['term_id'] ?? ''); ?>">
				<input type="hidden" name="term" value="<?php echo esc_attr(get_queried_object()->slug ?? $_GET['term'] ?? ''); ?>">
				<input type="text" class="form-control" placeholder="<?php esc_attr_e('Search courses...', 'socialv'); ?>" name="c_search" value="<?php echo esc_attr($s); ?>">
				<button type="submit" class="btn-search"><i class="iconly-Search icli"></i></button>
			</div>
		</form>
	</div>
	<div class="switch-layout">
		<?php foreach ($layouts as $layout => $value) : ?>
			<input type="radio" name="lp-switch-layout-btn" value="<?php echo esc_attr($layout); ?>" id="lp-switch-layout-btn-<?php echo esc_attr($layout); ?>" <?php checked($layout, $active); ?>>
			<label class="switch-btn <?php echo esc_attr($layout); ?>" title="<?php echo sprintf(esc_attr__('Switch to %s', 'socialv'), $layout); ?>" for="lp-switch-layout-btn-<?php echo esc_attr($layout); ?>"></label>
		<?php endforeach; ?>
	</div>
</div>