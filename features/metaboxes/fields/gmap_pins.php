<?php
/**
 * Wordpress gmap pins procesing
 */


wp_enqueue_script( 'gmap_pins' );

wp_localize_script( 'gmap_pins', 'l18n_gmap_pins', array(
	'location_url_label'   => esc_html__( 'Location URL', 'pixtypes' ),
	'name_label'           => esc_html__( 'Name', 'pixtypes' ),
	'delete_label'         => esc_html__( 'Delete', 'pixtypes' ),
	'confirm_delete'       => esc_html__( 'Are you sure?', 'pixtypes' ),
	'dont_delete_all_pins' => esc_html__( 'This page is useless without pins. Better trash this page!', 'pixtypes' ),
) );

global $post; ?>
<div class="gmap_pins_container">
	<ul class="gmap_pins" data-field_name="<?php echo $field['id']; ?>">
		<?php if ( empty( $meta ) ) {
			$meta = array(
				1 => array(
					'location_url' => '',
					'name'         => ''
				)
			);

			//this runs only when the field is empty and not initialized ... some sort of demo
			if ( isset( $field['std'] ) && is_array( $field['std'] ) ) {
				$meta = $field['std'];
			}
		}

		foreach ( $meta as $key => $pin ) { ?>
			<li class="gmap_pin">
				<fieldset class="pin_location_url">
					<label
						for="<?php echo $field['id']; ?>[<?php echo $key ?>][location_url]">#<?php echo $key . ' ' . esc_html__( 'Location URL', 'pixtypes' ); ?></label>
					<input type="text" name="<?php echo $field['id']; ?>[<?php echo $key ?>][location_url]"
					       value="<?php echo $pin['location_url']; ?>"/>
				</fieldset>
				<fieldset class="pin_name">
					<label
						for="<?php echo $field['id']; ?>[<?php echo $key ?>][name]"><?php esc_html_e( 'Name', 'pixtypes' ); ?></label>
					<input type="text" name="<?php echo $field['id']; ?>[<?php echo $key ?>][name]"
					       value="<?php echo $pin['name']; ?>"/>
				</fieldset>
				<span class="pin_delete"></span>
			</li>
		<?php } ?>
	</ul>

	<span class="button add_new_location"><?php esc_html_e( 'Add New Location', 'pixtypes' ) ?></span>

	<?php if ( isset( $field['desc'] ) && ! empty( $field['desc'] ) ) { ?>
		<span class="cmb_metabox_description"><?php echo $field['desc']; ?></span>
	<?php } ?>
</div>