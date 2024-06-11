<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/admin
 * @author     Wbcom Designs <admin@gmail.com>
 */
class Bp_Xprofile_Export_Import_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bp_Xprofile_Export_Import_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bp_Xprofile_Export_Import_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-xprofile-export-import-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bp_Xprofile_Export_Import_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bp_Xprofile_Export_Import_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-xprofile-export-import-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'bpxp_ajax_url',
			array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'bpxp_ajax_request' ),
			)
		);
	}

	/**
	 * Register admin menu page for plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_admin_menu() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bp_Xprofile_Export_Import_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bp_Xprofile_Export_Import_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'bp-xprofile-export-import' ), esc_html__( 'WB Plugins', 'bp-xprofile-export-import' ), 'manage_options', 'wbcomplugins', array( $this, 'bpxp_member_export_import_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'bp-xprofile-export-import' ), esc_html__( 'General', 'bp-xprofile-export-import' ), 'manage_options', 'wbcomplugins' );
		}

		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Member Export Import Setting Page', 'bp-xprofile-export-import' ), esc_html__( 'BP Member Export Import', 'bp-xprofile-export-import' ), 'manage_options', 'bpxp-member-export-import', array( $this, 'bpxp_member_export_import_settings_page' ) );

	}


	/**
	 * Callback function for bp member xprofile export import settings page.
	 *
	 * @since    1.0.0
	 */
	public function bpxp_member_export_import_settings_page() {
		$current = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';
		?>
		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
			<div id="wb_admin_logo">
				<a href="https://wbcomdesigns.com/downloads/buddypress-community-bundle/" target="_blank">
					<img src="<?php echo esc_url( BPXP_PLUGIN_URL ) . 'admin/wbcom/assets/imgs/wbcom-offer-notice.png'; ?>">
				</a>
			</div>
		</div>
		<div class="wbcom-wrap wbcom-plugin-wrapper">
			<div class="blpro-header">
						<div class="wbcom_admin_header-wrapper">
							<div id="wb_admin_plugin_name">
								<?php esc_html_e( 'BuddyPress Member Export Import', 'bp-xprofile-export-import' ); ?>
								<span><?php printf( __( 'Version %s', 'bp-xprofile-export-import' ), BPXP_PLUGIN_VERSION ); ?></span>
							</div>
							<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
						</div>
				</div>
		<div class="wbcom-admin-settings-page">
		<?php
		$bpxp_tabs = array(
			'welcome'        => __( 'Welcome', 'bp-xprofile-export-import' ),
			'members_export' => __( 'Members Export', 'bp-xprofile-export-import' ),
			'members_import' => __( 'Members Import', 'bp-xprofile-export-import' ),
		);

		$tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $bpxp_tabs as $bpxp_tab => $bpxp_name ) {
			$class     = ( $bpxp_tab == $current ) ? 'nav-tab-active' : '';
			$tab_html .= '<li class="' . $bpxp_tab . '"><a class="nav-tab ' . $class . '" href="admin.php?page=bpxp-member-export-import&tab=' . $bpxp_tab . '">' . $bpxp_name . '</a></li>';
		}
		$tab_html .= '</div></ul></div>';
		echo wp_kses_post( $tab_html );
		$this->bpxp_plugin_option_pages();
		echo '</div>'; /* closing of div class wbcom-admin-settings-page */
		echo '</div>'; /* closing div class wbcom-wrap */
		echo '</div>'; /* closing div class wrap */
	}

	/**
	 * Get desired options page file at admin settings end.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_plugin_option_pages() {
			$bpxp_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'welcome';

		$this->bpxp_include_admin_setting_tabs( $bpxp_tab );
	}

	/**
	 * Include setting template.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 * @param bpxp_tab $bpxp_tab bpxp_tab.
	 */
	public function bpxp_include_admin_setting_tabs( $bpxp_tab ) {
		switch ( $bpxp_tab ) {
			case 'welcome':
				$this->bpxp_welcome_page();
				break;
			case 'members_export':
				$this->bpxp_export_setting_page();
				break;
			case 'members_import':
				$this->bpxp_import_setting_page();
				break;
			default:
				$this->bpxp_welcome_page();
				break;
		}
	}

	/**
	 * Bpxp_welcome_page
	 *
	 * @return void
	 */
	public function bpxp_welcome_page() {
		include BPXP_PLUGIN_PATH . 'admin/partials/bp-welcome-page.php';
	}


	/**
	 * Display Admin menu page export into CSV fields
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_export_setting_page() {
		include BPXP_PLUGIN_PATH . 'admin/partials/bp-xprofile-export-admin-display.php';
	}

	/**
	 * Display Admin menu subpage content.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	public function bpxp_import_setting_page() {
		include BPXP_PLUGIN_PATH . 'admin/partials/bp-xprofile-import-admin-display.php';
	}

	/**
	 * Hide all notices from the setting page.
	 *
	 * @return void
	 */
	public function bpxp_export_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'bpxp-member-export-import' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}

	}
}
