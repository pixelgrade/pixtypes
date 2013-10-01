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

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}

// add video support for attachments

add_filter("attachment_fields_to_edit", "add_video_url_field_to_attachments", null, 2);

function add_video_url_field_to_attachments($form_fields, $post){
	// Add a Credit field
	$form_fields["video_url"] = array(
		"label" => __("Video URL", 'pixtypes_txtd'),
		"input" => "text", // this is default if "input" is omitted
		"value" => esc_url( get_post_meta($post->ID, "_video_url", true) ),
		"helps" => __("<p>Here you can link a video</p><small>Only youtube or vimeo!</small>", 'pixtypes_txtd'),
	);

	return $form_fields;
}

/**
 * Save custom media metadata fields
 *
 * Be sure to validate your data before saving it
 * http://codex.wordpress.org/Data_Validation
 *
 * @param $post The $post data for the attachment
 * @param $attachment The $attachment part of the form $_POST ($_POST[attachments][postID])
 * @return $post
 */
function add_image_attachment_fields_to_save( $post, $attachment ) {
	if ( isset( $attachment['video_url'] ) )
		update_post_meta( $post['ID'], '_video_url', esc_url($attachment['video_url']) );

	return $post;
}
add_filter("attachment_fields_to_save", "add_image_attachment_fields_to_save", null , 2);