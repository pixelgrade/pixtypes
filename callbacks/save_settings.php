<?php defined('ABSPATH') or die;
/**
 * On save action we process all settings for each theme settings we have in db
 *
 * Think about inserting this function in after_theme_switch hook so the settings should be updated on theme switch
 *
 * @param $values
 */

function save_pixtypes_settings( $values ){

	if ( class_exists('wpgrade') ) {
		$current_theme = wpgrade::shortname();
	} else {
		$current_theme = 'pixtypes';
	}

	$options = get_option('pixtypes_settings');

	if ( isset( $options['themes'] ) ) {

		/** Save these settings for each theme we have */
		foreach ( $options['themes'] as $key => &$theme ) {

			/**
			 * @TODO Care about uniqueness ?
			 * Well create a slug prefix which is empty for the current theme
			 * We do that because we need the all slugs unique
			 */

			if ( $current_theme == $key) {
				$slug_prefix = '';
			} else {
				$slug_prefix = $key . '_';
			}

			/** Apply settings for post types */
			if ( isset($theme['post_types']) ) {
				foreach( $theme['post_types'] as $name => &$post_type ) {

					$is_jetpack_compatible = false;
					if ( strpos( $name, 'jetpack' ) !== FALSE ) {
						$is_jetpack_compatible = true;
					}

					if ( $is_jetpack_compatible ) {
						$post_type_key = strstr( $name, '-');
						$post_type_key = substr($post_type_key, 1);
					} else {
						// get post type key without prefix
						$post_type_key = strstr( $name, '_');
						$post_type_key = substr($post_type_key, 1);
					}

					// modify these settings only if the post type is enabled
					if ( isset($options["enable_" . $post_type_key ]) && $options["enable_" . $post_type_key] ) {

						/** Singular labels */
						if ( isset($values[$post_type_key . '_single_item_label']) && $values[$post_type_key . '_single_item_label'] != $post_type['labels']['name'] ) {

							$single_label = $values[$post_type_key . '_single_item_label'];

							$post_type['labels']['name'] = $single_label;
							$post_type['labels']['singular_name'] = $single_label;
							$post_type['labels']['add_new_item'] = 'Add New ' . $single_label;
							$post_type['labels']['edit_item'] = 'Edit ' . $single_label;
							$post_type['labels']['new_item'] = 'New ' . $single_label;
							$post_type['labels']['view_item'] = 'View ' . $single_label;
							$post_type['labels']['not_found'] = 'No '. $single_label .' found' ;
							$post_type['labels']['not_found_in_trash'] = 'No '. $single_label .' found in Trash';
						}

						/** Plural labels */
						if ( isset($values[$post_type_key . '_multiple_items_label']) && $values[$post_type_key . '_multiple_items_label'] != $post_type['labels']['menu_name'] ) {

							$plural_label = $values[$post_type_key . '_multiple_items_label'];

							$post_type['labels']['menu_name'] = $plural_label;
							$post_type['labels']['all_items'] = 'All '. $plural_label;
							$post_type['labels']['search_items'] = 'Search'. $plural_label;
						}

						/** Slugs */
						if ( isset($values[$post_type_key . '_change_single_item_slug']) && $values[$post_type_key . '_change_single_item_slug'] && !empty($values[$post_type_key . '_new_single_item_slug']) ) {
							$post_type['rewrite']['slug'] = $slug_prefix . $values[$post_type_key . '_new_single_item_slug'];
						}

						if ( isset($values[$post_type_key . '_change_archive_slug']) && $values[$post_type_key . '_change_archive_slug'] && !empty( $values[$post_type_key . '_new_archive_slug'] ) ) {
							$post_type['has_archive'] = $slug_prefix . $values[$post_type_key . '_new_archive_slug'];
						}

						// assign tags @TODO later
//						if ( $values['portfolio_use_tags'] ) {
//							register_taxonomy_for_object_type( "post_tag", 'portfolio' );
//						}
					}
				}
			}

			/** Apply settings for taxonomies */
			if ( isset($theme['taxonomies']) ) {
				foreach( $theme['taxonomies'] as $name => &$taxonomy ) {

					$is_jetpack_compatible = false;
					if ( strpos( $name, 'jetpack' ) !== FALSE ) {
						///$xxxx = str_replace(  'jetpack-', '', $name);
						$is_jetpack_compatible = true;
					}

					if ( $is_jetpack_compatible ) {
						// eliminate the theme prefix
						$tax_key = strstr( $name, '-');
						$tax_key = substr($tax_key, 1);
					} else {
						// eliminate the theme prefix
						$tax_key = strstr( $name, '_');
						$tax_key = substr($tax_key, 1);
					}

					// modify these settings only if the post type is enabled
					if ( isset($options["enable_" . $tax_key ]) && $options["enable_" . $tax_key] ) {
						if ( isset( $values[$tax_key . '_change_archive_slug'] ) && $values[$tax_key . '_change_archive_slug'] && !empty( $values[$tax_key . '_change_archive_slug'] ) ) {
							$taxonomy['rewrite']['slug'] = $slug_prefix . $values[$tax_key . '_new_archive_slug'];
						}
					}
				}
			}
		}
	}

	// save this settings back
	update_option('pixtypes_settings', $options);

	/** Usually these settings will change slug settings se we need to flush the permalinks */
//	global $wp_rewrite;
//	//Call flush_rules() as a method of the $wp_rewrite object
//	$wp_rewrite->flush_rules();

	/**
	 * http://wordpress.stackexchange.com/questions/36152/flush-rewrite-rules-not-working-on-plugin-deactivation-invalid-urls-not-showing
	 * nothing from above works in plugin so ...
	 */
	delete_option('rewrite_rules');

}