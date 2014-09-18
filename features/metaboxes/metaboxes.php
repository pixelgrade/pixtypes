<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category WPGRADE_THEMENAME
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */


function load_metaboxes_fromdb( array $meta_boxes ){
	$options = get_option('pixtypes_settings');

	if ( !isset($options["themes"])) return;
	$theme_types = $options["themes"];
	if ( empty($theme_types) || !array($theme_types)) return;
	foreach ( $theme_types as $key => $theme ) {
		if ( isset( $theme['metaboxes']) && is_array( $theme['metaboxes'] )) {
			foreach ( $theme['metaboxes'] as $metabox){
				$meta_boxes[] = $metabox;
			}
		}
	}

	return $meta_boxes;
}

add_filter( 'cmb_meta_boxes', 'load_metaboxes_fromdb', 1 );

add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
/*
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) ) {
		require_once 'init.php';

		require_once 'cmb-field-select2/cmb-field-select2.php';
	}

}
