/**
 * Controls the behaviours of custom metabox fields.
 *
 * @author Andrew Norcross
 * @author Jared Atchison
 * @author Bill Erickson
 * @author Justin Sternberg
 * @see    https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */

/*jslint browser: true, devel: true, indent: 4, maxerr: 50, sub: true */
/*global jQuery, tb_show, tb_remove */

/**
 * Custom jQuery for Custom Metaboxes and Fields
 */
jQuery(document).ready(function ($) {
	'use strict';

	var formfield;

	$('body').on('input', '.cmb_text_range', function(e) {
		var $slider = $(this),
			value = $slider.val(),
			sliderMin = $slider.attr('min'),
			sliderMax = $slider.attr('max'),
			progress = (value - sliderMin) / (sliderMax - sliderMin),
			percentage = (progress * 100).toFixed(2) + '%';
		$slider.css('background-size', percentage + ' 100%');
	});

	/**
	 * First we take care of showing and hiding the meta boxes for post formats
	 */
	$('#post-formats-select input').change(verifyFormatMetaboxes);

	function verifyFormatMetaboxes() {
		var format = $('#post-formats-select input:checked').attr('value');

		//this should only happen on posts pages
		if (typeof format != 'undefined') {
			$('#post-body div[id^=post_format_metabox_]').hide();
			$('#post-body #post_format_metabox_' + format + '').stop(true, true).fadeIn(400);
		}
	}

	$(window).load(function () {
		verifyFormatMetaboxes();
	});

	/**
	 * Initialize timepicker (this will be moved inline in a future release)
	 */
	$('.cmb_timepicker').each(function () {
		$('#' + jQuery(this).attr('id')).timePicker({
			startTime: "07:00",
			endTime: "22:00",
			show24Hours: false,
			separator: ':',
			step: 30
		});
	});

	$('.tooltip').tooltipster({
		icon: '(?)',
		iconDesktop: true,
		iconTouch: true,
		iconTheme: '.tooltipster-icon',
		delay: 300,
		maxWidth: 280,
		interactive: true,
		trigger: 'hover',
		contentAsHTML: true,
	});

	/**
	 * Initialize jQuery UI datepicker (this will be moved inline in a future release)
	 */
	$('.cmb_datepicker').each(function () {
		$('#' + jQuery(this).attr('id')).datepicker();
		// $('#' + jQuery(this).attr('id')).datepicker({ dateFormat: 'yy-mm-dd' });
		// For more options see http://jqueryui.com/demos/datepicker/#option-dateFormat
	});
	// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
	$("#ui-datepicker-div").wrap('<div class="cmb_element" />');

	/**
	 * Initialize color picker
	 */
	if (typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function') {
		$('input:text.cmb_colorpicker').wpColorPicker({
			change: function ( ev ) {
				$(this).trigger('wpcolorpicker:change');
			},
			color: function ( ev ) {
				$(this).trigger('wpcolorpicker:change');
			},
		});
	} else {
		$('input:text.cmb_colorpicker').each(function (i) {
			$(this).after('<div id="picker-' + i + '" style="z-index: 1000; background: #EEE; border: 1px solid #CCC; position: absolute; display: block;"></div>');
			$('#picker-' + i).hide().farbtastic($(this));
		})
		                               .focus(function () {
			                               $(this).next().show();
		                               })
		                               .blur(function () {
			                               $(this).next().hide();
		                               });
	}

	/**
	 * File and image upload handling
	 */
	$('.cmb_upload_file').change(function () {
		formfield = $(this).attr('name');
		$('#' + formfield + '_id').val("");
	});

	$('.cmb_upload_button').on('click', $(this), function () {
		var buttonLabel;
		formfield = $(this).prev('input').attr('name');
		buttonLabel = 'Use as ' + $('label[for=' + formfield + ']').text();
		tb_show('', 'media-upload.php?post_id=' + $('#post_ID').val() + '&type=file&cmb_force_send=true&cmb_send_label=' + buttonLabel + '&TB_iframe=true');
		return false;
	});

	$('.cmb_remove_file_button').on('click', $(this), function () {
		formfield = $(this).attr('rel');
		$('input#' + formfield).val('');
		$('input#' + formfield + '_id').val('');
		$(this).parent().remove();
		return false;
	});

	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function (html) {
		var itemurl, itemclass, itemClassBits, itemid, htmlBits, itemtitle,
			image, uploadStatus = true;

		if (formfield) {

			if ($(html).html(html).find('img').length > 0) {
				itemurl = $(html).html(html).find('img').attr('src'); // Use the URL to the size selected.
				itemclass = $(html).html(html).find('img').attr('class'); // Extract the ID from the returned class name.
				itemClassBits = itemclass.split(" ");
				itemid = itemClassBits[itemClassBits.length - 1];
				itemid = itemid.replace('wp-image-', '');
			} else {
				// It's not an image. Get the URL to the file instead.
				htmlBits = html.split("'"); // jQuery seems to strip out XHTML when assigning the string to an object. Use alternate method.
				itemurl = htmlBits[1]; // Use the URL to the file.
				itemtitle = htmlBits[2];
				itemtitle = itemtitle.replace('>', '');
				itemtitle = itemtitle.replace('</a>', '');
				itemid = ""; // TO DO: Get ID for non-image attachments.
			}

			image = /(jpe?g|png|gif|ico)$/gi;

			if (itemurl.match(image)) {
				uploadStatus = '<div class="img_status"><img src="' + itemurl + '" alt="" /><a href="#" class="cmb_remove_file_button" rel="' + formfield + '">Remove Image</a></div>';
			} else {
				// No output preview if it's not an image
				// Standard generic output if it's not an image.
				html = '<a href="' + itemurl + '" target="_blank" rel="external">View File</a>';
				uploadStatus = '<div class="no_image"><span class="file_link">' + html + '</span>&nbsp;&nbsp;&nbsp;<a href="#" class="cmb_remove_file_button" rel="' + formfield + '">Remove</a></div>';
			}

			var to_meta = $('#' + formfield);
			if (to_meta.hasClass('attachment')) {

				var meta_val = {};

				meta_val['link'] = itemurl;
				meta_val['id'] = itemid;
				meta_val = JSON.stringify(meta_val);

				$('[name="' + formfield + '"]').val(meta_val);

			} else {
				to_meta.val(itemurl);
				$('#' + formfield + '_id').val(itemid);
			}

			to_meta.siblings('.cmb_media_status').slideDown().html(uploadStatus);
			tb_remove();

		} else {
			window.original_send_to_editor(html);
		}

		formfield = '';
	};

	/**
	 * Ajax oEmbed display
	 */

	// ajax on paste
	$('.cmb_oembed').bind('paste', function (e) {
		var pasteitem = $(this);
		// paste event is fired before the value is filled, so wait a bit
		setTimeout(function () {
			// fire our ajax function
			doCMBajax(pasteitem, 'paste');
		}, 100);
	}).blur(function () {
		// when leaving the input
		setTimeout(function () {
			// if it's been 2 seconds, hide our spinner
			$('.postbox table.cmb_metabox .cmb-spinner').hide();
		}, 2000);
	});

	// ajax when typing
	$('.cmb_metabox').on('keyup', '.cmb_oembed', function (event) {
		// fire our ajax function
		doCMBajax($(this), event);
	});

	// function for running our ajax
	function doCMBajax(obj, e) {
		// get typed value
		var oembed_url = obj.val();
		// only proceed if the field contains more than 6 characters
		if (oembed_url.length < 6)
			return;

		// only proceed if the user has pasted, pressed a number, letter, or whitelisted characters
		if (e === 'paste' || e.which <= 90 && e.which >= 48 || e.which >= 96 && e.which <= 111 || e.which == 8 || e.which == 9 || e.which == 187 || e.which == 190) {

			// get field id
			var field_id = obj.attr('id');
			// get our inputs context for pinpointing
			var context = obj.parents('.cmb_metabox tr td');
			// show our spinner
			$('.cmb-spinner', context).show();
			// clear out previous results
			$('.embed_wrap', context).html('');
			// and run our ajax function
			setTimeout(function () {
				// if they haven't typed in 500 ms
				if ($('.cmb_oembed:focus').val() == oembed_url) {
					$.ajax({
						type: 'post',
						dataType: 'json',
						url: window.ajaxurl,
						data: {
							'action': 'cmb_oembed_handler',
							'oembed_url': oembed_url,
							'field_id': field_id,
							'post_id': window.cmb_ajax_data.post_id,
							'cmb_ajax_nonce': window.cmb_ajax_data.ajax_nonce
						},
						success: function (response) {
							// if we have a response id
							if (typeof response.id !== 'undefined') {
								// hide our spinner
								$('.cmb-spinner', context).hide();
								// and populate our results from ajax response
								$('.embed_wrap', context).html(response.result);
							}
						}
					});
				}
			}, 500);
		}
	}

	/**
	 * No more tractor!!!
	 * Hide and show metafields depending on each other
	 */

	var toggle_metafields = function (el) {

		var $self = $(el),
			action = $self.data('action'),
			field = $self.attr('data-when_key'),
			value = $self.data('has_value'),
			selector = '[name="' + field + '"]',
			$selector = $('[name="' + field + '"]'),
			currentValue = '';

		//we need to treat radio groups differently
		if ($selector.length > 1) {
			//we assume that we are in a group
			//then we need to get the value through other means
			currentValue = $('[name="' + field + '"]:checked').val();
		} else {
			currentValue = $selector.val();
		}

		if ( value_check( currentValue, value ) ) {
			toggle_meta(el, action);
		} else {
			toggle_opposite(el, action);
		}

		//each time it changes get down to business
		$(document).on('change', selector, function (e) {
			//we need to treat radio groups differently
			if ($selector.length > 1) {
				//we assume that we are in a group
				//then we need to get the value through other means
				currentValue = $('[name="' + field + '"]:checked').val();
			} else {
				currentValue = $selector.val();
			}

			if ( value_check( currentValue, value ) ) {
				toggle_meta(el, action);
			} else {
				toggle_opposite(el, action);
			}

		});
	};

	var value_check = function(currentValue, value ) {
		var value_test = false;

		// if is not an object convert to string
		if ( typeof value === 'number' || typeof value === 'boolean' ) {
			value = String(value);
		}

		//check for single or multiple values
		if ( typeof value === 'string' && currentValue == value ) {
			value_test = true;
		} else if ( typeof value === 'object' && value.indexOf( currentValue ) > -1) {
			// in case there are multiple values check if our current values is inside the array
			value_test = true;
		}

		return value_test;
	};

	var toggle_meta = function (selector, action) {
		var when_key = $(selector).data('when_key'),
			$target = $('#' + when_key),
			$parent = $target.parent().parent();

		/**
		 * Check if the curent element needs to be showed
		 * Also if it's parent is hidden the child needs to follow
		 */
		if (action == 'show' && !$parent.hasClass('hidden')) {
			$(selector).show().removeClass('hidden');
		} else {
			$(selector).hide().addClass('hidden');
		}

		/**
		 * Trigger a change!
		 * This way our kids(elements) will know that something is changed and they should follow
		 */
		$(selector).find('select, input:radio').trigger('change');
	};

	var toggle_opposite = function (selector, action) {
		var when_key = $(selector).data('when_key'),
			$target = $('#' + when_key),
			$parent = $target.parent().parent();
		/**
		 * Check if the curent element needs to be showed
		 * Also if it's parent is hidden the child needs to follow
		 */
		if (action == 'hide' && !$parent.hasClass('hidden')) {
			$(selector).show().removeClass('hidden');
		} else {
			$(selector).hide().addClass('hidden');
		}

		/**
		 * Trigger a change!
		 * This way our kids(elements) will now that something is changed and they should follow
		 */
		$(selector).find('select, input:radio').trigger('change');
	};

	function showMetaboxesGutenberg() {
		$('#custom_portfolio_page_settings').show();
		$('.editor-block-list__layout').hide();
		$('.edit-post-layout__content .edit-post-visual-editor').css(
			{
				'flex-basis': 0,
				'flex': 'unset'
			}
		);
	}

	function hideMetaboxesGutenberg() {
		$('#custom_portfolio_page_settings').hide();
		$('.editor-block-list__layout').show();
		$('.edit-post-layout__content .edit-post-visual-editor').removeAttr('style');
	}

	/**
	 * Fold elements when the entire page is loaded
	 * Some css classes are added dynamically, they can only be used after the load event
	 */
	$(window).on('load', function () {
		$('.display_on').each(function () {
			toggle_metafields(this);
		});

		$('.show_metabox_on').each(function () {
			toggle_metaboxes(this);
		});

		if ($('.editor-page-attributes__template select').val() === 'page-templates/custom-portfolio-page.php') {
			showMetaboxesGutenberg();
		}
	});

	var toggle_metaboxes = function(el) {

		var $el = $(el),
			key = $el.data('key'),
			value = $el.data('value'),
			hide = $el.data('hide') || false;

		if ( typeof value === 'undefined' ) return;

		if ( key == 'page-template' ) {

			var templateSelector = $('.editor-page-attributes__template select');

			var condition = false;
			$.each(value, function(key,val){
				var $select = $('select#page_template').length ? $('select#page_template') : $('.editor-page-attributes__template select');
				if ( $select.val() === val ) {
					condition = true;
				}
			});

			// Make metaboxes show on Gutenberg Editor
			if ($('body').hasClass('block-editor-page')) {

				templateSelector.on('change', function(){
					if (templateSelector.val() === 'page-templates/custom-portfolio-page.php') {
						showMetaboxesGutenberg()
					} else {
						hideMetaboxesGutenberg();
					}
				});
			}

			if ( condition ) {
				display_metabox( el, !hide );
			} else {
				display_metabox( el, hide );
			}

		} else if ( key == 'select_value') {
			// in the future
		}

	};

	/**
	 * This function takes an element and decides to show or hide it by the given param
	 * @param element
	 * @param display
	 */
	var display_metabox = function (element, display) {
		if ( display ) {
			$(element).parents('.postbox').show();
		} else {
			$(element).parents('.postbox').hide();
		}
	};

	/**
	 * On page template change ajaxify metaboxes
	 */

	$('#page_template').on('change', function () {
		$('.show_metabox_on').each(function () {
			toggle_metaboxes(this);
		});
	});

	//LENS - Ahaaaaaa!!! This is so evil and shameful, but I like it

	//logic for the LENS homepage chooser metabox
	if ($('#page_template').val() == 'template-homepage.php') {
		$('#lens_homepage_chooser').show();
	} else {
		$('#lens_homepage_chooser').hide();
	}

	$('#page_template').on('change', function () {
		if ($('#page_template').val() == 'template-homepage.php') {
			$('#lens_homepage_chooser').show();
		} else {
			$('#lens_homepage_chooser').hide();
		}
	});

	//Manage the radios controling the homepage contents
	var $lens_custom_homepage_checked = $('input:radio[name="_lens_custom_homepage"]:checked');
	if ($lens_custom_homepage_checked.val() == 'lens_gallery') {
		$('#_lens_homepage_gallery').closest('tr').show();
		$('#_lens_homepage_projects_number').closest('tr').hide();
	} else if ($lens_custom_homepage_checked.val() == 'lens_portfolio_cat') {
		$('#_lens_homepage_portfolio_category').closest('tr').show();
		$('#_lens_homepage_projects_number').closest('tr').show();
	} else if ($lens_custom_homepage_checked.val() == 'lens_galleries_cat') {
		$('#_lens_homepage_galleries_category').closest('tr').show();
		$('#_lens_homepage_projects_number').closest('tr').show();
	}

	//monitor the change of the radio group
	$("input[name='_lens_custom_homepage']").change(function (e) {
		if ($(this).val() == 'lens_gallery') {
			$('#_lens_homepage_portfolio_category').closest('tr').hide();
			$('#_lens_homepage_galleries_category').closest('tr').hide();
			$('#_lens_homepage_gallery').closest('tr').show();
			$('#_lens_homepage_projects_number').closest('tr').hide();
		} else if ($(this).val() == 'lens_portfolio') {
			$('#_lens_homepage_portfolio_category').closest('tr').hide();
			$('#_lens_homepage_galleries_category').closest('tr').hide();
			$('#_lens_homepage_gallery').closest('tr').hide();
			$('#_lens_homepage_projects_number').closest('tr').show();
		} else if ($(this).val() == 'lens_portfolio_cat') {
			$('#_lens_homepage_portfolio_category').closest('tr').show();
			$('#_lens_homepage_galleries_category').closest('tr').hide();
			$('#_lens_homepage_gallery').closest('tr').hide();
			$('#_lens_homepage_projects_number').closest('tr').show();
		} else if ($(this).val() == 'lens_galleries_archive') {
			$('#_lens_homepage_portfolio_category').closest('tr').hide();
			$('#_lens_homepage_galleries_category').closest('tr').hide();
			$('#_lens_homepage_gallery').closest('tr').hide();
			$('#_lens_homepage_projects_number').closest('tr').show();
		} else if ($(this).val() == 'lens_galleries_cat') {
			$('#_lens_homepage_portfolio_category').closest('tr').hide();
			$('#_lens_homepage_galleries_category').closest('tr').show();
			$('#_lens_homepage_gallery').closest('tr').hide();
			$('#_lens_homepage_projects_number').closest('tr').show();
		}
	});

	//hide and show the gallery metabox on the project editor depending on the template used
	if ($('#_lens_project_template').val() == 'classic') {
		$('#portfolio_gallery').hide();
		$('#portfolio_video').hide();
		$('#_lens_portfolio_image_scale_mode').parents('tr').hide();
		$('#_lens_portfolio_slider_transition').parents('tr').hide();
		$('#_lens_portfolio_slider_autoplay').parents('tr').hide();
		$('#_lens_portfolio_slider_delay').parents('tr').hide();
	} else {
		$('#portfolio_gallery').show();
		$('#portfolio_video').show();
		$('#_lens_portfolio_image_scale_mode').parents('tr').show();
		$('#_lens_portfolio_slider_transition').parents('tr').show();
		$('#_lens_portfolio_slider_autoplay').parents('tr').show();
		$('#_lens_portfolio_slider_delay').parents('tr').show();
	}

	if ($('#_lens_project_template').val() == 'fullwidth') {
		$('#_lens_portfolio_image_scale_mode').parents('tr').show();
		$('#_lens_portfolio_slider_transition').parents('tr').show();
		$('#_lens_portfolio_slider_autoplay').parents('tr').show();
		$('#_lens_portfolio_slider_delay').parents('tr').show();
	} else {
		$('#_lens_portfolio_image_scale_mode').parents('tr').hide();
		$('#_lens_portfolio_slider_transition').parents('tr').hide();
		$('#_lens_portfolio_slider_autoplay').parents('tr').hide();
		$('#_lens_portfolio_slider_delay').parents('tr').hide();
	}

	$('#_lens_project_template').on('change', function () {

		if ($('#_lens_project_template').val() == 'classic') {
			$('#portfolio_gallery').hide();
			$('#portfolio_video').hide();
		} else {
			$('#portfolio_gallery').show();
			$('#portfolio_video').show();
		}

		if ($('#_lens_project_template').val() == 'fullwidth') {
			$('#_lens_portfolio_image_scale_mode').parents('tr').show();
			$('#_lens_portfolio_slider_transition').parents('tr').show();
			$('#_lens_portfolio_slider_autoplay').parents('tr').show();
			$('#_lens_portfolio_slider_delay').parents('tr').show();
		} else {
			$('#_lens_portfolio_image_scale_mode').parents('tr').hide();
			$('#_lens_portfolio_slider_transition').parents('tr').hide();
			$('#_lens_portfolio_slider_autoplay').parents('tr').hide();
			$('#_lens_portfolio_slider_delay').parents('tr').hide();
		}

	});

	// hide and show on gallery
	if ($('#_lens_gallery_template').val() == 'masonry') {
		$('#_lens_thumb_orientation').parents('tr').show();
		$('#_lens_show_gallery_title').parents('tr').show();
		$('#_lens_exclude_gallery').parents('tr').show();

		$('#_lens_gallery_slider_transition').parents('tr').hide();
		$('#_lens_gallery_image_scale_mode').parents('tr').hide();
		$('#_lens_gallery_slider_autoplay').parents('tr').hide();
		$('#_lens_gallery_slider_delay').parents('tr').hide();
		$('#_lens_post_slider_visiblenearby').parents('tr').hide();
	} else if ($('#_lens_gallery_template').val() == 'fullwidth' || $('#_lens_gallery_template').val() == 'fullscreen') {
		$('#_lens_thumb_orientation').parents('tr').hide();
		$('#_lens_show_gallery_title').parents('tr').hide();
		$('#_lens_gallery_slider_transition').parents('tr').show();
		$('#_lens_gallery_image_scale_mode').parents('tr').show();
		$('#_lens_gallery_slider_autoplay').parents('tr').show();
		$('#_lens_gallery_slider_delay').parents('tr').show();
		$('#_lens_post_slider_visiblenearby').parents('tr').show();
		$('#_lens_exclude_gallery').parents('tr').show();
	} else if ($('#_lens_gallery_template').val() == 'masonry-plus') {
		$('#_lens_gallery_template').parents('tr').nextAll().hide();
		$('#_lens_thumb_orientation').parents('tr').show();
		$('#_lens_show_gallery_title').parents('tr').show();
		$('#_lens_exclude_gallery').parents('tr').show();
	}

	$('#_lens_gallery_template').on('change', function () {

		// hide and show on gallery
		if ($(this).val() == 'masonry') {
			$('#_lens_thumb_orientation').parents('tr').show();
			$('#_lens_show_gallery_title').parents('tr').show();
			$('#_lens_exclude_gallery').parents('tr').show();
			$('#_lens_gallery_slider_transition').parents('tr').hide();
			$('#_lens_gallery_image_scale_mode').parents('tr').hide();
			$('#_lens_gallery_slider_autoplay').parents('tr').hide();
			$('#_lens_gallery_slider_delay').parents('tr').hide();
			$('#_lens_post_slider_visiblenearby').parents('tr').hide();
		} else if ($(this).val() == 'fullwidth' || $(this).val() == 'fullscreen') {
			$('#_lens_thumb_orientation').parents('tr').hide();
			$('#_lens_show_gallery_title').parents('tr').hide();
			$('#_lens_gallery_slider_transition').parents('tr').show();
			$('#_lens_gallery_image_scale_mode').parents('tr').show();
			$('#_lens_gallery_slider_autoplay').parents('tr').show();
			$('#_lens_gallery_slider_delay').parents('tr').show();
			$('#_lens_post_slider_visiblenearby').parents('tr').show();
			$('#_lens_exclude_gallery').parents('tr').show();
		} else if ($(this).val() == 'masonry-plus') {
			$(this).parents('tr').nextAll().hide();
			$('#_lens_thumb_orientation').parents('tr').show();
			$('#_lens_show_gallery_title').parents('tr').show();
			$('#_lens_exclude_gallery').parents('tr').show();
		}

	});

	if ($('#_lens_gallery_slider_autoplay').val() == '1') {
		$('#_lens_gallery_slider_delay').parents('tr').show();
	} else {
		$('#_lens_gallery_slider_delay').parents('tr').hide();
	}

	$('#_lens_gallery_slider_autoplay').on('change', function () {

		if ($('#_lens_gallery_slider_autoplay').val() == '1') {
			$('#_lens_gallery_slider_delay').parents('tr').show();
		} else {
			$('#_lens_gallery_slider_delay').parents('tr').hide();
		}

	});
});
