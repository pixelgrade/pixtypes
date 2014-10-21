<?php

// init display options with false
$display_option = array(
	'portfolio_categories' => false,
	'gallery_categories' => false
);

$options = get_option('pixtypes_settings');
// go through each theme and activate portfolio post types
if ( isset($options["themes"]) ) {
	$theme_types = $options["themes"];
	foreach ( $theme_types as $key => $theme ) {
		if ( isset( $theme['taxonomies'] ) && is_array( $theme['taxonomies'] ) ) {
			$theme_name = str_replace( '_pixtypes_theme', '', $key );
			foreach ( $theme['taxonomies'] as $post_type => $post_type_args ) {
				$display_option[ str_replace( $theme_name . '_', '', $post_type ) ] = true;
			}
			$display_settings = true;
		} else {
			return array( 'type'=> 'hidden');
		}
	}
}

$options_config = array (
	'type' => 'postbox',
	'label' => 'Taxonomies',
	'options' => array()
); # config

if ( $display_option['portfolio_categories'] ) {

	$options_config['options']['enable_portfolio_categories'] = array(
		'label'          => __( 'Enable Portfolio Categories', 'pixtypes_txtd' ),
		'default'        => true,
		'type'           => 'switch',
		'show_group'     => 'enable_portfolio_categories_group',
		'display_option' => ''
	);

	$options_config['options']['enable_portfolio_categories_group'] = array(
		'type'    => 'group',
		'options' => array(
			'portfolio_categories_change_archive_slug'       => array(
				'label'      => __( 'Change Category Slug', 'pixtypes_txtd' ),
				'desc'       => __( 'Do you want to rewrite the portfolio category slug?', 'pixtypes_txtd' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'portfolio_categories_change_archive_slug_group'
			),
			'portfolio_categories_change_archive_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'portfolio_categories_new_archive_slug' => array(
						'label'   => __( 'New Category Slug', 'pixtypes_txtd' ),
						'desc'    => __( 'Change the portfolio category slug as you need it.', 'pixtypes_txtd' ),
						'default' => 'portfolio_categories',
						'type'    => 'text',
					),
				),
			),
		),
	);

}

if ( $display_option['gallery_categories'] ) {

	$options_config['options']['enable_gallery_categories'] = array(
		'label'      => __( 'Enable Gallery Categories', 'pixtypes_txtd' ),
		'default'    => true,
		'type'       => 'switch',
		'show_group' => 'enable_gallery_categories_group'
	);

	$options_config['options']['enable_gallery_categories_group'] = array(
		'type'    => 'group',
		'options' => array(
			'gallery_categories_change_archive_slug'       => array(
				'label'      => __( 'Change Category Slug', 'pixtypes_txtd' ),
				'desc'       => __( 'Do you want to rewrite the gallery category slug?', 'pixtypes_txtd' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'gallery_categories_change_archive_slug_group'
			),
			'gallery_categories_change_archive_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'gallery_categories_new_archive_slug' => array(
						'label'   => __( 'New Category Slug', 'pixtypes_txtd' ),
						'desc'    => __( 'Change the gallery category slug as you need it.', 'pixtypes_txtd' ),
						'default' => 'gallery_categories',
						'type'    => 'text',
					),
				),
			),
		),
	);

}

return $options_config;