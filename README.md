=== PixTypes === [![Build Status](https://travis-ci.org/pixelgrade/pixtypes.svg?branch=update)](https://travis-ci.org/pixelgrade/pixtypes)

WordPress plugin for managing custom post types and custom meta boxes.

The main idea of this plugin is to allow a WordPress theme to define what custom post-types or metaboxes are needed for that theme.

=== <a name="pixytpes_config">#Configuration</a> ===

Note: We still have to add things in this documentation.

The PixTypes plugin is taking configurations from the `pixtypes_themes_settings` option.

All we have to do is to add our settings in this option when the theme gets active, so we need to use the [after_switch_theme](http://codex.wordpress.org/Plugin_API/Action_Reference/after_switch_theme) filter.

Here is a small example, which adds a portfolio post type, a portfolio taxonomy and some custom metaboxes for a contact page template(kinda fictive, I know).

```
function theme_getting_active () {

	// first get the old settings if there are ones.
	$types_options = get_option( 'pixtypes_themes_settings' );
	if ( empty( $types_options ) ) {
		$types_options = array();
	}

	// now add your settings
	$types_options[ 'theme_name' ] = array(
		'first_activation' => true,
		'post_types' => array(
			'theme_name_portfolio' => array(
				'labels'        => array(
					'name'               => esc_html__( 'Project', 'theme_name_txtd' ),
					'singular_name'      => esc_html__( 'Project', 'theme_name_txtd' ),
					'add_new'            => esc_html__( 'Add New', 'theme_name_txtd' ),
					'add_new_item'       => esc_html__( 'Add New Project', 'theme_name_txtd' ),
					'edit_item'          => esc_html__( 'Edit Project', 'theme_name_txtd' ),
					'new_item'           => esc_html__( 'New Project', 'theme_name_txtd' ),
					'all_items'          => esc_html__( 'All Projects', 'theme_name_txtd' ),
					'view_item'          => esc_html__( 'View Project', 'theme_name_txtd' ),
					'search_items'       => esc_html__( 'Search Projects', 'theme_name_txtd' ),
					'not_found'          => esc_html__( 'No Project found', 'theme_name_txtd' ),
					'not_found_in_trash' => esc_html__( 'No Project found in Trash', 'theme_name_txtd' ),
					'menu_name'          => esc_html__( 'Projects', 'theme_name_txtd' ),
				),
				'public'        => true,
				'rewrite'       => array(
					'slug'       => 'theme_name_portfolio',
					'with_front' => false,
				),
				'has_archive'   => 'portfolio-archive',
				'menu_icon'     => 'report.png',
				'menu_position' => null,
				'hierarchical' => true,
				'supports'      => array(
					'title',
					'editor',
					'page-attributes',
					'thumbnail',
				),
				'yarpp_support' => true,
			)
		),
		'taxonomies' => array(
			'theme_name_portfolio_categories' => array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => esc_html__( 'Project Categories', 'theme_name_txtd' ),
					'singular_name'     => esc_html__( 'Project Category', 'theme_name_txtd' ),
					'search_items'      => esc_html__( 'Search Project Categories', 'theme_name_txtd' ),
					'all_items'         => esc_html__( 'All Project Categories', 'theme_name_txtd' ),
					'parent_item'       => esc_html__( 'Parent Project Category', 'theme_name_txtd' ),
					'parent_item_colon' => esc_html__( 'Parent Project Category: ', 'theme_name_txtd' ),
					'edit_item'         => esc_html__( 'Edit Project Category', 'theme_name_txtd' ),
					'update_item'       => esc_html__( 'Update Project Category', 'theme_name_txtd' ),
					'add_new_item'      => esc_html__( 'Add New Project Category', 'theme_name_txtd' ),
					'new_item_name'     => esc_html__( 'New Project Category Name', 'theme_name_txtd' ),
					'menu_name'         => esc_html__( 'Portfolio Categories', 'theme_name_txtd' ),
				),
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'portfolio-category', 'with_front' => false ),
				'sort'              => true,
				'post_types'        => array( 'theme_name_portfolio' )
			),
		),
		'metaboxes' => array(
			//for the Contact Page template
			'_gmap_settings' => array(
				'id'         => '_gmap_settings',
				'title'      => esc_html__( 'Map Coordinates & Display Options', 'theme_name_txtd' ),
				'pages'      => array( 'page' ), // Post type
				'context'    => 'normal',
				'priority'   => 'high',
				'hidden'     => true,
				'show_on'    => array(
					'key' => 'page-template',
					'value' => array( 'page-templates-contact.php' ),
				),
				'show_names' => true, // Show field names on the left
				'fields'     => array(
					array(
						'name' => esc_html__( 'Map Height', 'theme_name_txtd' ),
						'desc' => __( '<p class="cmb_metabox_description">Select the height of the Google Map area in relation to the browser window.</p>', 'theme_name_txtd' ),
						'id'   => 'page_gmap_height',
						'type'    => 'select',
						'options' => array(
							array(
								'name'  => esc_html__( '&nbsp; &#9673;&#9711; &nbsp;Half', 'theme_name_txtd' ),
								'value' => 'half-height',
							),
							array(
								'name'  => esc_html__( '&#9673;&#9673;&#9711; Two Thirds', 'theme_name_txtd' ),
								'value' => 'two-thirds-height',
							),
							array(
								'name'  => esc_html__( '&#9673;&#9673;&#9673; Full Height', 'theme_name_txtd' ),
								'value' => 'full-height',
							)
						),
						'std'     => 'two-thirds-height',
					),
					array(
						'name' => esc_html__( 'Google Maps Pins', 'theme_name_txtd' ),
						'desc' => __( 'Paste here the Share URL you have taken from <a href="http://www.google.com/maps" target="_blank">Google Maps</a>.', 'theme_name_txtd' ),
						'id'   => 'gmap_urls',
						'type' => 'gmap_pins',
						'std' => array(
							1 => array(
								'location_url' => "https://www.google.ro/maps/@51.5075586,-0.1284425,18z",
								'name' => esc_html__('London', 'theme_name_txtd')
							)
						)
					),
				),
			),
		),
	);
	update_option( 'pixtypes_themes_settings', $types_options );
}
```

=== Old Change Log  ===

1.3.5
Improved the multicheck field

1.3.2
WordPress 4.3 compatibility
Fixed Sticky buttons for the PixBuilder field

1.3.1

Allow portfolio to be a jetpack compatible type
Small fixes to the gallery field

1.2.10

Show / Hide options bug fix

1.2.9

Gmap pins added

1.2.6

Builder field added
Support for wp 4.0
Small fixes

1.2.2

Small fixes to metaboxes

1.2.1

Github Updater slug fix
And small fixes...

1.2.0

Ajax Update
Gallery Metabox works now even if there is no wp-editor on page
And small fixes...

1.1.0

Add admin panel
Fixes

1.0.0 - Here we go
