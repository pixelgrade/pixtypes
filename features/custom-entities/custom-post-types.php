<?php

// register post types
$portfolio_enabled = true;

if ( $portfolio_enabled ) {

	// go through each theme and activate portfolio post types
	$theme_types = get_option('pixtypes_settings');
	if ( empty($theme_types) || !array($theme_types)) return;

	foreach ( $theme_types as $key => $theme ) {

		// post types
		if ( isset( $theme['post_types'] ) && is_array( $theme['post_types'] ) ) {
			foreach ( $theme['post_types'] as $post_type => $post_type_args ) {
				register_post_type( $post_type, $post_type_args );
			}
		}

		// taxonomies
		if ( isset( $theme['taxonomies'] ) && is_array( $theme['taxonomies'] ) ) {
			foreach ( $theme['taxonomies'] as $tax => $tax_args) {

				$tax_post_types = $tax_args['post_types'];

				// remove "post_types", isn't a register_taxonomy argument we are just using it for post type linking
				unset( $tax_args['post_types'] );
				$console_this = register_taxonomy( 'cityhub_portfolio_cat', $tax_post_types, $tax_args );
			}
		}

	}
}
