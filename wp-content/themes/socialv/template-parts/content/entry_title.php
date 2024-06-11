<?php

/**
 * Template part for displaying a post's title
 *
 * @package socialv
 */

namespace SocialV\Utility;

if (!is_singular(get_post_type()) && !empty(trim(get_the_title()))) {
	echo '<a href="' . esc_url(get_permalink()) . '" rel="bookmark"><h3 class="entry-title">' . get_the_title() . '</h3></a>';
}
