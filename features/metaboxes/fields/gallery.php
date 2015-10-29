<?php
/**
 * Wordpress gallery procesing
 */
global $post;

// include our gallery scripts only when we need them
wp_enqueue_media();
wp_enqueue_script( 'pixgallery' );
// ensure the wordpress modal scripts even if an editor is not present
wp_enqueue_script( 'jquery-ui-dialog', false, array('jquery'), false, true );
wp_localize_script( 'pixgallery', 'locals', array(
	'ajax_url' => admin_url( 'admin-ajax.php' ),
	'pixtypes_l18n' => array(
		'confirmClearGallery' => __( 'You want for sure to clear this gallery?', 'pixtypes_txtd' ),
		'alertGalleryIsEmpty' => __( 'Gallery is already empty!', 'pixtypes_txtd' )
	)
) );?>
<div id="pixgallery" class="pixgallery_field" >
	<ul></ul>
	<a class="open_pixgallery" href="#" >
	<input type="hidden" name="<?php echo $field['id']; ?>" id="pixgalleries" value="<?php echo '' !== $meta ? $meta : $field['std'] ?>" />
		<i class="icon dashicons dashicons-images-alt2"></i>
		<span class="clear_gallery"><?php _e('Clear', 'pixtypes_txtd' ); ?></span>
	</a>
</div>