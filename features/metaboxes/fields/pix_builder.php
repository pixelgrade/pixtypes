<div class="pix_builder_container hidden">
	<?php
	$gridster_params = '';
	if ( isset($field['gridster_params'] ) ) {
		$gridster_params = ' data-params=\'' . json_encode($field['gridster_params'] ) . '\'';
	}

	echo '<input type="hidden" name="', $field['id'], '" id="pix_builder" value="', '' !== $meta ? htmlspecialchars($meta) : $field['std'], '" '. $gridster_params .' />'; ?>
	<div class="pixbuilder-controls">
		<button class="add_block button button-primary button-large" value="image" > + Image</button>
		<button class="add_block button button-primary button-large" value="editor"> + Editor</button>
	</div>

	<!-- <span class="clear-all button button-secondary">Clear All</span> -->

	<div class="pixbuilder-grid gridster">
		<ul>
			<?php if ( !empty($meta) ) {
				$meta = json_decode($meta);
				if ( !empty($meta)) {
					foreach ( $meta as $key => $block ) {

						if ( ! isset( $block->type ) ) {
							return;
						}

						$content          = '';
						$controls_content = '';
						switch ( $block->type ) {
							case 'editor' :
								$content = '<textarea class="to_send" style="display: none">' . htmlspecialchars($block->content) . '</textarea>' . '<div class="editor_preview">' . '<div class="editor_preview_wrapper">' . pix_builder_display_content( $block->content ) . '</div>' . '</div>';

								$controls_content = '<a class="edit_editor"><span>Edit</span></a>';

								break;

							case 'image' :
								// in case of an image the content should hold only an integer which represents the id
								if ( is_numeric( $block->content ) && $block->content !== '' ) {
									$attach = wp_get_attachment_image_src( $block->content );

									if ( isset( $attach[ 0 ] ) && ! empty( $attach[ 0 ] ) ) {
										$content          = '<img class="image_preview" src="' . $attach[ 0 ] . '">';
										$controls_content = '<a class="open_media" href="#" class="wp-gallery" data-attachment_id="' . $block->content . '"><span>' . __( 'Set Image', 'pixtypes' ) . '</span></a>';
									}
								} else {
									$content          = '<img class="image_preview">';
									$controls_content = '<a class="open_media" href="#" class="wp-gallery" data-attachment_id="' . $block->content . '"><span>' . __( 'Set Image', 'pixtypes' ) . '</pan></a>';
								}
								break;
							default :
								break;
						}

						$empty_class = '';

						if ( empty($block->content) ) {
							$empty_class = 'empty';
						} ?>

						<li id="block_<?php echo $block->id ?>" class="block-type--<?php echo $block->type; ?> item" data-type="<?php echo $block->type ?>" data-row="<?php echo $block->row ?>" data-col="<?php echo $block->col ?>" data-sizex="<?php echo $block->size_x ?>" data-sizey="<?php echo $block->size_y ?>">
							<div class="item__controls">
								<ul class="nav nav--controls">
									<li class="edit"><?php echo $controls_content ?></li>
									<li class="remove remove_block"><span>Remove</span></li>
									<li class="move drag_handler"></li>
								</ul>
							</div>
							<div class="item__content block_content <?php echo $empty_class;?>">
								<?php echo $content ?>
							</div>
						</li>
					<?php
					}
				}
			}?>
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
						<?php
						function pix_builder_change_mce_options($initArray) {

							$initArray['verify_html'] = false;
							$initArray['cleanup_on_startup'] = false;
							$initArray['cleanup'] = false;
							$initArray['forced_root_block'] = false;
							$initArray['validate_children'] = false;
							$initArray['remove_redundant_brs'] = false;
							$initArray['remove_linebreaks'] = false;
							$initArray['force_p_newlines'] = true;
							$initArray['force_br_newlines'] = true;
							$initArray['fix_table_elements'] = false;
							$initArray['entity_encoding'] = "named";

							$initArray['entities'] = '160,nbsp,38,amp,60,lt,62,gt';

							return $initArray;
						}

						add_filter('tiny_mce_before_init', 'pix_builder_change_mce_options');

						wp_editor( '', 'pix_builder_editor', array( 'textarea_rows' => 20, 'editor_height' => 350 ) );

						remove_filter('tiny_mce_before_init', 'pix_builder_change_mce_options');
						?>
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


function pix_builder_display_content( $content = '' ) {
	// since we cannot apply "the_content" filter on some content blocks we should apply at least these bellow
	$content = apply_filters( 'wptexturize', $content );
	$content = apply_filters( 'convert_smilies', $content );
	$content = apply_filters( 'convert_chars', $content );

	$content = wpautop( $content );

	if ( function_exists( 'wpgrade_remove_spaces_around_shortcodes' ) ) {
		$content = wpgrade_remove_spaces_around_shortcodes( $content );
	}
	//	$content = shortcode_unautop ($content);
	$content = apply_filters( 'prepend_attachment', $content );

	// in case there is a shortcode
//	return nl2br( $content );
//	return do_shortcode( $content );
	return $content;
}