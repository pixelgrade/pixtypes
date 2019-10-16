<?php
/**
 * PixTypes.
 *
 * @package   PixTypes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      https://pixelgrade.com
 * @copyright 2013-2017 Pixelgrade
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
	protected $version;
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

	public $display_admin_menu = false;

	protected $config;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 *
	 * @param string $version
	 */
	protected function __construct( $version = '1.0.0' ) {
		$this->version = $version;
		$this->plugin_basepath = plugin_dir_path( __FILE__ );
		$this->config          = self::config();

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item only when is needed.
		if ( isset( $this->config['display_settings'] ) && $this->config['display_settings'] ) {
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

			// Add an action link pointing to the options page.
			$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'pixtypes.php' );
			add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		}

		// Load admin stylesheet and JavaScript files.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		add_action( 'plugins_loaded', array( $this, 'register_metaboxes' ), 14 );
		add_action( 'init', array( $this, 'register_entities' ), 99999 );

		// We need this later then the default 10 priority so we can have things happening between the init-10 and the PixTypes config
		add_action( 'init', array( $this, 'theme_version_check' ), 15 );

		/**
		 * Ajax Callbacks - only for logged in users
		 */
		add_action( 'wp_ajax_unset_pixtypes', array( &$this, 'ajax_unset_pixtypes' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @param string $version The current plugin version.
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance( $version = '1.0.0' ) {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self( $version );
		}

		return self::$instance;
	}

	public static function config() {
		// @TODO maybe check this
		return include 'plugin-config.php';
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		$config = self::config();

		/** get options defined by themes */
		$theme_types    = get_option( 'pixtypes_themes_settings' );
		$types_settings = get_option( $config['settings-key'] );
		$current_theme  = '_pixtypes_theme';

		// init settings
		if ( empty( $theme_types ) ) {
			$theme_types = array();
		}

		if ( empty( $types_settings ) ) {
			$types_settings = array( 'themes' => array() );
		}

		/** A pixelgrade theme will always have this class so we know we can import new settings **/
		if ( class_exists( 'wpgrade' ) ) {

			$current_theme = wpgrade::shortname() . $current_theme;
			// also inform the plugin about theme version
			$types_settings['wpgrade_theme_version'] = wpgrade::themeversion();

		} else {
			$theme_types = self::get_defaults( 'pixtypes' . $current_theme );
		}

		if ( ! empty( $theme_types ) ) {
			foreach ( $theme_types as $theme_key => $theme ) {
				$theme_name = str_replace( '_pixtypes_theme', '', $theme_key );
				/** Process each post type's arguments **/
				if ( $theme_key == $current_theme ) {

					/** POST TYPES slugs **/
					if ( ! empty( $theme_types[ $current_theme ]['post_types'] ) ) {
						foreach ( $theme_types[ $current_theme ]['post_types'] as $key => $post_type ) {
							$testable_slug = str_replace( $theme_name . '-', '', $post_type['rewrite']['slug'] );

							/** for our current theme we try to prioritize slugs */
							if ( isset( $post_type['rewrite'] ) && self::is_custom_post_type_slug_unique( $testable_slug ) ) {
								/** this slug is unique we can quit the theme suffix */
								$theme_types[ $current_theme ]['post_types'][ $key ]['rewrite']['slug'] = $testable_slug;
							}

							// process menu icon if it exists
							if ( isset( $post_type['menu_icon'] ) ) {
								// If we have been given a dashicon, use it without processing
								if ( false !== strpos( $post_type['menu_icon'], 'dashicon' ) ) {
									$theme_types[ $current_theme ]['post_types'][ $key ]['menu_icon'] =  $post_type['menu_icon'];
								} else {
									$theme_types[ $current_theme ]['post_types'][ $key ]['menu_icon'] = plugins_url( 'assets/' . $post_type['menu_icon'], __FILE__ );
								}
							}
						}
					}

					/** TAXONOMIES slugs **/
					if ( ! empty( $theme_types[ $current_theme ]['taxonomies'] ) ) {
						foreach ( $theme_types[ $current_theme ]['taxonomies'] as $key => $tax ) {
							$testable_slug = str_replace( $theme_name . '-', '', $tax['rewrite']['slug'] );
							if ( isset( $tax['rewrite'] ) && self::is_tax_slug_unique( $testable_slug ) ) {
								/** this slug is unique we can quit the theme suffix */
								$theme_types[ $current_theme ]['taxonomies'][ $key ]['rewrite']['slug'] = $testable_slug;
							}
						}
					}
				}
				$types_settings['themes'][ $theme_name ] = $theme_types[ $theme_key ];
			}
		}

		update_option( $config['settings-key'], $types_settings );

		/**
		 * http://wordpress.stackexchange.com/questions/36152/flush-rewrite-rules-not-working-on-plugin-deactivation-invalid-urls-not-showing
		 * nothing from above works in plugins so ...
		 */
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
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
		load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/lang/' );
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
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
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
			wp_localize_script( $this->plugin_slug . '-admin-script', 'locals',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
				)
			);
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			esc_html__( 'PixTypes', 'pixtypes' ),
			esc_html__( 'PixTypes', 'pixtypes' ),
			'manage_options',
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
		return array_merge( array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=pixtypes' ) . '">' . esc_html__( 'Settings', 'pixtypes' ) . '</a>' ), $links );
	}

	function register_entities() {
		// register post types
		$options = $updated_options  = apply_filters( 'pixtypes_settings_pre_register_entitites', get_option( 'pixtypes_settings' ) );
		$updated_options['display_settings'] = false;

		// go through each theme and activate portfolio post types
		if ( empty( $options['themes'] ) || ! array( $options['themes'] ) ) {
			return;
		}

		/** A pixelgrade theme will always have this class so we know we can import new settings **/
		$current_theme = false;
		if ( class_exists( 'wpgrade' ) ) {
			$current_theme = wpgrade::shortname();
		}

		$theme_types = $options['themes'];
		foreach ( $theme_types as $key => $theme ) {
			// post types
			if ( isset( $theme['post_types'] ) && is_array( $theme['post_types'] ) ) {
				// Remember what post types we have removed from settings so we can skip registering their taxonomies
				$deleted_post_types = array();
				foreach ( $theme['post_types'] as $post_type => $post_type_args ) {
					// First check if the post type is already registered - we bail if that is the case
					if ( post_type_exists( $post_type ) ) {
						continue;
					}

					// Second, for post types not belonging to the current theme, we only register them if there are posts
					if ( false !== $current_theme && $key != $current_theme ) {
						// We get the posts to see if there are any - we include all post statuses (trash and auto-draft need to be specified directly)
						$posts = get_posts( array( 'post_type' => $post_type, 'post_status' => array( 'any', 'trash', 'auto-draft' ), 'posts_per_page' => '1' ) );
						if ( empty( $posts ) ) {
							// We should also delete the post type from the settings - no worries; if the theme gets activated again, all this will come back
							// This is just for auto-cleanup
							unset( $updated_options['themes'][ $key ]['post_types'][ $post_type ] );
							$deleted_post_types[] = $post_type;
							continue;
						}
					}

					$is_jetpack_compatible = false;
					if ( strpos( $post_type, 'jetpack' ) !== false ) {
						///$xxxx = str_replace(  'jetpack-', '', $post_type);
						$is_jetpack_compatible = true;
					}

					if ( $is_jetpack_compatible ) {
						$post_type_key = strstr( $post_type, '-' );
						$post_type_key = substr( $post_type_key, 1 );
					} else {
						// eliminate the theme prefix
						$post_type_key = strstr( $post_type, '_' );
						$post_type_key = substr( $post_type_key, 1 );
					}

					// process menu icon if it exists
					if ( isset( $post_type_args['menu_icon'] ) ) {
						// If we have been given a dashicon or full URL, use it without processing
						if ( false === strpos( $post_type_args['menu_icon'], 'dashicon' ) && false === filter_var( $post_type_args['menu_icon'], FILTER_VALIDATE_URL ) ) {
							$post_type_args['menu_icon'] = plugins_url( 'assets/' . $post_type_args['menu_icon'], __FILE__ );
						}
					}

					if ( isset( $options[ 'enable_' . $post_type_key ] ) ) {
						$updated_options['display_settings'] = true;
						if ( $options[ 'enable_' . $post_type_key ] ) {
							register_post_type( $post_type, $post_type_args );
						}
					}
				}
			}

			// taxonomies
			if ( isset( $theme['taxonomies'] ) && is_array( $theme['taxonomies'] ) ) {
				foreach ( $theme['taxonomies'] as $tax => $tax_args ) {
					// First check if the taxonomy is already registered - we bail if that is the case
					if ( taxonomy_exists( $tax ) ) {
						continue;
					}

					// If we have deleted post types settings we need to skip registering their taxonomies
					if ( ! empty( $tax_args['post_types'] ) && ! empty( $deleted_post_types ) ) {
						if ( ! is_array( $tax_args['post_types'] ) ) {
							if ( in_array( $tax_args['post_types'], $deleted_post_types ) ) {
								continue;
							}
						} else {
							$temp = array_diff( $tax_args['post_types'], $deleted_post_types );
							if ( empty( $temp ) ) {
								continue;
							}
						}
					}

					$tax_post_types = $tax_args['post_types'];
					// remove "post_types", isn't a register_taxonomy argument we are just using it for post type linking
					unset( $tax_args['post_types'] );

					$is_jetpack_compatible = false;
					if ( strpos( $tax, 'jetpack' ) !== false ) {
						///$xxxx = str_replace(  'jetpack-', '', $tax);
						$is_jetpack_compatible = true;
					}

					if ( $is_jetpack_compatible ) {
						$tax_key = strstr( $tax, '-' );
						$tax_key = substr( $tax_key, 1 );
					} else {
						// eliminate the theme prefix
						$tax_key = strstr( $tax, '_' );
						$tax_key = substr( $tax_key, 1 );
					}

					if ( isset( $options[ 'enable_' . $tax_key ] ) ) {
						$updated_options['display_settings'] = true;
						if ( $options[ 'enable_' . $tax_key ] ) {
							register_taxonomy( $tax, $tax_post_types, $tax_args );
						}
					}
				}
			}
		}

		// Only update if we have actually changed something
		if ( $options != $updated_options ) {
			update_option( 'pixtypes_settings', $updated_options );
		}
	}

	function register_metaboxes() {
		require_once( $this->plugin_basepath . 'features/metaboxes/metaboxes.php' );
	}

	/**
	 * Check if this post_type's slug is unique
	 *
	 * @param $slug string
	 *
	 * @return boolean
	 */
	static function is_custom_post_type_slug_unique( $slug ) {

		global $wp_post_types;
		$is_unique = true;
		/** Suppose it's true */

		foreach ( $wp_post_types as $key => $post_type ) {
			$rewrite = $post_type->rewrite;
			/** if this post_type has a rewrite rule check for it */
			if ( ! empty( $rewrite ) && isset( $rewrite['slug'] ) && $slug == $rewrite['slug'] ) {
				$is_unique = false;
			} elseif ( $slug == $key ) {
				/** the post_type doesn't have a slug param, so the slug is the name itself */
				$is_unique = false;
			}
		}

		return $is_unique;
	}

	/**
	 * Check if this taxnonomie's slug is unique
	 *
	 * @param $slug string
	 *
	 * @return boolean
	 */
	static function is_tax_slug_unique( $slug ) {
		global $wp_taxonomies;

		/** Suppose it's true */
		$is_unique = true;

		foreach ( $wp_taxonomies as $key => $tax ) {
			$rewrite = $tax->rewrite;
			/** if this post_type has a rewrite rule check for it */
			if ( ! empty( $rewrite ) && isset( $rewrite['slug'] ) && $slug == $rewrite['slug'] ) {
				$is_unique = false;
			} elseif ( $slug == $key ) {
				/** the post_type doesn't have a slug param, so the slug is the name itself */
				$is_unique = false;
			}
		}

		return $is_unique;
	}

	static function get_defaults( $theme_key ) {

		$types_options                             = array();
		$types_options[ $theme_key ]               = array();
		$types_options[ $theme_key ]['post_types'] = array(
			'pix_portfolio' => array(
				'labels'        => array(
					'name'               => esc_html__( 'Project', 'pixtypes' ),
					'singular_name'      => esc_html__( 'Project', 'pixtypes' ),
					'add_new'            => esc_html__( 'Add New', 'pixtypes' ),
					'add_new_item'       => esc_html__( 'Add New Project', 'pixtypes' ),
					'edit_item'          => esc_html__( 'Edit Project', 'pixtypes' ),
					'new_item'           => esc_html__( 'New Project', 'pixtypes' ),
					'all_items'          => esc_html__( 'All Projects', 'pixtypes' ),
					'view_item'          => esc_html__( 'View Project', 'pixtypes' ),
					'search_items'       => esc_html__( 'Search Projects', 'pixtypes' ),
					'not_found'          => esc_html__( 'No Project found', 'pixtypes' ),
					'not_found_in_trash' => esc_html__( 'No Project found in Trash', 'pixtypes' ),
					'menu_name'          => esc_html__( 'Projects', 'pixtypes' ),
				),
				'public'        => true,
				'rewrite'       => array(
					'slug'       => 'portfolio',
					'with_front' => false,
				),
				'has_archive'   => 'portfolio-archive',
				'menu_icon'     => 'dashicons-portfolio',
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'excerpt' ),
				'yarpp_support' => true,
			),
			'pix_gallery'   => array(
				'labels'        => array(
					'name'               => esc_html__( 'Gallery', 'pixtypes' ),
					'singular_name'      => esc_html__( 'Gallery', 'pixtypes' ),
					'add_new'            => esc_html__( 'Add New', 'pixtypes' ),
					'add_new_item'       => esc_html__( 'Add New Gallery', 'pixtypes' ),
					'edit_item'          => esc_html__( 'Edit Gallery', 'pixtypes' ),
					'new_item'           => esc_html__( 'New Gallery', 'pixtypes' ),
					'all_items'          => esc_html__( 'All Galleries', 'pixtypes' ),
					'view_item'          => esc_html__( 'View Gallery', 'pixtypes' ),
					'search_items'       => esc_html__( 'Search Galleries', 'pixtypes' ),
					'not_found'          => esc_html__( 'No Gallery found', 'pixtypes' ),
					'not_found_in_trash' => esc_html__( 'No Gallery found in Trash', 'pixtypes' ),
					'menu_name'          => esc_html__( 'Galleries', 'pixtypes' ),
				),
				'public'        => true,
				'rewrite'       => array(
					'slug'       => 'galleries',
					'with_front' => false,
				),
				'has_archive'   => 'galleries-archive',
				'menu_position' => null,
				'menu_icon'     => 'dashicons-format-gallery',
				'supports'      => array( 'title', 'thumbnail', 'page-attributes', 'excerpt' ),
				'yarpp_support' => true,
			),
		);

		/** TAXONOMIES **/
		$types_options[ $theme_key ]['taxonomies'] = array(
			'pix_portfolio_categories' => array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => esc_html__( 'Portfolio Categories', 'pixtypes' ),
					'singular_name'     => esc_html__( 'Portfolio Category', 'pixtypes' ),
					'search_items'      => esc_html__( 'Search Portfolio Category', 'pixtypes' ),
					'all_items'         => esc_html__( 'All Portfolio Categories', 'pixtypes' ),
					'parent_item'       => esc_html__( 'Parent Portfolio Category', 'pixtypes' ),
					'parent_item_colon' => esc_html__( 'Parent Portfolio Category: ', 'pixtypes' ),
					'edit_item'         => esc_html__( 'Edit Portfolio Category', 'pixtypes' ),
					'update_item'       => esc_html__( 'Update Portfolio Category', 'pixtypes' ),
					'add_new_item'      => esc_html__( 'Add New Portfolio Category', 'pixtypes' ),
					'new_item_name'     => esc_html__( 'New Portfolio Category Name', 'pixtypes' ),
					'menu_name'         => esc_html__( 'Portfolio Categories', 'pixtypes' ),
				),
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'portfolio-category', 'with_front' => false ),
				'sort'              => true,
				'post_types'        => array( 'pix_portfolio' )
			),
			'pix_gallery_categories'   => array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => esc_html__( 'Gallery Categories', 'pixtypes' ),
					'singular_name'     => esc_html__( 'Gallery Category', 'pixtypes' ),
					'search_items'      => esc_html__( 'Search Gallery Category', 'pixtypes' ),
					'all_items'         => esc_html__( 'All Gallery Categories', 'pixtypes' ),
					'parent_item'       => esc_html__( 'Parent Gallery Category', 'pixtypes' ),
					'parent_item_colon' => esc_html__( 'Parent Gallery Category: ', 'pixtypes' ),
					'edit_item'         => esc_html__( 'Edit Gallery Category', 'pixtypes' ),
					'update_item'       => esc_html__( 'Update Gallery Category', 'pixtypes' ),
					'add_new_item'      => esc_html__( 'Add New Gallery Category', 'pixtypes' ),
					'new_item_name'     => esc_html__( 'New Gallery Category Name', 'pixtypes' ),
					'menu_name'         => esc_html__( 'Gallery Categories', 'pixtypes' ),
				),
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'gallery-category', 'with_front' => false ),
				'sort'              => true,
				'post_types'        => array( 'pix_gallery' )
			),
		);

		/** METABOXES **/
		$types_options[ $theme_key ]['metaboxes'] = array(
			'pix_portfolio' => array(
				'id'         => 'portfolio_gallery',
				'title'      => esc_html__( 'Gallery', 'pixtypes' ),
				'pages'      => array( 'pix_portfolio' ), // Post type
				'context'    => 'normal',
				'priority'   => 'high',
				'show_names' => true, // Show field names on the left
				'fields'     => array(
					array(
						'name' => esc_html__( 'Images', 'pixtypes' ),
						'id'   => 'pix_portfolio_gallery',
						'type' => 'gallery',
					)
				)
			),
			'pix_gallery'   => array(
				'id'         => 'pix_gallery',
				'title'      => esc_html__( 'Gallery', 'pixtypes' ),
				'pages'      => array( 'pix_gallery' ), // Post type
				'context'    => 'normal',
				'priority'   => 'high',
				'show_names' => true, // Show field names on the left
				'fields'     => array(
					array(
						'name' => esc_html__( 'Images', 'pixtypes' ),
						'id'   => 'pix_main_gallery',
						'type' => 'gallery',
					)
				)
			)
		);

		return $types_options;
	}

	/**
	 * Ajax callback for cleaning up the settings for a theme
	 */
	function ajax_unset_pixtypes() {
		$result = array( 'success' => false, 'msg' => 'Incorrect nonce' );
		if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'unset_pixtype' ) ) {
			echo json_encode( $result );
			die();
		}

		if ( isset( $_POST['theme_slug'] ) ) {
			$key     = $_POST['theme_slug'];
			$options = get_option( 'pixtypes_settings' );
			if ( isset( $options['themes'][ $key ] ) ) {
				unset( $options['themes'][ $key ] );
				update_option( 'pixtypes_settings', $options );
				$result['msg']     = 'Settings for ' . ucfirst( $key ) . ' have been cleaned up!';
				$result['success'] = true;
			}
		}

		echo json_encode( $result );
		exit;
	}

	/**
	 * On every wpgrade themes update we need to reconvert theme options into plugin options
	 */
	function theme_version_check() {
		if ( class_exists( 'wpgrade' ) ) {
			// Each theme should have it's pixtypes config theme version saved
			$options = get_option( 'pixtypes_settings' );

			// Make sure that we fix things just in case
			if ( ! isset( $options['wpgrade_theme_version'] ) ) {
				$options['wpgrade_theme_version'] = '0.0.1';
			}

			if ( version_compare( wpgrade::themeversion(), $options['wpgrade_theme_version'], '!=' ) ) {
				// the plugin will copy these options into it's own field
				self::activate( false );
				// and finally merge user's settings with the theme ones
				save_pixtypes_settings( $options );
			}
		}
	}

	function get_plugin_version() {
		return $this->version;
	}
}
