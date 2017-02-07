<?php

// init display options with false
$display_option = array(
	'portfolio_categories'   => false,
	'gallery_categories'     => false,
	'jetpack-portfolio-type' => false,
	'jetpack-portfolio-tag'  => false
);

$options = get_option( 'pixtypes_settings' );
// go through each theme and activate portfolio post types
if ( isset( $options["themes"] ) ) {
	$theme_types = $options["themes"];
	foreach ( $theme_types as $key => $theme ) {
		if ( isset( $theme['taxonomies'] ) && is_array( $theme['taxonomies'] ) ) {
			$theme_name = str_replace( '_pixtypes_theme', '', $key );
			foreach ( $theme['taxonomies'] as $post_type => $post_type_args ) {
				$display_option[ str_replace( $theme_name . '_', '', $post_type ) ] = true;
			}
			$display_settings = true;
		} else {
			return array( 'type' => 'hidden' );
		}
	}
}

$options_config = array(
	'type'    => 'postbox',
	'label'   => 'Taxonomies',
	'options' => array()
); # config

// Note: in case of jetpack types is very important to have the key with "-" so allover
// there is a key with portfolio and a key they should be splited by a "-"

if ( $display_option['jetpack-portfolio-type'] ) {

	$options_config['options']['enable_portfolio-type'] = array(
		'label'          => esc_html__( 'Enable Portfolio Types', 'pixtypes' ),
		'default'        => true,
		'type'           => 'switch',
		'show_group'     => 'enable_portfolio-type_group',
		'display_option' => ''
	);

	$options_config['options']['enable_portfolio-type_group'] = array(
		'type'    => 'group',
		'options' => array(
			'portfolio-type_change_archive_slug'       => array(
				'label'      => esc_html__( 'Change Portfolio Types Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the portfolio type slug?', 'pixtypes' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'portfolio-type_change_archive_slug_group'
			),
			'portfolio-type_change_archive_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'portfolio-type_new_archive_slug' => array(
						'label'   => esc_html__( 'New Portfolio Type Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the portfolio type slug as you need it.', 'pixtypes' ),
						'default' => 'project-type',
						'type'    => 'text',
					),
				),
			),
		),
	);
}


if ( $display_option['jetpack-portfolio-tag'] ) {

	$options_config['options']['enable_portfolio-tag'] = array(
		'label'          => esc_html__( 'Enable Portfolio Tag', 'pixtypes' ),
		'default'        => true,
		'type'           => 'switch',
		'show_group'     => 'enable_portfolio-tag_group',
		'display_option' => ''
	);

	$options_config['options']['enable_portfolio-tag_group'] = array(
		'type'    => 'group',
		'options' => array(
			'portfolio-tag_change_archive_slug'       => array(
				'label'      => esc_html__( 'Change Portfolio Tag Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the portfolio tag slug?', 'pixtypes' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'portfolio-tag_change_archive_slug_group'
			),
			'portfolio-tag_change_archive_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'portfolio-tag_new_archive_slug' => array(
						'label'   => esc_html__( 'New Portfolio Tag Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the portfolio tag slug as you need it.', 'pixtypes' ),
						'default' => 'project-tag',
						'type'    => 'text',
					),
				),
			),
		),
	);
}

if ( $display_option['portfolio_categories'] ) {

	$options_config['options']['enable_portfolio_categories'] = array(
		'label'          => esc_html__( 'Enable Portfolio Categories', 'pixtypes' ),
		'default'        => true,
		'type'           => 'switch',
		'show_group'     => 'enable_portfolio_categories_group',
		'display_option' => ''
	);

	$options_config['options']['enable_portfolio_categories_group'] = array(
		'type'    => 'group',
		'options' => array(
			'portfolio_categories_change_archive_slug'       => array(
				'label'      => esc_html__( 'Change Category Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the portfolio category slug?', 'pixtypes' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'portfolio_categories_change_archive_slug_group'
			),
			'portfolio_categories_change_archive_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'portfolio_categories_new_archive_slug' => array(
						'label'   => esc_html__( 'New Category Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the portfolio category slug as you need it.', 'pixtypes' ),
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
		'label'      => esc_html__( 'Enable Gallery Categories', 'pixtypes' ),
		'default'    => true,
		'type'       => 'switch',
		'show_group' => 'enable_gallery_categories_group'
	);

	$options_config['options']['enable_gallery_categories_group'] = array(
		'type'    => 'group',
		'options' => array(
			'gallery_categories_change_archive_slug'       => array(
				'label'      => esc_html__( 'Change Category Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the gallery category slug?', 'pixtypes' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'gallery_categories_change_archive_slug_group'
			),
			'gallery_categories_change_archive_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'gallery_categories_new_archive_slug' => array(
						'label'   => esc_html__( 'New Category Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the gallery category slug as you need it.', 'pixtypes' ),
						'default' => 'gallery_categories',
						'type'    => 'text',
					),
				),
			),
		),
	);
}

return $options_config;