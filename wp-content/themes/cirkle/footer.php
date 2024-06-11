<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0.2
 */

namespace radiustheme\cirkle;
use radiustheme\cirkle\RDTheme;
?>

	<?php 
	if ( class_exists( 'BuddyPress' ) && class_exists( 'bbPress' ) ) {
		if ( is_buddypress() || is_bbpress() ) { ?>
				</div>
			</div>
	<?php } 
	} ?>

	<?php get_template_part( 'template-parts/footer/footer', RDTheme::$footer_style ); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>