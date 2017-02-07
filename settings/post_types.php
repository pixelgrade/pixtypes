<?php
// init display options with false
$display_option = array(
	'portfolio' => false,
	'gallery'   => false
);

$options = get_option( 'pixtypes_settings' );
// go through each theme and activate portfolio post types
if ( isset( $options["themes"] ) ) {
	$theme_types = $options["themes"];
	foreach ( $theme_types as $key => $theme ) {

		$theme_name = str_replace( '_pixtypes_theme', '', $key );

		if ( isset( $theme['post_types'] ) && is_array( $theme['post_types'] ) ) {
			foreach ( $theme['post_types'] as $post_type => $post_type_args ) {

				if ( strpos( $post_type, 'jetpack' ) !== false ) {
					$post_type = str_replace( 'jetpack-', '', $post_type );
				}
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
	'label'   => esc_html__( 'Post Types', 'pixtypes' ),
	'options' => array()
); # config

if ( $display_option['portfolio'] ) {

	$options_config['options']['enable_portfolio']       = array(
		'label'      => esc_html__( 'Enable Portfolio', 'pixtypes' ),
		'default'    => true,
		'type'       => 'switch',
		'show_group' => 'enable_portfolio_group',
	);
	$options_config['options']['enable_portfolio_group'] = array(
		'type'    => 'group',
		'options' => array(
			'portfolio_single_item_label'             => array(
				'label'   => esc_html__( 'Single Item Label', 'pixtypes' ),
				'desc'    => esc_html__( 'Here you can change the singular label.The default is "Project"', 'pixtypes' ),
				'default' => esc_html__( 'Project', 'pixtypes' ),
				'type'    => 'text',
			),
			'portfolio_multiple_items_label'          => array(
				'label'   => esc_html__( 'Multiple Items Label (plural)', 'pixtypes' ),
				'desc'    => esc_html__( 'Here you can change the plural label. The default is "Projects"', 'pixtypes' ),
				'default' => esc_html__( 'Projects', 'pixtypes' ),
				'type'    => 'text',
			),
			'portfolio_change_single_item_slug'       => array(
				'label'      => esc_html__( 'Change Single Item Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the single portfolio item slug?', 'pixtypes' ),
				'default'    => true,
				'type'       => 'switch',
				'show_group' => 'portfolio_change_single_item_slug_group',
			),
			'portfolio_change_single_item_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'portfolio_new_single_item_slug' => array(
						'label'   => esc_html__( 'New Single Item Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the single portfolio item slug as you need it.', 'pixtypes' ),
						'default' => 'project',
						'type'    => 'text',
					),
				),
			),
			'portfolio_change_archive_slug'           => array(
				'label'      => esc_html__( 'Change Archive Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the portfolio archive slug? This will only be used if you don\'t have a page with the Portfolio template.', 'pixtypes' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'portfolio_change_archive_slug_group',
			),
			'portfolio_change_archive_slug_group'     => array(
				'type'    => 'group',
				'options' => array(
					'portfolio_new_archive_slug' => array(
						'label'   => esc_html__( 'New Archive Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the portfolio archive slug as you need it.', 'pixtypes' ),
						'default' => 'portfolio',
						'type'    => 'text',
					),
				),
			),
		),
	);

}

if ( $display_option['gallery'] ) {

	$options_config['options']['enable_gallery']       = array(
		'label'      => esc_html__( 'Enable Gallery', 'pixtypes' ),
		'default'    => true,
		'type'       => 'switch',
		'show_group' => 'enable_gallery_group',
	);
	$options_config['options']['enable_gallery_group'] = array(
		'type'    => 'group',
		'options' => array(
			'gallery_change_single_item_slug'       => array(
				'label'      => esc_html__( 'Change Single Item Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the single gallery item slug?', 'pixtypes' ),
				'default'    => true,
				'type'       => 'switch',
				'show_group' => 'gallery_change_single_item_slug_group',
			),
			'gallery_change_single_item_slug_group' => array(
				'type'    => 'group',
				'options' => array(
					'gallery_new_single_item_slug' => array(
						'label'   => esc_html__( 'New Single Item Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the single gallery item slug as you need it.', 'pixtypes' ),
						'default' => 'project',
						'type'    => 'text',
					),
				),
			),
			'gallery_change_archive_slug'           => array(
				'label'      => esc_html__( 'Change Archive Slug', 'pixtypes' ),
				'desc'       => esc_html__( 'Do you want to rewrite the gallery archive slug? This will only be used if you don\'t have a page with the gallery template.', 'pixtypes' ),
				'default'    => false,
				'type'       => 'switch',
				'show_group' => 'gallery_change_archive_slug_group',
			),
			'gallery_change_archive_slug_group'     => array(
				'type'    => 'group',
				'options' => array
				(
					'gallery_new_archive_slug' => array
					(
						'label'   => esc_html__( 'New Archive Slug', 'pixtypes' ),
						'desc'    => esc_html__( 'Change the gallery archive slug as you need it.', 'pixtypes' ),
						'default' => 'gallery',
						'type'    => 'text',
					),
				),
			),
		)
	);

}

return $options_config;