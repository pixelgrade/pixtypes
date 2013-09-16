<?php
/**
 * PixTypes.
 *
 * @package   PixTypes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2013 Pixelgrade
 */

/**
 * Plugin class.
 *
 * @package PixTypes
 * @author    Pixelgrade <contact@pixelgrade.com>
 */
class PixTypesPlugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @const   string
	 */
	protected $version = '1.0.0';
	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'pixtypes';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Path to the plugin.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_basepath = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	protected function __construct() {

		$this->plugin_basepath = plugin_dir_path( __FILE__ );

		// Load plugin text domain
//		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		 add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		 $plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'pixtypes.php' );
		 add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
//		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
//		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'init', array( $this, 'register_entities'), 99999);
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		$config = include 'plugin-config.php';

		ob_start();
		/** get options defined by themes */
		$theme_types = get_option('pixtypes_themes_settings');
		$types_settings = get_option($config['settings-key']);
		$current_theme = '_pixtypes_theme';

		// init settings
		if ( empty($theme_types) ) {
			$theme_types = array();
		}

		if ( empty($types_settings) ) {
			$types_settings = array('themes' => array());
		}

		/** A pixelgrade theme will always have this class so we know we can import new settings **/
		if (class_exists('wpgrade') ) $current_theme = wpgrade::shortname() . $current_theme;

		if ( !empty($theme_types) ) {
			foreach ( $theme_types as $theme_key => $theme) {
				$theme_name = str_replace('_pixtypes_theme', '', $theme_key);
				/** for our current theme we try to prioritize slugs */
				if ( $theme_key == $current_theme ) {

					/** POST TYPES slugs **/
					if (!empty( $theme_types[$current_theme]['post_types']) ){
						foreach ( $theme_types[$current_theme]['post_types'] as $key => $post_type ) {
							$testable_slug = $is_slug_unique = '';
							$testable_slug = str_replace ( $theme_name.'-', '', $post_type["rewrite"]["slug"]);
							if ( isset( $post_type["rewrite"] ) && self::is_custom_post_type_slug_unique($testable_slug) ) {
								/** this slug is unique we can quit the theme suffix */
								$theme_types[$current_theme]['post_types'][$key]["rewrite"]["slug"] = $testable_slug;
							}
						}
					}

					/** TAXONOMIES slugs **/
					if (!empty( $theme_types[$current_theme]['taxonomies'] ) ) {
						foreach ( $theme_types[$current_theme]['taxonomies'] as $key => $tax ) {
							$testable_slug = $is_slug_unique = '';
							$testable_slug = str_replace ( $theme_name.'-', '', $tax["rewrite"]["slug"]);
							if ( isset( $tax["rewrite"] ) && self::is_tax_slug_unique($testable_slug) ) {
								/** this slug is unique we can quit the theme suffix */
								$theme_types[$current_theme]['taxonomies'][$key]["rewrite"]["slug"] = $testable_slug;
							}
						}
					}
				}
				$types_settings['themes'][$theme_name] = $theme_types[$theme_key];
			}
		}

		update_option($config['settings-key'], $types_settings);

		$debug = ob_get_clean();
	}

	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0.0
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'PixTypes', $this->plugin_slug ),
			__( 'PixTypes', $this->plugin_slug ),
			'update_core',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 */
	function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 */
	function add_action_links( $links ) {
		return array_merge( array( 'settings' => '<a href="' . admin_url( 'plugins.php?page=pixtypes' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>' ), $links );
	}

	function register_entities(){
		require_once( $this->plugin_basepath . 'features/custom-entities.php' );
		require_once( $this->plugin_basepath . 'features/metaboxes/metaboxes.php' );
	}

	/**
	 * Check if this post_type's slug is unique
	 * @param $slug string
	 * @return boolean
	 */
	static function is_custom_post_type_slug_unique( $slug ){

		global $wp_post_types;
		$is_unique = true; /** Suppose it's true */

		foreach ( $wp_post_types as $key => $post_type){
			$rewrite = $post_type->rewrite;
			/** if this post_type has a rewrite rule check for it */
			if ( !empty( $rewrite ) && isset($rewrite["slug"]) && $slug == $rewrite["slug"] ){
				$is_unique = false;
			} elseif ( $slug == $key ) { /** the post_type doesn't have a slug param, so the slug is the name itself */
				$is_unique = false;
			}
		}

		return $is_unique;
	}

	/**
	 * Check if this taxnonomie's slug is unique
	 * @param $slug string
	 * @return boolean
	 */
	static function is_tax_slug_unique( $slug ){

		global $wp_taxonomies;
		$is_unique = true; /** Suppose it's true */

		foreach ( $wp_taxonomies as $key => $tax){
			$rewrite = $tax->rewrite;
			/** if this post_type has a rewrite rule check for it */
			if ( !empty( $rewrite ) && isset($rewrite["slug"]) && $slug == $rewrite["slug"] ){
				$is_unique = false;
			} elseif ( $slug == $key ) { /** the post_type doesn't have a slug param, so the slug is the name itself */
				$is_unique = false;
			}
		}

		return $is_unique;
	}
}