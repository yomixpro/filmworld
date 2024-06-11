<?php

/**
 * Archive Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

?>

<div id="bbpress-forums" class="bbpress-wrapper">

	<?php if (bbp_allow_search()) : ?>
		<?php bbp_get_template_part('form', 'search'); ?>

	<?php endif; ?>

	<?php do_action('bbp_template_before_topic_tag_description'); ?>

	<?php if (bbp_is_topic_tag()) : ?>

		<?php bbp_topic_tag_description(array('before' => '<div class="bbp-template-notice info"><ul class="list-inline m-0"><li>', 'after' => '</li></ul></div>')); ?>

	<?php endif; ?>

	<?php do_action('bbp_template_after_topic_tag_description'); ?>

	<?php do_action('bbp_template_before_topics_index'); ?>

	<?php if (bbp_has_topics()) : ?>

		<?php bbp_get_template_part('pagination', 'topics'); ?>

		<?php bbp_get_template_part('loop',       'topics'); ?>

	<?php else : ?>

		<?php bbp_get_template_part('feedback',   'no-topics'); ?>

	<?php endif; ?>

	<?php do_action('bbp_template_after_topics_index'); ?>

</div>