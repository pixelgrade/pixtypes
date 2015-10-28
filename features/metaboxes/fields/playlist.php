<?php
/**
 * Wordpress playlist procesing
 */

// include our gallery scripts only when we need them
wp_enqueue_media();
wp_enqueue_script( 'pixplaylist' );
// ensure the wordpress modal scripts even if an editor is not present
wp_enqueue_script( 'jquery-ui-dialog', false, array( 'jquery' ), false, true );
wp_localize_script( 'pixplaylist', 'playlist_locals', array(
	'ajax_url'      => admin_url( 'admin-ajax.php' ),
	'playlist_type' => $playlist_type,
	'pixtypes_l18n' => array(
		'confirmClearGallery' => __( 'You want for sure to clear this gallery?', 'pixtypes_txtd' ),
		'alertGalleryIsEmpty' => __( 'Gallery is already empty!', 'pixtypes_txtd' )
	)
) ); ?>
<div id="pixvideos">
	<ul></ul>
	<a class="open_pixvideos" href="#">
		<input type="hidden" name="<?php echo $field['id'] ?>" id="pixplaylist" value="<?php echo '' !== $meta ? $meta : $field['std']; ?>"/>
		<i class="icon dashicons dashicons-playlist-video"></i>
		<span class="clear_gallery"><?php _e( 'Clear', 'pixtypes_txtd' ); ?></span>
	</a>
</div>