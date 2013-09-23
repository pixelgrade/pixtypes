<?php defined('ABSPATH') or die;
/**
 * On save action we process all settings for each theme settings we have in db
 *
 * @param $values
 */

function save_pixtypes_settings( $values ){

	$options = get_option('pixtypes_settings');

	if ( isset( $options['themes'] ) ) {

		/** Save these settings for each theme we have */
		foreach ( $options['themes'] as $key => &$theme ) {


			/** Apply settings for post types */
			if ( $theme['post_types'] ) {
				foreach( $theme['post_types'] as $name => &$post_type ) {
					// eliminate the theme prefix
					$post_type_key = strstr( $name, '_');
					$post_type_key = substr($post_type_key, 1);

					// modify these settings only if the post type is enabled
					if ( isset($options["enable_" . $post_type_key ]) && $options["enable_" . $post_type_key] ) {

						/** @TODO Care about uniqueness */
						if ( isset($values[$post_type_key . '_change_single_item_slug']) && $values[$post_type_key . '_change_single_item_slug'] && !empty($values[$post_type_key . '_new_single_item_slug']) ) {
							$post_type['rewrite']['slug'] = $values[$post_type_key . '_new_single_item_slug'];
						}

						if ( isset($values[$post_type_key . '_change_archive_slug']) && $values[$post_type_key . '_change_archive_slug'] && !empty( $values[$post_type_key . '_new_archive_slug'] ) ) {
							$post_type['has_archive'] = $values[$post_type_key . '_new_archive_slug'];
						}

						// assign tags @TODO later
//						if ( $values['portfolio_use_tags'] ) {
//							register_taxonomy_for_object_type( "post_tag", 'portfolio' );
//						}
					}
				}
			}

			/** Apply settings for taxonomies */
			if ( $theme['taxonomies'] ) {
				foreach( $theme['taxonomies'] as $name => &$taxonomy ) {

					// eliminate the theme prefix
					$tax_key = strstr( $name, '_');
					$tax_key = substr($tax_key, 1);

					// modify these settings only if the post type is enabled
					if ( isset($options["enable_" . $tax_key ]) && $options["enable_" . $tax_key] ) {

						/** @TODO Care about uniqueness */
						if ( isset( $values[$tax_key . '_change_archive_slug'] ) && $values[$tax_key . '_change_archive_slug'] && !empty( $values[$tax_key . '_change_archive_slug'] ) ) {
							$taxonomy['has_archive'] = $values[$tax_key . '_new_archive_slug'];
						}
					}
				}
			}
		}
	}

	var_dump($options);

	// save this settings back
	update_option('pixtype_settings', $options);

	/** Usually these settings will change slug settings se we need to flush the permalinks */
	flush_rewrite_rules();

}