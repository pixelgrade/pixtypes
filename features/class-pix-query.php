<?php

class Pix_Query extends Wp_Query {

	function get_post_metabox( $key = '', $args = array() ) {

	}

	/**
	 * Get the gallery of the global post
	 * @required global $post this should be called inside a loop
	 * @return array $ids
	 */

	function get_gallery_ids() {
		global $post;
		var_dump(get_post_meta($post->ID));
	}

}