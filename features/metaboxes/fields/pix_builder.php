<div class="pix_builder_container">
	<?php

	$gridster_params = '';
	if ( isset($field['gridster_params'] ) ) {
		$gridster_params = ' data-params=\'' . json_encode($field['gridster_params'] ) . '\'';
	}

	echo '<input type="hidden" name="', $field['id'], '" id="pix_builder" value="', '' !== $meta ? htmlspecialchars($meta) : $field['std'], '" '. $gridster_params .' />'; ?>

	<span class="add_block button button-primary button-large">
		Add :
	</span>
	<select name="block_type" id="block_type">
		<option value="image" selected>Image</option>
		<option value="text">Text</option>
	</select>

	<span class="deletion  button button-secondary">
		Clear All
	</span>
		<div class="gridster">
			<ul>

			</ul>
		</div>
</div>