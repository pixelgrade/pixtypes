<?php

// register post types
$options = get_option('pixtypes_settings');

// go through each theme and activate portfolio post types

if ( empty($options["themes"]) || !array($options["themes"])) return;
$theme_types = $options["themes"];
foreach ( $theme_types as $key => $theme ) {

	// post types
	if ( isset( $theme['post_types'] ) && is_array( $theme['post_types'] ) ) {
		foreach ( $theme['post_types'] as $post_type => $post_type_args ) {

			// eliminate the theme prefix
			$post_type_key = strstr( $post_type, '_');
			$post_type_key = substr($post_type_key, 1);
			if ( isset($options["enable_" . $post_type_key ]) && $options["enable_" . $post_type_key] ) {
				register_post_type( $post_type, $post_type_args );
			}
		}
	}

	// taxonomies
	if ( isset( $theme['taxonomies'] ) && is_array( $theme['taxonomies'] ) ) {
		foreach ( $theme['taxonomies'] as $tax => $tax_args) {
			$tax_post_types = $tax_args['post_types'];
			// remove "post_types", isn't a register_taxonomy argument we are just using it for post type linking
			unset( $tax_args['post_types'] );

			// eliminate the theme prefix
			$tax_key = strstr( $tax, '_');
			$tax_key = substr($tax_key, 1);

			if ( isset($options["enable_" . $tax_key ]) && $options["enable_" . $tax_key] ) {
				register_taxonomy( $tax, $tax_post_types, $tax_args );
			}
		}
	}

}
