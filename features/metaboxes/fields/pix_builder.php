<div class="pix_builder_container hidden">
	<?php

	$base64_decode = false;
	$gridster_params = '';
	if( isset( $field['gridster_params'] ) ) {
		$gridster_params = ' data-params=\'' . json_encode( $field['gridster_params'] ) . '\'';
	}
	global $post;

	// this should ensure the legacy with old meta values
	// basically if there is no post content it will fall on old meta way. and converti it to content(the new way)
	if( isset( $post->post_content ) && ! empty( $post->post_content ) ) {
		// remove the white spacces added by the editor
		$meta = preg_replace( '/[\p{Z}\s]{2,}/u', ' ', $post->post_content );
		$base64_decode = true;
	}

	echo '<input type="hidden" name="', $field['id'], '" id="pix_builder" value="', '' !== $meta ? htmlspecialchars( $meta ) : $field['std'], '" ' . $gridster_params . ' />'; ?>
	<div class="pixbuilder-controls">
		<button class="add_block button button-primary button-large"
		        value="image"> <?php esc_html_e( '+ Image', 'pixtypes' ); ?></button>
		<button class="add_block button button-primary button-large"
		        value="editor"> <?php esc_html_e( '+ Editor', 'pixtypes' ); ?></button>
	</div>

	<!-- <span class="clear-all button button-secondary">Clear All</span> -->

	<div class="pixbuilder-grid gridster">
		<ul>
			<?php

			if( ! empty ( $meta ) ) {

				$meta = json_decode( $meta );


				if( ! empty( $meta ) && is_array( $meta ) ) {

					foreach ( $meta as $key => $block ) {

						if( ! isset( $block->type ) ) {
							return;
						}

						$content          = '';
						$controls_content = '';
						switch ( $block->type ) {
							case 'editor' :
								if ( $base64_decode ) {
									$block_content = base64_decode( $base64_decode );
								}
								$block_content = htmlspecialchars( $block->content );
								$content = '<textarea class="to_send" style="display: none">' . $block_content . '</textarea>' . '<div class="editor_preview">' . '<div class="editor_preview_wrapper">' . pix_builder_display_content( $block->content, $base64_decode ) . '</div>' . '</div>';

								$controls_content = '<a class="edit_editor"><span>' . esc_html__( 'Edit', 'pixtypes' ) . '</span></a>';

								break;

							case 'image' :
								// in case of an image the content should hold only an integer which represents the id
								if( is_numeric( $block->content ) && $block->content !== '' ) {
									$attach = wp_get_attachment_image_src( $block->content );

									if( isset( $attach[0] ) && ! empty( $attach[0] ) ) {
										$content          = '<img class="image_preview" src="' . $attach[0] . '">';
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

						if( empty( $block->content ) ) {
							$empty_class = 'empty';
						}

						if( empty( $block->position ) ) {
							$block->position = array(
								'top'    => 0,
								'right'  => 0,
								'bottom' => 0,
								'left'   => 0
							);
						} else {
							$block->position = (array) $block->position;
						}

						$middle_status = 'active';

						foreach ( $block->position as $pos ) {
							if( $pos !== '0' ) {
								$middle_status = '';
							}
						} ?>
						<li id="block_<?php echo $block->id ?>" class="block-type--<?php echo $block->type; ?> item"
						    data-type="<?php echo $block->type ?>" data-row="<?php echo $block->row ?>"
						    data-col="<?php echo $block->col ?>" data-sizex="<?php echo $block->size_x ?>"
						    data-sizey="<?php echo $block->size_y ?>">
							<div class="item__controls">
								<ul class="nav nav--controls">
									<li class="edit"><?php echo $controls_content ?></li>
									<li class="position"><span><?php esc_html_e( 'Position', 'pixtypes' ); ?></span>
										<div class="position__ui">
											<div
												class="position__ui-title"><?php esc_html_e( 'Alignment', 'pixtypes' ); ?></div>
											<div class="position__ui-body">
												<div class="position__ui-row">
													<div
														class="position__ui-cell top <?php echo '0' === $block->position['top'] ? '' : 'active'; ?>">
														<div class="position__ui-handle"
														     data-step="<?php echo $block->position['top']; ?>"><?php esc_html_e( 'top', 'pixtypes' ); ?></div>
													</div>
												</div>
												<div class="position__ui-row">
													<div
														class="position__ui-cell left <?php echo '0' === $block->position['left'] ? '' : 'active'; ?>">
														<div class="position__ui-handle"
														     data-step="<?php echo $block->position['left']; ?>"><?php esc_html_e( 'left', 'pixtypes' ); ?></div>
													</div>
													<div class="position__ui-cell middle <?php echo $middle_status; ?>">
														<div class="position__ui-handle">middle</div>
													</div>
													<div
														class="position__ui-cell right <?php echo '0' === $block->position['right'] ? '' : 'active'; ?>">
														<div class="position__ui-handle"
														     data-step="<?php echo $block->position['right']; ?>"><?php esc_html_e( 'right', 'pixtypes' ); ?></div>
													</div>
												</div>
												<div class="position__ui-row">
													<div
														class="position__ui-cell bottom <?php echo '0' === $block->position['bottom'] ? '' : 'active'; ?>">
														<div class="position__ui-handle"
														     data-step="<?php echo $block->position['bottom']; ?>"><?php esc_html_e( 'bottom', 'pixtypes' ); ?></div>
													</div>
												</div>
											</div>
										</div>
									</li>
									<li class="remove remove_block">
										<span><?php esc_html_e( 'Remove', 'pixtypes' ); ?></span></li>
									<li class="move drag_handler"></li>
								</ul>
							</div>
							<div class="item__content block_content <?php echo $empty_class; ?>">
								<?php echo $content ?>
							</div>
						</li>
						<?php
					}
				}
			} ?>
		</ul>
	</div>
</div>
<?php add_action( 'admin_footer', 'my_admin_footer_function' );
function my_admin_footer_function() { ?>
	<div class="pix_builder_editor_modal_container" style="display:none">
		<div class="modal_wrapper">
			<div class="media-modal wp-core-ui">
				<a class="media-modal-close close_modal_btn" href="#" title="Close"><span
						class="media-modal-icon"></span></a>
				<!--                <a class="close_modal_btn media-modal-close" href="#"></a>-->
				<div class="media-modal-content">
					<div class="media-frame-title"><h1><?php esc_html_e( 'Insert Content', 'pixtypes' ); ?></h1></div>
					<div class="media-frame-router"></div>
					<div class="media-frame-content">
						<?php
						function pix_builder_change_mce_options( $initArray ) {

							$initArray['verify_html']          = false;
							$initArray['cleanup_on_startup']   = false;
							$initArray['cleanup']              = false;
							$initArray['forced_root_block']    = false;
							$initArray['validate_children']    = false;
							$initArray['remove_redundant_brs'] = false;
							$initArray['remove_linebreaks']    = false;
							$initArray['force_p_newlines']     = true;
							$initArray['force_br_newlines']    = true;
							$initArray['fix_table_elements']   = false;
							$initArray['convert_urls']   = false;
							$initArray['relative_urls']   = false;
							$initArray['entity_encoding']      = "raw";

							$initArray['entities'] = '160,nbsp,38,amp,60,lt,62,gt';

							return $initArray;
						}

						add_filter( 'tiny_mce_before_init', 'pix_builder_change_mce_options' );

						wp_editor( '', 'pix_builder_editor', array( 'textarea_rows' => 20, 'editor_height' => 350 ) );

						remove_filter( 'tiny_mce_before_init', 'pix_builder_change_mce_options' );
						?>
					</div>
					<div class="modal_controls media-frame-toolbar">
						<a class="close_modal_btn button button-large" href="#"><?php esc_html_e( 'Cancel', 'pixtypes' ) ?></a>
						<a class="insert_editor_content button media-button button-primary button-large"
						   href="#"><?php esc_html_e( 'Insert Content', 'pixtypes' ); ?></a>
					</div>
				</div>
			</div>
			<div class="media-modal-backdrop close_modal_btn"></div>
		</div>
	</div>
<?php }

add_filter('_wp_post_revision_field_post_content', 'testtt', 15, 4 );

function testtt ( $compare_to_field, $field, $compare_to, $target ){
	$parsed = json_decode( $compare_to_field, true );
	$change = false;
	if ( ! empty( $parsed ) && is_array( $parsed ) ) {
		$preview_content = '';
		foreach ( $parsed as $key => $line ) {
			if ( isset( $line['type'] ) && isset( $line['content'] ) && ! empty($line['content']) && $line['type']=== 'editor') {

				$new_link = base64_decode(  $line['content'] );

				if ( ! empty( $new_link ) ) {
					$change = true;
					$parsed[$key]['content'] = htmlspecialchars( $new_link ) ;
				}
			}
		}
	}

	if ( $change ) {
		$compare_to_field = json_encode( $parsed );
	}

	return $compare_to_field;
}

function pix_builder_display_content( $content = '', $decode = true ) {

	if ( $decode && ! empty( $content ) ) {
		$content = base64_decode($content);
	}
	// since we cannot apply "the_content" filter on some content blocks we should apply at least these bellow
	$content = apply_filters( 'wptexturize', $content );
	$content = apply_filters( 'convert_smilies', $content );
	$content = apply_filters( 'convert_chars', $content );

	$content = wpautop( $content );

	if( function_exists( 'wpgrade_remove_spaces_around_shortcodes' ) ) {
		$content = wpgrade_remove_spaces_around_shortcodes( $content );
	}
	//	$content = shortcode_unautop ($content);
	$content = apply_filters( 'prepend_attachment', $content );

	// in case there is a shortcode
//	return nl2br( $content );
//	return do_shortcode( $content );
	return $content;
}