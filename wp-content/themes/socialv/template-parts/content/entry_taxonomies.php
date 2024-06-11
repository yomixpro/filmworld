<?php

/**
 * Template part for displaying a post's taxonomy terms
 *
 * @package socialv
 */

namespace SocialV\Utility;

$taxonomies = wp_list_filter(
	get_object_taxonomies($post, 'objects'),
	array(
		'public' => true,
	)
);
$post_tag = get_the_tags();
if ($post_tag) {
?>
	<ul class="socialv-blogtag list-inline">
		<li class="socialv-label"><?php esc_html_e("Tags:", "socialv") ?></li>
		<?php foreach ($post_tag as $tag) { ?>
			<li>
				<a href="<?php echo get_tag_link($tag->term_id) ?>"><?php echo esc_html($tag->name); ?></a>
			</li>
		<?php } ?>
	</ul>
<?php } ?>