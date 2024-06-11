<?php

/**
 * SocialV\Utility\Styles\Component class
 *
 * @package socialv
 */

namespace SocialV\Utility\Styles;

use SocialV\Utility\Component_Interface;
use SocialV\Utility\Templating_Component_Interface;
use function SocialV\Utility\socialv;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function wp_register_style;
use function wp_style_add_data;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_styles;
use function add_editor_style;
use function wp_style_is;
use function _doing_it_wrong;
use function wp_print_styles;
use function post_password_required;
use function is_singular;
use function comments_open;
use function get_comments_number;
use function apply_filters;
use function add_query_arg;

/**
 * Class for managing stylesheets.
 *
 * Exposes template tags:
 * * `socialv()->print_styles()`
 */
class Component implements Component_Interface, Templating_Component_Interface
{

	/**
	 * Associative array of CSS files, as $handle => $data pairs.
	 * $data must be an array with keys 'file' (file path relative to 'assets/css' directory), and optionally 'global'
	 * (whether the file should immediately be enqueued instead of just being registered) and 'preload_callback'
	 * (callback function determining whether the file should be preloaded for the current request).
	 *
	 * Do not access this property directly, instead use the `get_css_files()` method.
	 *
	 * @var array
	 */
	protected $css_files;

	/**
	 * Associative array of Google Fonts to load, as $font_name => $font_variants pairs.
	 *
	 * Do not access this property directly, instead use the `get_google_fonts()` method.
	 *
	 * @var array
	 */
	protected $google_fonts;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string
	{
		return 'styles';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize()
	{
		add_action('wp_enqueue_scripts', [$this, 'action_enqueue_styles']);
		add_action('wp_head', [$this, 'action_preload_styles']);
		add_action('after_setup_theme', [$this, 'action_add_editor_styles']);
		add_filter('wp_resource_hints', [$this, 'filter_resource_hints'], 10, 2);
	}
	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `socialv()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags(): array
	{
		return array(
			'print_styles' => array($this, 'print_styles'),
		);
	}

	/**
	 * Registers or enqueues stylesheets.
	 *
	 * Stylesheets that are global are enqueued. All other stylesheets are only registered, to be enqueued later.
	 */
	public function action_enqueue_styles()
	{
		// Leanpress COurse Review Plugin 
		if (class_exists('LP_Addon_Course_Review')) {
			wp_dequeue_style('course-review');
			wp_enqueue_style('course-review', LP_ADDON_COURSE_REVIEW_URL . '/assets/css/course-review.css');
		}
		
		// Enqueue Google Fonts.
		$google_fonts_url = $this->get_google_fonts_url();
		if (!empty($google_fonts_url)) {
			wp_enqueue_style('socialv-fonts', $google_fonts_url, array(), null); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		}

		$css_uri = get_template_directory_uri() . '/assets/css/';
		$css_dir = get_template_directory() . '/assets/css/';
		$preloading_styles_enabled = $this->preloading_styles_enabled();

		$css_files = $this->get_css_files();
		foreach ($css_files as $handle => $data) {
			$src     = $css_uri . $data['file'];
			$version = socialv()->get_asset_version($css_dir . $data['file']);

			/*
			 * Enqueue global stylesheets immediately and register the other ones for later use
			 * (unless preloading stylesheets is disabled, in which case stylesheets should be immediately
			 * enqueued based on whether they are necessary for the page content).
			 */
			if ($data['global'] || !$preloading_styles_enabled && is_callable($data['preload_callback']) && call_user_func($data['preload_callback'])) {
				wp_enqueue_style($handle, $src, array(), $version, $data['media']);
			} else {
				wp_register_style($handle, $src, array(), $version, $data['media']);
			}

			wp_style_add_data($handle, 'precache', true);
		}
	}

	/**
	 * Preloads in-body stylesheets depending on what templates are being used.
	 *
	 * Only stylesheets that have a 'preload_callback' provided will be considered. If that callback evaluates to true
	 * for the current request, the stylesheet will be preloaded.
	 *
	 * Preloading is disabled when AMP is active, as AMP injects the stylesheets inline.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Preloading_content
	 */
	public function action_preload_styles()
	{
		// If preloading styles is disabled, return early.
		if (!$this->preloading_styles_enabled()) {
			return;
		}

		$wp_styles = wp_styles();

		$css_files = $this->get_css_files();
		foreach ($css_files as $handle => $data) {

			// Skip if stylesheet not registered.
			if (!isset($wp_styles->registered[$handle])) {
				continue;
			}

			// Skip if no preload callback provided.
			if (!is_callable($data['preload_callback'])) {
				continue;
			}

			// Skip if preloading is not necessary for this request.
			if (!call_user_func($data['preload_callback'])) {
				continue;
			}

			$preload_uri = $wp_styles->registered[$handle]->src . '?ver=' . $wp_styles->registered[$handle]->ver;

			echo '<link rel="preload" id="' . esc_attr($handle) . '-preload" href="' . esc_url($preload_uri) . '" as="style">';
			echo "\n";
		}
	}

	/**
	 * Enqueues WordPress theme styles for the editor.
	 */
	public function action_add_editor_styles()
	{

		// Enqueue Google Fonts.
		$google_fonts_url = $this->get_google_fonts_url();
		if (!empty($google_fonts_url)) {
			add_editor_style($this->get_google_fonts_url());
		}

		// Enqueue block editor stylesheet.
		add_editor_style('assets/css/editor/editor-styles.min.css');
	}

	/**
	 * Adds preconnect resource hint for Google Fonts.
	 *
	 * @param array  $urls          URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed.
	 * @return array URLs to print for resource hints.
	 */
	public function filter_resource_hints(array $urls, string $relation_type): array
	{
		if ('preconnect' === $relation_type && wp_style_is('socialv-fonts', 'queue')) {
			$urls[] = array(
				'href' => 'https://fonts.gstatic.com',
				'crossorigin',
			);
		}

		return $urls;
	}

	/**
	 * Prints stylesheet link tags directly.
	 *
	 * This should be used for stylesheets that aren't global and thus should only be loaded if the HTML markup
	 * they are responsible for is actually present. Template parts should use this method when the related markup
	 * requires a specific stylesheet to be loaded. If preloading stylesheets is disabled, this method will not do
	 * anything.
	 *
	 * If the `<link>` tag for a given stylesheet has already been printed, it will be skipped.
	 *
	 * @param string ...$handles One or more stylesheet handles.
	 */
	public function print_styles(string ...$handles)
	{

		// If preloading styles is disabled (and thus they have already been enqueued), return early.
		if (!$this->preloading_styles_enabled()) {
			return;
		}

		$css_files = $this->get_css_files();
		$handles   = array_filter(
			$handles,
			function ($handle) use ($css_files) {
				$is_valid = isset($css_files[$handle]) && !$css_files[$handle]['global'];
				if (!$is_valid) {
					/* translators: %s: stylesheet handle */
					_doing_it_wrong(__CLASS__ . '::print_styles()', esc_html(sprintf(__('Invalid theme stylesheet handle: %s', 'socialv'), $handle)), 'SocialV 2.0.0');
				}
				return $is_valid;
			}
		);

		if (empty($handles)) {
			return;
		}
		wp_print_styles($handles);
	}

	/**
	 * Determines whether to preload stylesheets and inject their link tags directly within the page content.
	 *
	 * Using this technique generally improves performance, however may not be preferred under certain circumstances.
	 * For example, since AMP will include all style rules directly in the head, it must not be used in that context.
	 * By default, this method returns true unless the page is being served in AMP. The
	 * {@see 'socialv_preloading_styles_enabled'} filter can be used to tweak the return value.
	 *
	 * @return bool True if preloading stylesheets and injecting them is enabled, false otherwise.
	 */
	protected function preloading_styles_enabled()
	{
		$preloading_styles_enabled = !socialv()->is_amp();

		/**
		 * Filters whether to preload stylesheets and inject their link tags within the page content.
		 *
		 * @param bool $preloading_styles_enabled Whether preloading stylesheets and injecting them is enabled.
		 */
		return apply_filters('socialv_preloading_styles_enabled', $preloading_styles_enabled);
	}

	/**
	 * Gets all CSS files.
	 *
	 * @return array Associative array of $handle => $data pairs.
	 */
	protected function get_css_files(): array
	{
		if (is_array($this->css_files)) {
			return $this->css_files;
		}

		$css_files = array(
			'bootstrap'			=> array(
				'file'   => 'vendor/bootstrap.min.css',
				'global' => true,
			),
			'iconly-bulk'			=> array(
				'file'   => 'vendor/iconly/css/bulk-style.css',
				'global' => true,
			),
			'iconly'			=> array(
				'file'   => 'vendor/iconly/css/style.css',
				'global' => true,
			),
			'custom-iconly'			=> array(
				'file'   => 'vendor/custom-icons/iconly.css',
				'global' => true,
			),
			'select2'			=> array(
				'file'   => 'vendor/select2.css',
				'global' => true,
			),
			'swiper-slider'			=> array(
				'file'   => 'vendor/swiper-bundle.min.css',
				'global' => true,
			),
			'socialv-global'     => array(
				'file'   => 'global.min.css',
				'global' => true,
			),
			'socialv-dummy'      => array(
				'file'   => 'dummy.min.css',
				'preload_callback' => '__return_true',
				'global' => true,
			),
			'socialv-button'     => array(
				'file'   => 'button.min.css',
				'global' => true,
			),
			'socialv-comments'   => array(
				'file'             => 'comments.min.css',
				'preload_callback' => function () {
					return !post_password_required() && is_singular() && (comments_open() || get_comments_number());
				},
			),
			'socialv-sidebar'    => array(
				'file'             => 'sidebar.min.css',
				'preload_callback' => function () {
					return socialv()->is_primary_sidebar_active();
				},
				'global' => true,
			),
			'socialv-widgets'    => array(
				'file'             => 'widgets.min.css',
				'preload_callback' => function () {
					return socialv()->is_primary_sidebar_active();
				},
				'global' => true,
			),
			'socialv-woocommerce' => array(
				'file'   => 'woocommerce.min.css',
				'global' => true,
			),
			'socialv-dark'     => array(
				'file'   => 'dark.min.css',
				'global' => true,
			),
			'socialv-rtl'     => array(
				'file'   => 'rtl.min.css',
				'global' => true,
			),
		);

		if (class_exists('Wpstory_Premium')) {
			$bp_files = array(
				'socialv-sotry'     => array(
					'file'   => 'story.min.css',
					'global' => true,
				),
			);
			$css_files =  array_merge($css_files, $bp_files);
		}
		if (function_exists('buddypress')) {
			$bp_files = array(
				'socialv-buddypress'     => array(
					'file'   => 'socialv-buddypress.min.css',
					'global' => true,
				),
			);
			$css_files =  array_merge($css_files, $bp_files);
		}
		if (class_exists('LearnPress')) {
			$bp_files = array(
				'socialv-learnpress'     => array(
					'file'   => 'socialv-learnpress.min.css',
					'global' => true,
				),
			);
			$css_files =  array_merge($css_files, $bp_files);
		}
		if (class_exists('LP_Addon_Course_Review')) {
			$bp_files = array(
				'socialv-learnpress-review'     => array(
					'file'   => 'socialv-learnpress-review.min.css',
					'global' => true,
				),
			);
			$css_files =  array_merge($css_files, $bp_files);
		}
		if (class_exists( 'PMPro_Membership_Level' )) {
			$bp_files = array(
				'socialv-pmpro'     => array(
					'file'   => 'socialv-pmpro.min.css',
					'global' => true,
				),
			);
			$css_files =  array_merge($css_files, $bp_files);
		}
		/**
		 * Filters default CSS files.
		 *
		 * @param array $css_files Associative array of CSS files, as $handle => $data pairs.
		 *                         $data must be an array with keys 'file' (file path relative to 'assets/css'
		 *                         directory), and optionally 'global' (whether the file should immediately be
		 *                         enqueued instead of just being registered) and 'preload_callback' (callback)
		 *                         function determining whether the file should be preloaded for the current request).
		 */
		$css_files = apply_filters('socialv_css_files', $css_files);

		$this->css_files = array();
		foreach ($css_files as $handle => $data) {
			if (is_string($data)) {
				$data = array('file' => $data);
			}

			if (empty($data['file'])) {
				continue;
			}

			$this->css_files[$handle] = array_merge(
				array(
					'global'           => false,
					'preload_callback' => null,
					'media'            => 'all',
				),
				$data
			);
		}

		return $this->css_files;
	}

	/**
	 * Returns Google Fonts used in theme.
	 *
	 * @return array Associative array of $font_name => $font_variants pairs.
	 */
	protected function get_google_fonts(): array
	{
		if (is_array($this->google_fonts)) {
			return $this->google_fonts;
		}

		$google_fonts = array(
			'Plus Jakarta Sans' => array('400', '400i', '500', '600', '700'),
		);

		/**
		 * Filters default Google Fonts.
		 *
		 * @param array $google_fonts Associative array of $font_name => $font_variants pairs.
		 */
		$this->google_fonts = (array) apply_filters('socialv_google_fonts', $google_fonts);

		return $this->google_fonts;
	}

	/**
	 * Returns the Google Fonts URL to use for enqueuing Google Fonts CSS.
	 *
	 * Uses `latin` subset by default. To use other subsets, add a `subset` key to $query_args and the desired value.
	 *
	 * @return string Google Fonts URL, or empty string if no Google Fonts should be used.
	 */
	protected function get_google_fonts_url(): string
	{
		$google_fonts = $this->get_google_fonts();

		if (empty($google_fonts)) {
			return '';
		}

		$font_families = array();

		foreach ($google_fonts as $font_name => $font_variants) {
			if (!empty($font_variants)) {
				if (!is_array($font_variants)) {
					$font_variants = explode(',', str_replace(' ', '', $font_variants));
				}

				$font_families[] = $font_name . ':' . implode(',', $font_variants);
				continue;
			}

			$font_families[] = $font_name;
		}

		$query_args = array(
			'family'  => implode('%7C', $font_families),
			'display' => 'swap',
		);

		return add_query_arg($query_args, 'https://fonts.googleapis.com/css');
	}
}
