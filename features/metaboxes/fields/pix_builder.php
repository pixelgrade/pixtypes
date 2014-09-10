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
		<option value="editor">Editor</option>
	</select>

	<span class="deletion  button button-secondary">
		Clear All
	</span>

	<div class="pixbuilder-grid gridster">
		<ul>
		</ul>
	</div>
</div>
<?php add_action('admin_footer', 'my_admin_footer_function');
function my_admin_footer_function() { ?>
<div class="pix_builder_editor_modal_container" style="display:none">
	<div class="modal_wrapper">
		<div class="media-modal wp-core-ui">
			<a class="media-modal-close close_modal_btn" href="#" title="Close"><span class="media-modal-icon"></span></a>
			<!--                <a class="close_modal_btn media-modal-close" href="#"></a>-->
			<div class="media-modal-content">
				<div class="media-frame-title"><h1>Insert Content</h1></div>
				<div class="media-frame-router"></div>
				<div class="media-frame-content">
					<?php wp_editor( '', 'pix_builder_editor', array() ); ?>
				</div>
				<div class="modal_controls media-frame-toolbar">
					<a class="close_modal_btn button button-large" href="#">Cancel</a>
					<a class="insert_editor_content button media-button button-primary button-large" href="#">Insert Content</a>
				</div>
			</div>
		</div>
		<div class="media-modal-backdrop close_modal_btn"></div>
	</div>
</div>
<?php }