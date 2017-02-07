<?php
/**
 * Wordpress single image procesing
 */
global $post;

// include our image scripts only when we need them
wp_enqueue_media();
wp_enqueue_script( 'piximage' );
// ensure the wordpress modal scripts even if an editor is not present
wp_enqueue_script( 'jquery-ui-dialog', false, array( 'jquery' ), false, true );
wp_localize_script( 'piximage', 'locals', array(
	'ajax_url'      => admin_url( 'admin-ajax.php' ),
	'pixtypes_l18n' => array(
		'setThumbnailImageTitle' => esc_html__( 'Choose Image', 'pixtypes' ),
		'confirmClearImage'      => esc_html__( 'Are you sure you want to clear this image?', 'pixtypes' ),
		'alertImageIsEmpty'      => esc_html__( 'This image is already empty!', 'pixtypes' )
	)
) );

$class = empty( $field['class'] ) ? '' : $field['class']; ?>
<div id="<?php echo $field['id']; ?>" class="piximage_field hidden <?php echo $class; ?>">
	<ul></ul>
	<a class="open_piximage" href="#">
		<input type="hidden" name="<?php echo $field['id']; ?>" class="piximage_id"
		       value="<?php echo '' !== $meta ? $meta : $field['std'] ?>"/>
		<div><i class="icon dashicons dashicons-images-alt2"></i>
			<span><?php echo empty ( $field['button_text'] ) ? esc_html__( 'Add Image', 'pixtypes' ) : $field['button_text']; ?></span>
		</div>
		<span
			class="clear_image"><?php echo empty ( $field['clear_text'] ) ? esc_html__( 'Clear', 'pixtypes' ) : $field['clear_text']; ?></span>
	</a>
</div>