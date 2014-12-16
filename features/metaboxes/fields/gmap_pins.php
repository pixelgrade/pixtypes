<?php
/**
 * Wordpress gmap pins procesing
 */
global $post; ?>
<div class="gmap_pins_container">
	<ul class="gmap_pins" data-field_name="<?php echo $field['id']; ?>">
		<?php if ( empty( $meta ) ) {
			$meta = array(
				1 => array(
					'location_url' => '',
					'name' => ''
				)
			);
		}

		foreach ( $meta as $key => $pin ) { ?>
			<li class="gmap_pin">
				<fieldset class="pin_location_url">
					<label for="<?php echo $field['id']; ?>[<?php echo $key ?>][location_url]" >#<?php echo $key . ' ' . __('Location URL', 'pixtypes_txtd'); ?></label>
					<input type="text" name="<?php echo $field['id']; ?>[<?php echo $key ?>][location_url]" value="<?php echo $pin['location_url']; ?>" />
				</fieldset>
				<fieldset class="pin_name">
					<label for="<?php echo $field['id']; ?>[<?php echo $key ?>][name]" ><?php _e('Name', 'pixtypes_txtd'); ?></label>
					<input type="text" name="<?php echo $field['id']; ?>[<?php echo $key ?>][name]" value="<?php echo $pin['name']; ?>" />
				</fieldset>
				<span class="pin_delete"><?php _e('Delete', 'pixtypes_txtd'); ?></span>
			</li>
		<?php } ?>
	</ul>

	<span class="button add_new_location"><?php _e('Add New Location', 'pixtypes_txtd') ?></span>

	<?php if ( isset( $field['desc'] ) && !empty( $field['desc'] ) ) { ?>
		<span class="cmb_metabox_description"><?php echo $field['desc']; ?></span>
	<?php } ?>
</div>