<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\cirkle;
?>
<div class="no-results not-found">
	<h2 class="page-title"><?php esc_html_e( 'Nothing Found', 'cirkle' ); ?></h2>
	<div class="row">
		<div class="col-lg-8">
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<p><?php printf( wp_kses( esc_html__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>', 'cirkle' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
			<?php elseif ( is_search() ) : ?>
				<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'cirkle' ); ?></p>
				<?php get_search_form(); ?>
			<?php else : ?>
				<p><?php esc_html_e( "It seems we can't find what you're looking for. Perhaps searching can help.", 'cirkle' ); ?></p>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div><!-- .page-content -->
	</div><!-- .page-content -->
</div><!-- .no-results -->