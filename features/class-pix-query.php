<?php

class Pix_Query extends Wp_Query {

	function get_post_metabox( $key = '', $args = array() ) {
		global $post;
		$ids = get_post_meta( $post->ID, $key, true );
	}

	/**
	 * Get the gallery of the global post
	 * @required global $post this should be called inside a loop
	 * @return array $ids
	 */

	function get_gallery_ids( $key = '' ){
		global $post;
		$prefix = '';

		if ( class_exists('wpgrade') ) {
			$prefix = wpgrade::prefix();
		}

		$ids = get_post_meta( $post->ID, $prefix . $key, true );

		if (!empty($ids)) {
			$ids = explode(',',$ids);
		}
		return $ids;
	}
}