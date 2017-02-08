<?php
/*
Script Name: 	Custom Metaboxes and Fields
Contributors: 	Andrew Norcross (@norcross / andrewnorcross.com)
				Jared Atchison (@jaredatch / jaredatchison.com)
				Bill Erickson (@billerickson / billerickson.net)
				Justin Sternberg (@jtsternberg / dsgnwrks.pro)
Description: 	This will create metaboxes with custom fields that will blow your mind.
Version: 		0.9.2
*/

/**
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 * This is an add-on for WordPress
 * http://wordpress.org/
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

/************************************************************************
 * You should not edit the code below or things might explode!
 *************************************************************************/

$meta_boxes = array();
$meta_boxes = apply_filters( 'cmb_meta_boxes', $meta_boxes );
if ( is_array( $meta_boxes ) && ! empty( $meta_boxes ) ) {
	foreach ( $meta_boxes as $meta_box ) {
		$my_box = new cmb_Meta_Box( $meta_box );
	}
}


//function ajax_update_metaboxes() {
//	global $wp_meta_boxes;
//	$meta_boxes = array();
//	$meta_boxes = apply_filters( 'cmb_meta_boxes', $meta_boxes );
//	if ( is_array( $meta_boxes ) && ! empty( $meta_boxes ) ) {
//		foreach ( $meta_boxes as $meta_box ) {
//			$my_box = new cmb_Meta_Box( $meta_box, $ajax = true );
//		}
//	}
//
//	if ( isset( $_REQUEST['post_ID'] ) ) {
//		global $post;
//		$post = get_post( $_REQUEST['post_ID'] );
//		ob_start();
//		do_meta_boxes( 'page', 'normal', null );
//		$metaboxes = ob_get_clean();
//		wp_send_json( array(
//			'metaboxes' => $metaboxes
//		) );
//	}
//	die();
//}

//add_action( 'wp_ajax_ajax_update_metaboxes', 'ajax_update_metaboxes' );

/**
 * Validate value of meta fields
 * Define ALL validation methods inside this class and use the names of these
 * methods in the definition of meta boxes (key 'validate_func' of each field)
 */
class cmb_Meta_Box_Validate {
	function check_text( $text ) {
		if ( $text != 'hello' ) {
			return false;
		}

		return true;
	}
}

/**
 * Defines the url to which is used to load local resources.
 * This may need to be filtered for local Window installations.
 * If resources do not load, please check the wiki for details.
 */
if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
	//winblows
	define( 'CMB_META_BOX_URL', trailingslashit( str_replace( DIRECTORY_SEPARATOR, '/', str_replace( str_replace( '/', DIRECTORY_SEPARATOR, WP_CONTENT_DIR ), content_url(), dirname( __FILE__ ) ) ) ) );

} else {
	define( 'CMB_META_BOX_URL', apply_filters( 'cmb_meta_box_url', trailingslashit( str_replace( WP_CONTENT_DIR, content_url(), dirname( __FILE__ ) ) ) ) );
}

/**
 * Create meta boxes
 */
class cmb_Meta_Box {
	protected $_meta_box;

	function __construct( $meta_box, $ajax_call = false ) {
		if ( ! is_admin() ) {
			return;
		}

		$this->_meta_box = $meta_box;

		$upload = false;
		foreach ( $meta_box['fields'] as $field ) {
			if ( $field['type'] == 'file' || $field['type'] == 'file_list' ) {
				$upload = true;
				break;
			}
		}

		global $pagenow;
		if ( $upload && in_array( $pagenow, array( 'page.php', 'page-new.php', 'post.php', 'post-new.php' ) ) ) {
			add_action( 'admin_head', array( &$this, 'add_post_enctype' ) );
		}

		if ( $ajax_call ) {
			$this->add();
		} else {
			add_action( 'admin_menu', array( &$this, 'add' ) );
		}

		add_action( 'save_post', array( &$this, 'save' ) );

		add_action( 'admin_head', array( &$this, 'fold_display' ) );

		add_filter( 'cmb_show_on', array( &$this, 'add_for_id' ), 10, 2 );
		//add_filter( 'cmb_show_on', array( &$this, 'add_for_page_template' ), 10, 2 );
		//add_filter( 'cmb_show_on', array( &$this, 'add_for_specific_select_value' ), 10, 2 );

		//add_filter('_wp_post_revision_field_post_content', array( $this, 'pixtypes_fix_builder_revisions_display'), 915, 4 );
		add_filter('default_hidden_meta_boxes', array( $this, 'hide_metaboxes_from_screen_options_by_config'), 15, 2 );
	}

	function add_post_enctype() {
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
	}

	function hide_metaboxes_from_screen_options_by_config ( $hidden, $screen ){
		foreach ( $this->_meta_box['pages'] as $page ) {
			if ( apply_filters( 'cmb_show_on', true, $this->_meta_box ) ) {
				if ( isset( $this->_meta_box['hidden'] ) && true === $this->_meta_box['hidden'] ) {
					$hidden[]=$this->_meta_box['id'];
				}
			}
		}
		return $hidden;
	}

	// Add metaboxes
	function add() {
		$this->_meta_box['context']  = empty( $this->_meta_box['context'] ) ? 'normal' : $this->_meta_box['context'];
		$this->_meta_box['priority'] = empty( $this->_meta_box['priority'] ) ? 'high' : $this->_meta_box['priority'];
		$this->_meta_box['show_on']  = empty( $this->_meta_box['show_on'] ) ? array(
			'key'   => false,
			'value' => false
		) : $this->_meta_box['show_on'];

		foreach ( $this->_meta_box['pages'] as $page ) {
			if ( apply_filters( 'cmb_show_on', true, $this->_meta_box ) ) {
				add_meta_box(
					$this->_meta_box['id'],
					$this->_meta_box['title'],
					array( &$this, 'show' ),
					$page,
					$this->_meta_box['context'],
					$this->_meta_box['priority']
				);
			}
		}
	}

	/**
	 * Show On Filters
	 * Use the 'cmb_show_on' filter to further refine the conditions under which a metabox is displayed.
	 * Below you can limit it by ID and page template
	 */

	// Add for ID
	function add_for_id( $display, $meta_box ) {
		if ( ! isset( $meta_box['show_on']['key'] ) || 'id' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		// If we're showing it based on ID, get the current ID
		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = $_POST['post_ID'];
		}
		if ( ! isset( $post_id ) ) {
			return false;
		}

		// If value isn't an array, turn it into one
		$meta_box['show_on']['value'] = ! is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// If current page id is in the included array, display the metabox

		if ( in_array( $post_id, $meta_box['show_on']['value'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	// Add for Page Template
	function add_for_page_template( $display, $meta_box ) {
		if ( 'page-template' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		// Get the current ID
		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = $_POST['post_ID'];
		}
		if ( ! ( isset( $post_id ) || is_page() ) ) {
			return false;
		}

		// if we are on an ajax request get the new template
		if ( isset( $_REQUEST['new_page_template'] ) && ! empty( $_REQUEST['new_page_template'] ) ) {
			$current_template = $_REQUEST['new_page_template'];
		} else {
			// Get current template
			$current_template = get_post_meta( $post_id, '_wp_page_template', true );
		}

		// If value isn't an array, turn it into one
		$meta_box['show_on']['value'] = ! is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// See if there's a match
		if ( in_array( $current_template, $meta_box['show_on']['value'] ) ) {
			return true;
		} else {
			return false;
		}
	}

	function add_for_specific_select_value( $display, $meta_box ) {

		// Get the current ID
		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = $_POST['post_ID'];
		}

		if ( ! ( isset( $post_id ) || is_page() ) ) {
			return true;
		}

		if ( isset( $meta_box['display_on'] ) && isset( $meta_box['display_on']['display'] ) ) {

			if ( $meta_box['display_on']['display'] ) {
				$show = true;
			} else {
				$show = false;
			}

			$display_on = $meta_box['display_on'];

			if ( isset( $display_on['on'] ) ) {
				if ( isset( $display_on['on']['field'] ) && isset( $display_on['on']['value'] ) ) {

					$metakey   = $display_on['on']['field'];
					$metavalue = $display_on['on']['value'];

					// if we are on an ajax request get the new metafield current value
					if ( isset( $_REQUEST['new_metafield_value'] ) && ! empty( $_REQUEST['new_metafield_value'] ) ) {
						$current_value = $_REQUEST['new_metafield_value'];
					} else {
						// Get current meta value
						$current_value = get_post_meta( $post_id, $metakey, true );
					}

					$value_test = false;

					if ( is_array( $metavalue ) && in_array( $current_value, $metavalue ) ) {
						$value_test = true;
					} elseif ( $metavalue == $current_value ) {
						$value_test = true;
					}

					if ( $value_test ) {
						if ( $show ) {
							return $display;
						} else {
							return false;
						}
					} else { // opposite
						if ( ! $show ) {
							return $display;
						} else {
							return false;
						}
					}

				}
			}
		} else {
			return $display;
		}
	}

	function pixtypes_fix_builder_revisions_display (  $post_content, $field, $compare_to, $target ){

		$change = false;
		$parsed = json_decode( $post_content, true );

		if ( empty( $parsed ) || ! is_array( $parsed ) ) return $post_content;

		foreach ( $parsed as $key => $block ) {
			if ( isset( $block['type'] ) && isset( $block['content'] ) && ! empty($block['content']) && $block['type']=== 'editor') {
				$new_link = base64_decode( $block['content'] );
				$change = true;
				$parsed[$key]['content'] = $new_link;
			}
		}

		if ( $change ) {
			return json_encode( $parsed );
		}
		return $post_content;
	}

	// Show fields
	function show() {

		global $post;

		if ( isset( $this->_meta_box['show_on'] ) ) {

		    // Allow others to filter the show_on value on the fly
            $show_on = apply_filters( 'pixtypes_cmb_metabox_show_on', $this->_meta_box['show_on'], $this->_meta_box );

            if ( ! empty( $show_on ) ) {

	            $data_key = '';
	            if ( isset( $show_on['key'] ) && ! empty( $show_on['key'] ) ) {
		            $data_key = ' data-key="' . $show_on['key'] . '"';
	            }

	            $data_value = '';
	            if ( isset( $show_on['value'] ) && ! empty( $show_on['value'] ) ) {
		            $data_value = ' data-value=\'' . json_encode( $show_on['value'] ) . '\'';
	            }

	            $data_hide = '';
	            if ( isset( $show_on['hide'] ) && ! empty( $show_on['hide'] ) ) {
		            $data_hide = ' data-hide=\'' . json_encode( $show_on['hide'] ) . '\'';
	            }

	            echo '<input type="hidden" class="show_metabox_on" ' . $data_value . $data_key . $data_hide . ' />';
            }
		}

		// Use nonce for verification
		echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

		// load assets only when we have a metabox on page
		cmb_enqueue_scripts();

		echo '<ul class="form-table cmb_metabox">';

		// we use the variable to check if we need a default or not
		$all_metas = get_post_meta( $post->ID );

		foreach ( $this->_meta_box['fields'] as $field ) {

			//some extra classes
			$classes = 'cmb-type cmb-type-' . sanitize_html_class( $field['type'] );

			// Set up blank or default values for empty ones
			if ( ! isset( $field['name'] ) ) {
				$field['name'] = '';
			}
			if ( ! isset( $field['show_names'] ) ) {
				$field['show_names'] = true;
			}
			if ( ! isset( $field['desc'] ) ) {
				$field['desc'] = '';
			}
			if ( ! isset( $field['std'] ) ) {
				$field['std'] = '';
			}
			if ( 'file' == $field['type'] && ! isset( $field['allow'] ) ) {
				$field['allow'] = array( 'url', 'attachment' );
			}
			if ( 'file' == $field['type'] && ! isset( $field['save_id'] ) ) {
				$field['save_id'] = false;
			}
			if ( 'multicheck' == $field['type'] ) {
				$field['multiple'] = true;
			}

			$meta = get_post_meta( $post->ID, $field['id'], 'multicheck' != $field['type'] /* If multicheck this can be multiple values */ );

			$meta_exists = false;
			if ( isset( $all_metas[ $field['id'] ] ) ) {
				$meta_exists = true;
			}

			if ( isset( $field['options'] ) && isset( $field['options']['hidden'] ) && $field['options']['hidden'] == true ) {
				echo '<li style="display:none;">';
			} else {

				$requires = '';
				if ( isset( $field['display_on'] ) ) {

					$classes .= ' display_on';

					$display_on = $field['display_on'];

					if ( isset( $display_on['display'] ) && ! empty( $display_on['display'] ) ) {
						$requires .= ' data-action="show"';
					} else {
						$requires .= ' data-action="hide" style="display:none;"';
					}

					if ( isset( $display_on['on'] ) && is_array( $display_on['on'] ) ) {

						$on = $display_on['on'];

						$requires .= 'data-when_key="' . $on['field'] . '"';

						if ( is_array( $on['value'] ) ) {
							$requires .= 'data-has_value=\'' . json_encode( $on['value'] ) . '\'';
						} else {
							$requires .= 'data-has_value="' . $on['value'] . '"';
						}
					}
				}

				echo '<li class="' . $classes . '" ' . $requires . '>';
			}

			echo '<div class="cmb_metabox_description">';
			if ( ! ( $field['type'] == 'portfolio-gallery' || $field['type'] == 'gallery' || $field['type'] == 'pix_builder' || $field['type'] == 'gmap_pins' ) ) {
				if ( isset( $this->_meta_box['show_names'] ) && $this->_meta_box['show_names'] == true ) {
					if ( isset( $field['show_names'] ) && $field['show_names'] == true ) {
						echo '<h3><label for="', $field['id'], '">', $field['name'], '</label></h3>';
					}
				}
			}
			if ( ! empty($field['desc']) ) {
				echo "<div>" . $field['desc'] . "</div>";
			}
			echo '</div>';


			if ( ! $meta_exists && isset( $field['std'] ) ) {
				$meta = $field['std'];
			}

			switch ( $field['type'] ) {

				case 'text':
					echo '<input class="cmb_text" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta, '" />';
					break;
				case 'text_small':
					echo '<input class="cmb_text cmb_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta, '" />';
					break;
				case 'text_medium':
					echo '<input class="cmb_text cmb_text_medium" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta, '" />';
					break;

				case 'text_range':
					$atts = '';

					if ( isset( $field['html_args'] ) && ! empty( $field['html_args'] ) ) {
						foreach ( $field['html_args'] as $key => $att ) {
							$atts .= $key . '="' . $att . '" ';
						}
					} ?>
					<input class="cmb_text_range" type="range" name="<?php echo $field['id']; ?>"
					       id="<?php echo $field['id'] ?>"
					       value="<?php echo '' !== $meta ? $meta : $field['std']; ?>" <?php echo $atts ?>
					       style="background-size: <?php echo 0 !== $meta ? $meta : $field['std']; ?>% 100%;"
					       oninput="<?php echo $field['id'] . '_output.value = ' . $field['id'] . '.value'; ?>"/>
					<output name="<?php echo $field['id'] ?>_output" id="<?php echo $field['id']; ?>_output">
						<?php echo '' !== $meta ? $meta : $field['std']; ?>
					</output>
					<?php break;
				case 'text_date':
					echo '<input class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" />';
					break;
				case 'text_date_timestamp':
					echo '<input class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? date( 'm\/d\/Y', $meta ) : $field['std'], '" />';
					break;

				case 'text_datetime_timestamp':
					echo '<input class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '[date]" id="', $field['id'], '_date" value="', '' !== $meta ? date( 'm\/d\/Y', $meta ) : $field['std'], '" />';
					echo '<input class="cmb_timepicker text_time" type="text" name="', $field['id'], '[time]" id="', $field['id'], '_time" value="', '' !== $meta ? date( 'h:i A', $meta ) : $field['std'], '" />';
					break;
				case 'text_time':
					echo '<input class="cmb_timepicker text_time" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" />';
					break;
				case 'text_money':
					echo '$ <input class="cmb_text_money" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" />';
					break;
				case 'colorpicker':
					$meta = '' !== $meta ? $meta : $field['std'];

					$hex_color = '(([a-fA-F0-9]){3}){1,2}$';
					if ( preg_match( '/^' . $hex_color . '/i', $meta ) ) // Value is just 123abc, so prepend #.
					{
						$meta = '#' . $meta;
					} elseif ( ! preg_match( '/^#' . $hex_color . '/i', $meta ) ) // Value doesn't match #123abc, so sanitize to just #.
					{
						$meta = "#";
					}
					echo '<input class="cmb_colorpicker cmb_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta, '" />';
					break;
				case 'textarea':
					echo '<textarea class="cmb_textarea" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10">', $meta, '</textarea>';
					break;
				case 'textarea_small':
					echo '<textarea class="cmb_textarea" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4">', $meta, '</textarea>';
					break;
				case 'textarea_code':
					$rows = $cols = '';
					if( isset( $field['rows'] ) && ! empty( $field['rows'] ) ) {
						$rows =  'rows="' . $field['rows'] . '"';
					}

					if( isset( $field['cols'] ) && ! empty( $field['cols'] ) ) {
						$cols = 'cols="' . $field['cols'] . '"';
					} else {
						$cols = 'style="width: 100%"';
					}

					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" ' . $cols .' ' . $rows . ' class="cmb_textarea cmb_textarea_code">', $meta, '</textarea>';
					break;
				case 'select':
					//we DON'T consider the '0' string as empty, nor do we consider (int)0 as empty
					if ( ( empty( $meta ) && ! $meta === 0 && ! $meta === '0' ) && ! empty( $field['std'] ) ) {
						$meta = $field['std'];
					}

					echo '<div class="selector-wrapper dashicons-before dashicons-arrow-down-alt2">';
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';

					foreach ( $field['options'] as $option ) {
						//this an edge case when using booleans as values (ie true and false)
						//the problem is that true is cast to 1 but false is cast to empty string
						//this doesn't help us much in setting the value to false
						//so we replace false with 0 since when testing
						if ( $option['value'] === false ) {
							$option['value'] = 0;
						}
						echo '<option value="', $option['value'], '"', $meta == $option['value'] ? ' selected="selected"' : '', '>', $option['name'], '</option>';
					}
					echo '</select>';
					echo '</div>';

					break;
				case 'select_cpt_post':

					echo '<div class="selector-wrapper dashicons-before dashicons-arrow-down-alt2">';
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					$args = array(
						'posts_per_page' => - 1,
					);
					global $post;
					$old_post  = $post;
					$args      = array_merge( $args, $field['options']['args'] );
					$cpt_posts = get_posts( $args );
					if ( ! empty( $cpt_posts ) ) {
						foreach ( $cpt_posts as $post ) {
							echo '<option value="', $post->ID, '"', $meta == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
						}
					}
					$post = $old_post;
					echo '</select>';
					echo '</div>';
					break;
				case 'select_cpt_term':
					echo '<div class="selector-wrapper dashicons-before dashicons-arrow-down-alt2">';
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					$cpt_terms = get_terms( $field['taxonomy'], 'orderby=count&hide_empty=0' );
					if ( ! empty( $cpt_terms ) ) {
						foreach ( $cpt_terms as $term ) {
							echo '<option value="', $term->slug, '"', $meta == $term->slug ? ' selected="selected"' : '', '>', $term->name, '</option>';
						}
					}
					echo '</select>';
					echo '</div>';
					break;
				case 'radio_inline':
					if ( empty( $meta ) && ! empty( $field['std'] ) ) {
						$meta = $field['std'];
					}
					echo '<div class="cmb_radio_inline">';
					$i = 1;
					foreach ( $field['options'] as $option ) {
						echo '<div class="cmb_radio_inline_option"><input type="radio" name="', $field['id'], '" id="', $field['id'], $i, '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', $option['name'], '</label></div>';
						$i ++;
					}
					echo '</div>';
					break;
				case 'radio':
					if ( empty( $meta ) && ! empty( $field['std'] ) ) {
						$meta = $field['std'];
					}
					echo '<ul>';
					$i = 1;
					foreach ( $field['options'] as $option ) {
						echo '<li><input type="radio" name="', $field['id'], '" id="', $field['id'], $i, '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', $option['name'] . '</label></li>';
						$i ++;
					}
					echo '</ul>';
					break;
				case 'checkbox':
					echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', ( $meta === 'on' ) ? ' checked="checked"' : '', ' />';
					break;
				case 'multicheck':
					//even if empty, we should check for the meta key existance - empty is a valid value, it means all the checkboxes have been unchecked
					if ( empty( $meta ) && ! metadata_exists( 'post', $post->ID, $field['id'] ) && ! empty( $field['std'] ) ) {
						$meta = $field['std'];
					}
					echo '<ul>';
					$i = 1;
					foreach ( $field['options'] as $value => $name ) {
						// Append `[]` to the name to get multiple values
						// Use in_array() to check whether the current option should be checked
						echo '<li><input type="checkbox" name="', $field['id'], '[]" id="', $field['id'], $i, '" value="', $value, '"', in_array( $value, $meta ) ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', $name, '</label></li>';
						$i ++;
					}
					echo '</ul>';
					break;
				case 'title':
					if ( isset( $field['value']) ) {
						echo '<div class="cmb_metabox_title" id="', $field['id'], '">', $field['value'], '</div>';
					}
					break;
				case 'wysiwyg':
					wp_editor( $meta, $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
					break;
				case 'taxonomy_select':

					echo '<div class="selector-wrapper dashicons-before dashicons-arrow-down-alt2">';
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					$names = wp_get_object_terms( $post->ID, $field['taxonomy'] );
					$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
					foreach ( $terms as $term ) {
						if ( ! is_wp_error( $names ) && ! empty( $names ) && ! strcmp( $term->slug, $names[0]->slug ) ) {
							echo '<option value="' . $term->slug . '" selected>' . $term->name . '</option>';
						} else {
							echo '<option value="' . $term->slug . '  ', $meta == $term->slug ? $meta : ' ', '  ">' . $term->name . '</option>';
						}
					}
					echo '</select>';
					echo '</div>';
					break;
				case 'taxonomy_radio':
					$names = wp_get_object_terms( $post->ID, $field['taxonomy'] );
					$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
					echo '<ul>';
					foreach ( $terms as $term ) {
						if ( ! is_wp_error( $names ) && ! empty( $names ) && ! strcmp( $term->slug, $names[0]->slug ) ) {
							echo '<li><input type="radio" name="', $field['id'], '" value="' . $term->slug . '" checked>' . $term->name . '</li>';
						} else {
							echo '<li><input type="radio" name="', $field['id'], '" value="' . $term->slug . '  ', $meta == $term->slug ? $meta : ' ', '  ">' . $term->name . '</li>';
						}
					}
					echo '</ul>';
					break;
				case 'taxonomy_multicheck':
					echo '<ul>';
					$names = wp_get_object_terms( $post->ID, $field['taxonomy'] );
					$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
					foreach ( $terms as $term ) {
						echo '<li><input type="checkbox" name="', $field['id'], '[]" id="', $field['id'], '" value="', $term->name, '"';
						foreach ( $names as $name ) {
							if ( $term->slug == $name->slug ) {
								echo ' checked="checked" ';
							};
						}
						echo ' /><label>', $term->name, '</label></li>';
					}
					echo '</ul>';
					break;
				case 'file_list':
					echo '<input class="cmb_upload_file" type="text" size="36" name="', $field['id'], '" value="" />';
					echo '<input class="cmb_upload_button button" type="button" value="Upload File" />';
					$args        = array(
						'post_type'   => 'attachment',
						'numberposts' => null,
						'post_status' => null,
						'post_parent' => $post->ID
					);
					$attachments = get_posts( $args );
					if ( $attachments ) {
						echo '<ul class="attach_list">';
						foreach ( $attachments as $attachment ) {
							echo '<li>' . wp_get_attachment_link( $attachment->ID, 'thumbnail', 0, 0, 'Download' );
							echo '<span>';
							echo apply_filters( 'the_title', '&nbsp;' . $attachment->post_title );
							echo '</span></li>';
						}
						echo '</ul>';
					}
					break;
				case 'file':
					$input_type_url = "hidden";
					if ( 'url' == $field['allow'] || ( is_array( $field['allow'] ) && in_array( 'url', $field['allow'] ) ) ) {
						$input_type_url = "text";
					}
					echo '<input class="cmb_upload_file" type="' . $input_type_url . '" size="45" id="', $field['id'], '" name="', $field['id'], '" value="', $meta, '" />';
					echo '<input class="cmb_upload_button button" type="button" value="Upload File" />';
					echo '<input class="cmb_upload_file_id" type="hidden" id="', $field['id'], '_id" name="', $field['id'], '_id" value="', get_post_meta( $post->ID, $field['id'] . "_id", true ), '" />';
					echo '<div id="', $field['id'], '_status" class="cmb_media_status">';
					if ( $meta != '' ) {
						$check_image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $meta );
						if ( $check_image ) {
							echo '<div class="img_status">';
							echo '<img src="', $meta, '" alt="" />';
							echo '<a href="#" class="cmb_remove_file_button" rel="', $field['id'], '">Remove Image</a>';
							echo '</div>';
						} else {
							$parts = explode( '/', $meta );
							for ( $i = 0; $i < count( $parts ); ++ $i ) {
								$title = $parts[ $i ];
							}
							echo 'File: <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $meta, '" target="_blank" rel="external">Download</a> / <a href="#" class="cmb_remove_file_button" rel="', $field['id'], '">Remove</a>)';
						}
					}
					echo '</div>';
					break;
				case 'attachment':
					$input_type_url = "hidden";

					if ( isset( $field['allow'] ) && ( 'url' == $field['allow'] || ( is_array( $field['allow'] ) && in_array( 'url', $field['allow'] ) ) ) ) {
						$input_type_url = "text";
					}
					echo '<input class="cmb_upload_file attachment" type="' . $input_type_url . '" size="45" id="', $field['id'], '" name="', $field['id'], '" value=\'', $meta, '\' />';
					echo '<input class="cmb_upload_button button" type="button" value="Upload File" />';
					echo '<input class="cmb_upload_file_id" type="hidden" id="', $field['id'], '_id" name="', $field['id'], '_id" value="', get_post_meta( $post->ID, $field['id'] . "_id", true ), '" />';
					echo '<div id="', $field['id'], '_status" class="cmb_media_status">';
					if ( $meta != '' ) {
						$check_image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $meta );
						if ( $check_image ) {
							echo '<div class="img_status">';
							$meta_img = (array) json_decode( $meta );
							echo '<img src="' . $meta_img["link"] . '" alt="" />';
							echo '<a href="#" class="cmb_remove_file_button" rel="', $field['id'], '">Remove Image</a>';
							echo '</div>';
						} else {
							$parts = explode( '/', $meta );
							for ( $i = 0; $i < count( $parts ); ++ $i ) {
								$title = $parts[ $i ];
							}
							echo 'File: <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $meta, '" target="_blank" rel="external">Download</a> / <a href="#" class="cmb_remove_file_button" rel="', $field['id'], '">Remove</a>)';
						}
					}
					echo '</div>';
					break;
				case 'portfolio-gallery':

					$file_path = plugin_basename( __FILE__ ) . 'fields/portfolio-gallery.php';
					if ( file_exists( $file_path ) ) {
						ob_start();
						include( $file_path );
						echo ob_get_clean();
					}

					break;
				case 'gallery':

					$file_path = plugin_dir_path( __FILE__ ) . 'fields/gallery.php';
					if ( file_exists( $file_path ) ) {
						ob_start();
						include( $file_path );
						echo ob_get_clean();
					}

					break;

				case 'image':

					$file_path = plugin_dir_path( __FILE__ ) . 'fields/image.php';
					if ( file_exists( $file_path ) ) {
						ob_start();
						include( $file_path );
						echo ob_get_clean();
					}

					break;

				case 'playlist':

					$playlist_type = 'video';
					if ( isset( $field['playlist_type'] ) && ! empty( $field['playlist_type'] ) ) {
						$playlist_type = $field['playlist_type'];
					}

					$file_path = plugin_dir_path( __FILE__ ) . 'fields/playlist.php';
					if ( file_exists( $file_path ) ) {
						ob_start();
						include( $file_path );
						echo ob_get_clean();
					}

					break;

				case 'pix_builder':

					wp_enqueue_script( 'pix_builder' );
					wp_enqueue_style( 'pix_builder' );


					$file_path = plugin_dir_path( __FILE__ ) . 'fields/pix_builder.php';
					if ( file_exists( $file_path ) ) {
						ob_start();
						include( $file_path );
						echo ob_get_clean();
					} else {
						echo '<p>Wrong path </p>';
					}

					break;

				case 'gmap_pins':

					$file_path = plugin_dir_path( __FILE__ ) . 'fields/gmap_pins.php';
					if ( file_exists( $file_path ) ) {
						ob_start();
						include( $file_path );
						echo ob_get_clean();
					} else {
						echo '<p>Wrong path </p>';
					}

					break;

				case 'oembed':
					echo '<input class="cmb_oembed" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', '' !== $meta ? $meta : $field['std'], '" />';
					echo '<p class="cmb-spinner spinner"></p>';
					echo '<div id="', $field['id'], '_status" class="cmb_media_status ui-helper-clearfix embed_wrap">';
					if ( $meta != '' ) {
						$check_embed = $GLOBALS['wp_embed']->run_shortcode( '[embed]' . esc_url( $meta ) . '[/embed]' );
						if ( $check_embed ) {
							echo '<div class="embed_status">';
							echo $check_embed;
							echo '<a href="#" class="cmb_remove_file_button" rel="', $field['id'], '">Remove Embed</a>';
							echo '</div>';
						} else {
							echo 'URL is not a valid oEmbed URL.';
						}
					}
					echo '</div>';
					break;

				case 'positions_map':
					if ( empty( $meta ) && ! empty( $field['std'] ) ) {
						$meta = $field['std'];
					}
					echo '<ul class="positions_map">';
					$i = 1;
					foreach ( $field['options'] as $option ) {
						echo '<li><input type="radio" name="', $field['id'], '" id="', $field['id'], $i, '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /><label for="', $field['id'], $i, '">', '<span>' . $option['value'] . '</span>' . '</label></li>';
						$i ++;
					}
					echo '</ul>';
					break;

				default:
					do_action( 'cmb_render_' . $field['type'], $field, $meta );
			}

			echo '</li>';
		}
		echo '</ul>';
	}

	function fold_display() {
		// some special care for the pix_builder type
		// when this type is on the page we need to make sure that the editor is supported but folded by default
		if ( ! empty( $this->_meta_box['fields'] ) ) {
			foreach ( $this->_meta_box['fields'] as $field ) {
				if ( 'pix_builder' === $field['type'] ) {
					if ( ! isset( $this->_meta_box['pages'] ) || empty( $this->_meta_box['pages'] ) ) {
						continue;
					}
					foreach ( $this->_meta_box['pages'] as $post_type ) {
						add_post_type_support( $post_type, 'editor' );
					}
					break;
				}
			}
		}

		if ( ! isset( $this->_meta_box['display_on'] ) ) {
			return;
		}

		if ( $this->_meta_box['display_on']['display'] ) {
			$show = true;
		} else {
			$show = false;
		}

		$display_on = $this->_meta_box['display_on'];
		ob_start(); ?>
		<script>
			;
			(function ($) {
				$(document).ready(function () {
					var metabox = $('#<?php echo $this->_meta_box['id'];  ?>');
					metabox.addClass('display_on')
						.attr('data-action', '<?php echo 'show'; ?>')
						.attr('data-when_key', '<?php echo $display_on['on']['field']; ?>')
						.attr('data-has_value', '<?php echo $display_on['on']['value']; ?>');
				});
			})(jQuery);
		</script>
		<?php
		$script = ob_get_clean();
		echo( $script );
	}

	// Save data from metabox
	function save( $post_id ) {

		// verify nonce
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// get the post types applied to the metabox group
		// and compare it to the post type of the content
		$post_type = get_post_type( $post_id );
		$meta_type = $this->_meta_box['pages'];
		$type_comp = in_array( $post_type, $meta_type ) ? true : false;

		foreach ( $this->_meta_box['fields'] as $field ) {
			$name = $field['id'];

			if ( ! isset( $field['multiple'] ) ) {
				$field['multiple'] = ( 'multicheck' == $field['type'] ) ? true : false;
			}

			$old = get_post_meta( $post_id, $name, ! $field['multiple'] /* If multicheck this can be multiple values */ );
			$new = isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : null;

			if ( $type_comp == true && in_array( $field['type'], array(
					'taxonomy_select',
					'taxonomy_radio',
					'taxonomy_multicheck'
				) )
			) {
				$new = wp_set_object_terms( $post_id, $new, $field['taxonomy'] );
			}

			if ( ( $field['type'] == 'textarea' ) || ( $field['type'] == 'textarea_small' ) ) {
				$new = htmlspecialchars( $new );
			}

			if ( ( $field['type'] == 'textarea_code' ) ) {
				$new = htmlspecialchars_decode( $new );
			}

			if ( ( $field['type'] == 'checkbox' ) ) {
				if ( empty( $new ) ) {
					$new = 'of';
				} else {
					//the value may also be 1 not on so lets uniformize things
					if ( $new == 1 ) {
						$new = 'on';
					}
				}
			}

			if ( $type_comp == true && $field['type'] == 'text_date_timestamp' ) {
				$new = strtotime( $new );
			}

			if ( $type_comp == true && $field['type'] == 'text_datetime_timestamp' ) {
				$string = $new['date'] . ' ' . $new['time'];
				$new    = strtotime( $string );
			}

			$new = apply_filters( 'cmb_validate_' . $field['type'], $new, $post_id, $field );

			// validate meta value
			if ( isset( $field['validate_func'] ) ) {
				$ok = call_user_func( array( 'cmb_Meta_Box_Validate', $field['validate_func'] ), $new );
				if ( $ok === false ) { // pass away when meta value is invalid
					continue;
				}
			} elseif ( $field['multiple'] ) {
				delete_post_meta( $post_id, $name );
				if ( ! empty( $new ) ) {
					foreach ( $new as $add_new ) {
						add_post_meta( $post_id, $name, $add_new, false );
					}
				}
			} elseif ( '' !== $new && $new != $old ) {
				update_post_meta( $post_id, $name, $new );
			} elseif ( '' == $new ) {
				update_post_meta( $post_id, $name, $new );
			}

			if ( 'file' == $field['type'] ) {
				$name = $field['id'] . "_id";
				$old  = get_post_meta( $post_id, $name, ! $field['multiple'] /* If multicheck this can be multiple values */ );
				if ( isset( $field['save_id'] ) && $field['save_id'] ) {
					$new = isset( $_POST[ $name ] ) ? $_POST[ $name ] : null;
				} else {
					$new = "";
				}

				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $name, $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $name, $old );
				}
			}

		}

	}
}

/**
 * Adding scripts and styles
 */
function cmb_register_scripts( $hook ) {

	global $pixtypes_plugin;
	$plugin_version = 0;
	if ( method_exists( $pixtypes_plugin, 'get_plugin_version' ) ) {
		$plugin_version = $pixtypes_plugin->get_plugin_version();
	}

	global $wp_version;
	// only enqueue our scripts/styles on the proper pages
	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
		// scripts required for cmb
		$cmb_script_array = array(
			'jquery',
			'jquery-ui-core',
			'jquery-ui-datepicker',
			'media-upload',
			'thickbox',
			'cmb-tooltipster'
		);
		// styles required for cmb
		$cmb_style_array = array( 'thickbox', 'tooltipster' );

		$cmb_script_array[] = 'wp-color-picker';
		$cmb_style_array[]  = 'wp-color-picker';

		wp_register_script( 'cmb-tooltipster', CMB_META_BOX_URL . 'js/jquery.tooltipster.min.js' );
		wp_register_script( 'cmb-timepicker', CMB_META_BOX_URL . 'js/jquery.timePicker.min.js' );
		wp_register_script( 'pixgallery', CMB_META_BOX_URL . 'js/pixgallery.js' );
		wp_register_script( 'piximage', CMB_META_BOX_URL . 'js/piximage.js' );
		wp_register_script( 'pixplaylist', CMB_META_BOX_URL . 'js/pixplaylist.js' );
		wp_register_script( 'gridster', CMB_META_BOX_URL . 'js/jquery.gridster.js' );
		wp_register_script( 'pix_builder', CMB_META_BOX_URL . 'js/pix_builder.js', array( 'gridster' ), $plugin_version );
		wp_localize_script( 'pix_builder', 'l18n_pix_builder', array(
			'set_image' => esc_html__( 'Set Image', 'pixtypes' ),
		) );
		wp_register_script( 'gmap_pins', CMB_META_BOX_URL . 'js/gmap_pins.js', array(), $plugin_version );

		wp_register_script( 'cmb-scripts', CMB_META_BOX_URL . 'js/cmb.js', $cmb_script_array, $plugin_version );
		wp_localize_script( 'cmb-scripts', 'cmb_ajax_data', array(
			'ajax_nonce' => wp_create_nonce( 'ajax_nonce' ),
			'post_id'    => get_the_ID(),
			'post_type'  => get_post_type()
		) );

		wp_register_style( 'gridster', CMB_META_BOX_URL . 'css/jquery.gridster.css' );

		wp_register_style( 'pix_builder', CMB_META_BOX_URL . 'css/pix_builder.css', array( 'gridster' ), $plugin_version );
		wp_register_style( 'tooltipster', CMB_META_BOX_URL . 'css/tooltipster.css' );
		wp_register_style( 'cmb-styles', CMB_META_BOX_URL . 'css/style.css', $cmb_style_array, $plugin_version );
	}
}

add_action( 'admin_enqueue_scripts', 'cmb_register_scripts', 10 );

function cmb_enqueue_scripts(){
	wp_enqueue_script( 'cmb-timepicker' );
	wp_enqueue_script( 'cmb-scripts' );
	wp_enqueue_style( 'cmb-styles' );
}

function cmb_editor_footer_scripts() {
	if ( isset( $_GET['cmb_force_send'] ) && 'true' == $_GET['cmb_force_send'] ) {
		$label = $_GET['cmb_send_label'];
		if ( empty( $label ) ) {
			$label = esc_html__( 'Select File', 'pixtypes' );
		} ?>
		<script type="text/javascript">
			jQuery(function ($) {
				$('td.savesend input').val('<?php echo $label; ?>');
			});
		</script>
		<?php
	}
}

add_action( 'admin_print_footer_scripts', 'cmb_editor_footer_scripts', 99 );

// Force 'Insert into Post' button from Media Library
add_filter( 'get_media_item_args', 'cmb_force_send' );
function cmb_force_send( $args ) {

	// if the Gallery tab is opened from a custom meta box field, add Insert Into Post button
	if ( isset( $_GET['cmb_force_send'] ) && 'true' == $_GET['cmb_force_send'] ) {
		$args['send'] = true;
	}

	// if the From Computer tab is opened AT ALL, add Insert Into Post button after an image is uploaded
	if ( isset( $_POST['attachment_id'] ) && '' != $_POST["attachment_id"] ) {

		$args['send'] = true;

		// TO DO: Are there any conditions in which we don't want the Insert Into Post
		// button added? For example, if a post type supports thumbnails, does not support
		// the editor, and does not have any cmb file inputs? If so, here's the first
		// bits of code needed to check all that.
		// $attachment_ancestors = get_post_ancestors( $_POST["attachment_id"] );
		// $attachment_parent_post_type = get_post_type( $attachment_ancestors[0] );
		// $post_type_object = get_post_type_object( $attachment_parent_post_type );
	}

	// change the label of the button on the From Computer tab
	if ( isset( $_POST['attachment_id'] ) && '' != $_POST["attachment_id"] ) {

		echo '
			<script type="text/javascript">
				function cmbGetParameterByNameInline(name) {
					name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
					var regexS = "[\\?&]" + name + "=([^&#]*)";
					var regex = new RegExp(regexS);
					var results = regex.exec(window.location.href);
					if(results == null)
						return "";
					else
						return decodeURIComponent(results[1].replace(/\+/g, " "));
				}

				jQuery(function($) {
					if (cmbGetParameterByNameInline("cmb_force_send")=="true") {
						var cmb_send_label = cmbGetParameterByNameInline("cmb_send_label");
						$("td.savesend input").val(cmb_send_label);
					}
				});
			</script>
		';
	}

	return $args;

}

add_action( 'wp_ajax_cmb_oembed_handler', 'cmb_oembed_ajax_results' );
/**
 * Handles our oEmbed ajax request
 */
function cmb_oembed_ajax_results() {

	// verify our nonce
	if ( ! ( isset( $_REQUEST['cmb_ajax_nonce'], $_REQUEST['oembed_url'] ) && wp_verify_nonce( $_REQUEST['cmb_ajax_nonce'], 'ajax_nonce' ) ) ) {
		die();
	}

	// sanitize our search string
	$oembed_string = sanitize_text_field( $_REQUEST['oembed_url'] );

	if ( empty( $oembed_string ) ) {
		$return = '<p class="ui-state-error-text">' . esc_html__( 'Please Try Again', 'pixtypes' ) . '</p>';
		$found  = 'not found';
	} else {

		global $wp_embed;

		$oembed_url = esc_url( $oembed_string );
		// Post ID is needed to check for embeds
		if ( isset( $_REQUEST['post_id'] ) ) {
			$GLOBALS['post'] = get_post( $_REQUEST['post_id'] );
		}
		// ping WordPress for an embed
		$check_embed = $wp_embed->run_shortcode( '[embed]' . $oembed_url . '[/embed]' );
		// fallback that WordPress creates when no oEmbed was found
		$fallback = $wp_embed->maybe_make_link( $oembed_url );

		if ( $check_embed && $check_embed != $fallback ) {
			// Embed data
			$return = '<div class="embed_status">' . $check_embed . '<a href="#" class="cmb_remove_file_button" rel="' . $_REQUEST['field_id'] . '">' . esc_html__( 'Remove Embed', 'pixtypes' ) . '</a></div>';
			// set our response id
			$found = 'found';

		} else {
			// error info when no oEmbeds were found
			$return = '<p class="ui-state-error-text">' . sprintf( esc_html__( 'No oEmbed Results Found for %s. View more info at', 'pixtypes' ), $fallback ) . ' <a href="http://codex.wordpress.org/Embeds" target="_blank">codex.wordpress.org/Embeds</a>.</p>';
			// set our response id
			$found = 'not found';
		}
	}

	// send back our encoded data
	echo json_encode( array( 'result' => $return, 'id' => $found ) );
	die();
}

// End. That's it, folks! //

// not yet ... let's ajaxify things around

// create an ajax call which will return a preview to the current gallery
function ajax_pixgallery_preview() {
	$result = array( 'success' => false, 'output' => '' );

	if ( isset( $_REQUEST['attachments_ids'] ) ) {
		$ids = $_REQUEST['attachments_ids'];
	}
	if ( empty( $ids ) ) {
		echo json_encode( $result );
		exit;
	}

	$ids = rtrim( $ids, ',' );
	$ids = explode( ',', $ids );

	$size = 'thumbnail';

	if ( count( $ids ) === 1 ) {
		$size = 'medium';
	}

	foreach ( $ids as $id ) {
		$attach = wp_get_attachment_image_src( $id, $size, false );

		$result["output"] .= '<li><img src="' . $attach[0] . '" /></li>';
	}
	$result["success"] = true;
	echo json_encode( $result );
	exit;
}

add_action( 'wp_ajax_ajax_pixgallery_preview', 'ajax_pixgallery_preview' );

function ajax_pixplaylist_preview() {

	if ( isset( $_REQUEST['attachments_ids'] ) ) {
		$ids = $_REQUEST['attachments_ids'];
	}

	if ( empty( $ids ) ) {
		wp_send_json_error( 'empty' );
		exit();
	}

	$ids = explode( ',', $ids );

	$result = '';
	foreach ( $ids as $id ) {
		$result .= '<li><span class="dashicons dashicons-format-video"></span><span class="attachment_title">' . get_the_title( $id ) . '</span></li>';
	}

	wp_send_json_success( $result );
	exit;
}

add_action( 'wp_ajax_pixplaylist_preview', 'ajax_pixplaylist_preview' );


/* ========== RELATED TO PIXBUILDER ======== */


/**
 * Add the builder meta to the revision fields (by default only post title, content and excerpt are accounted for)
 *
 * @param array $fields
 *
 * @return array
 */
function pixbuilder_add_revision_field( $fields ) {
	$fields['_pile_project_builder'] = 'Content Builder';

	return $fields;
}
add_filter( '_wp_post_revision_fields', 'pixbuilder_add_revision_field', 10, 1 );

/**
 * Overwrite the meta value if we have a global revision set
 *
 * @param mixed $value
 * @param string $field
 * @param WP_Post $revision
 * @param string $type It ca be either "from" or "to"; see wp-admin/includes/revision.php @73-76
 *
 * @return mixed
 */
function pixbuilder_revision_field_value( $value, $field, $revision, $type ) {
	if ( ! empty( $revision->ID ) ) {
		$value = get_metadata( 'post', $revision->ID, $field, true );
	}

	return $value;
}
add_filter( '_wp_post_revision_field__pile_project_builder', 'pixbuilder_revision_field_value', 10, 4 );

/**
 * @param int $post_ID     Post ID.
 * @param int $revision_ID Post revision ID.
 */
function pixbuilder_on_restore_revision( $post_ID, $revision_ID ) {
	//bail if something is fishy
	if ( empty( $post_ID ) || empty( $revision_ID ) ) {
		return;
	}

	$revision_meta = get_metadata( 'post', $revision_ID, '_pile_project_builder', true );

	//save the revision meta to the parent post
	if ( false === $revision_meta ) {
		delete_post_meta( $post_ID, '_pile_project_builder' );
	} else {
		update_post_meta( $post_ID, '_pile_project_builder', $revision_meta );
	}
}
add_action( 'wp_restore_post_revision', 'pixbuilder_on_restore_revision', 20, 2 );

function pixbuilder_save_revision_meta( $post_ID, $post ) {
	//test if the current post is a revision
	//we are only interested in them
	$parent_ID = wp_is_post_revision( $post_ID );
	if ( false !== $parent_ID ) {
		$parent = get_post( $parent_ID );
		if ( empty( $parent ) ) {
			//bail as we don't like playing with fire
			return;
		}

		//get the parent's meta data
		$meta = get_post_meta( $parent->ID, '_pile_project_builder', true );

		if ( false !== $meta ) {
			//we will only save one entry per revision, hence the update_metadata
			update_metadata( 'post', $post_ID, '_pile_project_builder', $meta );
		}
	}
}
add_action( 'save_post', 'pixbuilder_save_revision_meta', 20, 2 );