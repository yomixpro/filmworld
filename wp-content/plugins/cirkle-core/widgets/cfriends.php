<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Cirkle_Core;

use \WP_Widget;
use \RT_Widget_Fields;
use radiustheme\Cirkle\Helper;

/**
 * BuddyPress Login Widget.
 *
 * @since 1.9.0
 */
/**
 * The User Friends widget class.
 *
 * @since 1.9.0
 */
class CBP_Friends_Widget extends WP_Widget {

	/**
	 * Class constructor.
	 *
	 * @since 1.9.0
	 */
	function __construct() {
		$widget_ops = array(
			'description'                 => __( 'A dynamic list of recently active, popular, and newest Friends of the displayed member.  Widget is only shown when viewing a member profile.', 'cirkle' ),
			'classname'                   => 'widget_bp_core_friends_widget buddypress',
			'customize_selective_refresh' => true,
		);
		parent::__construct( false, $name = _x( '(A4) Friends', 'widget name', 'buddypress' ), $widget_ops );

		if ( is_customize_preview() || is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.6.0
	 */
	public function enqueue_scripts() {
		$min = bp_core_get_minified_asset_suffix();
		wp_enqueue_script( 'bp_core_widget_friends-js', buddypress()->plugin_url . "bp-friends/js/widget-friends{$min}.js", array( 'jquery' ), bp_get_version() );
	}

	/**
	 * Display the widget.
	 *
	 * @since 1.9.0
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance The widget settings, as saved by the user.
	 */
	function widget( $args, $instance ) {
		global $members_template;

		extract( $args );

		if ( ! bp_displayed_user_id() ) {
			return;
		}

		$user_id = bp_displayed_user_id();
		$link = trailingslashit( bp_displayed_user_domain() . bp_get_friends_slug() );
		$instance['title'] = esc_html__( 'My Friends', 'cirkle' );

		if ( empty( $instance['friend_default'] ) ) {
			$instance['friend_default'] = 'active';
		}

		/**
		 * Filters the Friends widget title.
		 *
		 * @since 1.8.0
		 * @since 2.3.0 Added 'instance' and 'id_base' to arguments passed to filter.
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $before_widget;

		$title = $instance['link_title'] ? '<a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a>' : esc_html( $title );

		echo $before_title . $title . $after_title;

		$members_args = array(
			'user_id'         => absint( $user_id ),
			'type'            => sanitize_text_field( $instance['friend_default'] ),
			'max'             => absint( $instance['max_friends'] ),
			'populate_extras' => 1,
		);

		// Back up the global.
		$old_members_template = $members_template;

		?>

		<?php if ( bp_has_members( $members_args ) ) : ?>
			<ul id="friends-list" class="item-list cirkle-member-list">
				<?php while ( bp_members() ) : bp_the_member(); ?>
					<li class="vcard">
						<div class="item-avatar">
							<a href="<?php bp_member_permalink(); ?>" class="bp-tooltip" data-bp-tooltip="<?php bp_member_name(); ?>"><?php bp_member_avatar(); ?></a>
						</div>

						<div class="item">
							<div class="item-title fn"><a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></div>
							<div class="item-meta">
								<?php if ( 'newest' == $instance['friend_default'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_registered( array( 'relative' => false ) ) ); ?>"><?php bp_member_registered(); ?></span>
								<?php elseif ( 'active' == $instance['friend_default'] ) : ?>
									<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span>
								<?php else : ?>
									<span class="activity"><?php bp_member_total_friend_count(); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'bp_core_widget_friends', '_wpnonce-friends' ); ?>
			<input type="hidden" name="friends_widget_max" id="friends_widget_max" value="<?php echo absint( $instance['max_friends'] ); ?>" />

		<?php else: ?>

			<div class="widget-error">
				<?php _e( 'Sorry, no members were found.', 'cirkle' ); ?>
			</div>

		<?php endif; ?>

		<?php echo $after_widget;

		// Restore the global.
		$members_template = $old_members_template;
	}

	/**
	 * Process a widget save.
	 *
	 * @since 1.9.0
	 *
	 * @param array $new_instance The parameters saved by the user.
	 * @param array $old_instance The parameters as previously saved to the database.
	 * @return array $instance The processed settings to save.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['max_friends']    = absint( $new_instance['max_friends'] );
		$instance['friend_default'] = sanitize_text_field( $new_instance['friend_default'] );
		$instance['link_title']	    = ! empty( $new_instance['link_title'] );

		return $instance;
	}

	/**
	 * Render the widget edit form.
	 *
	 * @since 1.9.0
	 *
	 * @param array $instance The saved widget settings.
	 * @return void
	 */
	function form( $instance ) {
		$defaults = array(
			'max_friends' 	 => 5,
			'friend_default' => 'active',
			'link_title' 	 => false
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$max_friends 	= $instance['max_friends'];
		$friend_default = $instance['friend_default'];
		$link_title	= (bool) $instance['link_title'];
		?>

		<p><label for="<?php echo $this->get_field_id( 'link_title' ); ?>"><input type="checkbox" name="<?php echo $this->get_field_name('link_title'); ?>" id="<?php echo $this->get_field_id( 'link_title' ); ?>" value="1" <?php checked( $link_title ); ?> /> <?php _e( 'Link widget title to Members directory', 'cirkle' ); ?></label></p>

		<p><label for="<?php echo $this->get_field_id( 'max_friends' ); ?>"><?php _e( 'Max friends to show:', 'cirkle' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_friends' ); ?>" name="<?php echo $this->get_field_name( 'max_friends' ); ?>" type="text" value="<?php echo absint( $max_friends ); ?>" style="width: 30%" /></label></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'friend_default' ) ?>"><?php _e( 'Default friends to show:', 'cirkle' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'friend_default' ); ?>" id="<?php echo $this->get_field_id( 'friend_default' ); ?>">
				<option value="active" <?php selected( $friend_default, 'active' );?>><?php _e( 'Active', 'buddypress' ); ?></option>
				<option value="newest" <?php selected( $friend_default, 'newest' ); ?>><?php _e( 'Newest', 'buddypress' ); ?></option>
				<option value="popular"  <?php selected( $friend_default, 'popular' ); ?>><?php _e( 'Popular', 'buddypress' ); ?></option>
			</select>
		</p>

	<?php
	}
}