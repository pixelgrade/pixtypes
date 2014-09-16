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
) );
// output html
echo '<div id="pixgallery">'.
		 '<ul></ul>'.
		 '<a class="open_pixgallery" href="#" class="wp-gallery" >'.
			'<input type="hidden" name="', $field['id'], '" id="pixgalleries" value="', '' !== $meta ? $meta : $field['std'], '" />'.
			'<i class="icon"></i>'.
			'<span class="clear_gallery">' . __('Clear', 'pixtypes_txtd' ) .'</span>'.
		 '</a>'.
	  '</div>'; ?>