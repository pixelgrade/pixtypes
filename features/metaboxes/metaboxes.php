<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category WPGRADE_THEMENAME
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */


function load_metaboxes_fromdb( $meta_boxes ) {
	// make sure we are in good working order
	if ( empty( $meta_boxes ) ) {
		$meta_boxes = array();
	}

	$options = get_option('pixtypes_settings');

	if ( empty( $options['themes'] ) ) {
		return $meta_boxes;
	}

	// We only want to display the metaboxes of the current theme
	if ( class_exists('wpgrade') ) {
		$current_theme = wpgrade::shortname();
	} else {
		$current_theme = 'pixtypes';
	}

	if ( empty( $options['themes'][ $current_theme ]['metaboxes'] ) ) {
		return $meta_boxes;
	}

	$theme_metaboxes = $options['themes'][ $current_theme ]['metaboxes'];
	if ( ! empty( $theme_metaboxes ) && is_array( $theme_metaboxes ) ) {
		foreach ( $theme_metaboxes as $metabox ) {
			$meta_boxes[] = $metabox;
		}
	}

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'load_metaboxes_fromdb', 1 );

/*
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) ) {
		require_once 'init.php';

		require_once 'cmb-field-select2/cmb-field-select2.php';
	}

}
add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
