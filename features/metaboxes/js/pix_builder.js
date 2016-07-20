(function ($) {
	/**
	 * Global variables
	 */
	var media = wp.media,
		Attachment = media.model.Attachment,
		serialize_intention = false,
		serialize_timeout = false;

	$(document).ready(function () {

		var $pix_builder = $('#content'),
			gridster = $(".gridster > ul"),
			modal_container = $('.pix_builder_editor_modal_container');

		/**
		 * @var gridster_params is an object localized by wordpress and is defined by the theme
		 *
		 * @type {Function}
		 */
		gridster_params.serialize_params = new Function([
				gridster_params.serialize_params[0],
				gridster_params.serialize_params[1]
			],
			gridster_params.serialize_params[2]);

		gridster_params.resize.resize = new Function(
			gridster_params.on_resize_callback[0],
			gridster_params.on_resize_callback[1],
			gridster_params.on_resize_callback[2],

			gridster_params.on_resize_callback[3]
		);

		var widget_width = $('#normal-sortables').width() / 6;
		gridster_params.widget_base_dimensions = [ widget_width - 35 , 40];

		gridster = gridster.gridster(gridster_params).data('gridster');

		$(window).on('resize', function() {
			var widget_width = $('#normal-sortables').width() / 6,
				options = {
					widget_base_dimensions: [ widget_width - 35, 40 ]
				}
			gridster.resize_widget_dimensions(options);
		});

		// Functions
		/**
		 * Checks if a serialisation event is already ongoing
		 * or start one if not
		 */
		var intent_to_serialize = function() {
			// prevent the user from saving
			$('#publish').attr('disabled', 'disabled');
			if ( ! serialize_intention ) {
				serialize_timeout = setTimeout( serialize_pix_builder_values, 1000);
				serialize_intention = true;
			} else {
				// kill the timout and start a new one
				clearTimeout(serialize_timeout);
				serialize_timeout = setTimeout( serialize_pix_builder_values, 1000);
			}
		};

		var b64EncodeUnicode = function(str) {
			return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
				return String.fromCharCode('0x' + p1);
			}));
		};

		var serialize_pix_builder_values = function(){

			var new_values = gridster.serialize();

			// sort_them
			new_values = Gridster.sort_by_row_and_col_asc(new_values);

			var public_content = '',
				output = [],
				attachment = null;

			$.each( new_values, function ( i, j) {
				if ( j.hasOwnProperty('content') ) {
					if ( j.type === 'editor') {
						public_content = public_content + j.content + "\n";
					}
				}
			});

			// we will save 2 versions of the content
			// 1 for public: public_content
			// 2 for internal use: internal_content
			// var public_content = prepare_values_for_content( new_values );
			// $.each( new_values, function ( i, j) {
			// 	if ( j.hasOwnProperty('content') ) {
			// 		if ( j.type === 'editor') {
			// 			new_values[i].content = b64EncodeUnicode( j.content );
			// 		}
			// 	}
			// });

			var internal_content = JSON.stringify(new_values),
				content_editor = tinyMCE.get('content');

			if( typeof content_editor === "undefined" || content_editor === null ) { // text editor
				$('#content').val( public_content );
				$('#content').text( public_content );
			} else { // visual editor
				content_editor.setContent( public_content , {format:'text'});
			}

			$('#pix_builder').val(internal_content);

			// now  it can be saved
			$('#publish').removeAttr('disabled');

			serialize_intention = false;
		};

		var close_editor_modal = function () {
			modal_container.removeClass('modal_opened').hide();
			set_pix_builder_editor_content('');
			tinyMCE.triggerSave();
		};

		var set_pix_builder_editor_content = function ( content ){

			var this_editor = tinyMCE.get('pix_builder_editor');

			if( typeof this_editor === "undefined" || this_editor === null) { // text editor
				$('#pix_builder_editor').val( content );
				$('#pix_builder_editor').text( content );

			} else { // visual editor
				this_editor.setContent( content.replace(/\n/ig,"<br>"), { format:'text' });
				this_editor.save( { no_events: true } );
			}
		};

		function create_block_id() {

			// get the curent number of blocks
			var blocks = $('.pixbuilder-grid > ul > li'),
				number_of_blocks = 1;

			if ( $('.pixbuilder-grid > ul > li').length < 1 ) {
				return number_of_blocks;
			}

			$('.pixbuilder-grid > ul > li').each( function (i, j) {
				var id_nr = $( this ).attr('id').replace('block_', '');

				if ( parseInt(id_nr) > number_of_blocks ) {
					number_of_blocks = parseInt(id_nr) + 1;
				} else {
					number_of_blocks = number_of_blocks + 1;
				}
			});
			
			return parseInt(number_of_blocks);
		}
		/**
		 * Events
		 */

		$(document).on('mouseup', '.gridster ul li', function (ev) {
			// lets serialize again
			$(document).trigger('pix_builder:serialize');
		});

		// Add blocks
		$(document).on('click', '.add_block', function (ev) {
			ev.preventDefault();

			var number_of_blocks = create_block_id();

			var type = $(this).val(),
				args = {
					id: number_of_blocks,
					type: type,
					content: ''
				};
			var block_template = get_block_template(args);

			gridster.add_widget(block_template, 2, 2);
			//after we done update the json
			$(document).trigger('pix_builder:serialize');
		});

		// Remove block
		$(document).on('click', '.remove_block', function () {
			gridster.remove_widget($(this).closest('.item'));
			//after we done update the json
			$(document).trigger('pix_builder:serialize');
		});

		// open modal and prepare the editor
		$(document).on('click', '.edit_editor', function (e){

			e.preventDefault();
			var id = $(this).closest('.item').attr('id').replace('block_', '');

			if ( ! modal_container.hasClass('modal_opened') ) {
				setTimeout(function () {

					modal_container.addClass('modal_opened')
						.show();

					var content = $('#block_'+ id + ' .to_send').val();

					if ( content !== "" ) {
						set_pix_builder_editor_content( content );
					} else {
						set_pix_builder_editor_content( '' );
					}

					// ensure the editor is on visual
					switchEditors.go( 'pix_builder_editor', 'tmce' );
				}, 600);

				modal_container.find('.insert_editor_content').data('block_id', id );
			}
		});

		// close modal
		$(document).on('click', '.close_modal_btn', function (){
			close_editor_modal();
		});

		// get editor's content and preview it
		$(document).on('click', '.insert_editor_content',function(e){
			e.preventDefault();
			tinyMCE.triggerSave();
			var editor = $('#pix_builder_editor'), // the only portfolio's editor
				editor_val = editor.val(),
				to_send = $('#block_'+ $(this).data('block_id') + ' .to_send');

			$(to_send).text( editor_val );

			// preview the new value
			$(to_send).next('.editor_preview').find('.editor_preview_wrapper').html(editor_val.replace(/\n/ig,"<br>"));

			if ( editor_val === '' ) {
				$(to_send).parent().addClass('empty');
			} else {
				$(to_send).parent().removeClass('empty');
			}

			$(document).trigger('pix_builder:serialize');

			close_editor_modal();
		});

		// serialize pix_builder values
		$(document).on('pix_builder:serialize', intent_to_serialize );

		$(document).on('click', '.clear-all', function( ev ){

			ev.preventDefault();

			var conf = confirm('Are you sure, sure you want to delete all blocks?');

			if ( conf ) {
				gridster.remove_all_widgets();
				$(document).trigger('pix_builder:serialize');
			}
		});

	}); /* Document.ready */

	// Get the html for the block
	var get_block_template = function (args) {

		if (typeof args !== 'object') {
			return '';
		}

		var content = '',
			controls_content = '';

		// Editor Block
		if (args.type === 'editor') {
			content = '<textarea class="to_send" style="display: none">' + args.content + '</textarea>'+
				'<div class="editor_preview">' +
				'<div class="editor_preview_wrapper">' + args.content.replace(/\n/ig,"<br>") + '</div>' +
				'</div>';
			controls_content = '<a class="edit_editor"><span>Edit</span></a>';

			// Image Block
		} else if (args.type == 'image') {
			// in case of an image the content should hold only an integer which represents the id
			if (!isNaN(args.content) && args.content !== '') {
				var attach = Attachment.get(args.content);
				attach.fetch({
					async: false,
					success: function () {
						content = '<img class="image_preview" src="' + attach.attributes.url + '">';
						controls_content = '<a class="open_media" href="#" class="wp-gallery" data-attachment_id="' + args.content + '"><span>' + l18n_pix_builder.set_image + '</span></a>';
					}
				});
			} else {
				content = '<img class="image_preview">';
				controls_content = '<a class="open_media" href="#" class="wp-gallery" data-attachment_id="' + args.content + '"><span>'+ l18n_pix_builder.set_image +'</pan></a>';
			}
		}

		var empty_class = '';
		if ( args.content === '' ) {
			empty_class = 'empty';
		}

		return '<li id="block_' + args.id + '" class="block-type--' + args.type + ' item" data-type="' + args.type + '">' +
			'<div class="item__controls">' +
			'<ul class="nav nav--controls">' +
			'<li class="edit">'+controls_content+'</li>' +
			'<li class="position"><span>Position</span>' +
				'<div class="position__ui">' +
					'<div class="position__ui-title">Alignment</div>' +
					'<div class="position__ui-body">' +
						'<div class="position__ui-row">' +
							'<div class="position__ui-cell top">' +
								'<div class="position__ui-handle">top</div>' +
							'</div>' +
						'</div>' +
						'<div class="position__ui-row">' +
							'<div class="position__ui-cell left' + ((args.type === 'editor') ? ' active' : '') + '">' +
								'<div class="position__ui-handle"' + ((args.type === 'editor') ? ' data-step="1"' : '') + '>left</div>' +
							'</div>' +
							'<div class="position__ui-cell middle' + ((args.type !== 'editor') ? ' active' : '') + '">' +
								'<div class="position__ui-handle">middle</div>' +
							'</div>' +
							'<div class="position__ui-cell right">' +
								'<div class="position__ui-handle">right</div>' +
							'</div>' +
						'</div>' +
						'<div class="position__ui-row">' +
							'<div class="position__ui-cell bottom">' +
								'<div class="position__ui-handle">bottom</div>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</li>' +
			'<li class="remove remove_block"><span>Remove</span></li>' +
			'<li class="move drag_handler"></li>' +
			'</ul>' +
			'</div>' +
			'<div class="item__content block_content ' + empty_class + '">' +
			content +
			'</div>' +
			'</li>';

	}; /* get_block_template */

	$(window).load(function () {

		var last_opened_block = {};
		wp.media.controller.PixBuilderSingleImage = wp.media.controller.FeaturedImage.extend({
			defaults: _.defaults({
				id: 'pix_builder_image',
				filterable: 'uploaded',
				multiple: false,
				toolbar: 'pix_builder_image',
				title: l18n_pix_builder.set_image,
				priority: 60,
				syncSelection: false,
				displaySettings:  false
			}, wp.media.controller.Library.prototype.defaults),
			updateSelection: function () {
				var selection = this.get('selection'),
					id = $(last_opened_block).attr('data-attachment_id'),
					attachment;
				if ('' !== id && -1 !== id) {
					attachment = Attachment.get(id);
					attachment.fetch();
				}
				selection.reset(attachment ? [attachment] : []);
			}
		});

		wp.media.PixBuilderSingleImage = {
			frame: function () {
				if (this._frame)
					return this._frame;

				// create our own media iframe
				this.$button = $(this.element);

				this._frame = wp.media({
					id: 'pix_builder_image',
					title: l18n_pix_builder.set_image,
					filterable: 'uploaded',
					library: {type: 'image'}
				});

				this._frame = wp.media({
					state: 'pix_builder_image',
					states: [new wp.media.controller.PixBuilderSingleImage()]
				});
				this._frame.on('toolbar:create:pix_builder_image', function (toolbar) {
					this.createSelectToolbar(toolbar, {
						text: l18n_pix_builder.set_image
					});
				}, this._frame);

				this._frame.state('pix_builder_image').on('select', this.select);

				this.attachment_id = $(last_opened_block).data('attachment_id');

				return this._frame;
			},
			init: function () {
				$(document).on('click', '.open_media', function (e) {
					e.preventDefault();
					wp.media.PixBuilderSingleImage.element = last_opened_block = this;
					wp.media.PixBuilderSingleImage.frame().open();
				});
			},
			select: function () {
				var selected_attach = selection = this.get('selection').single();

				if (typeof selected_attach.id !== 'undefined') {
					$(last_opened_block).attr('data-attachment_id', selected_attach.id);

					$(last_opened_block).parents('.item__controls').siblings('.block_content').removeClass('empty');

					preview_attachment_image(last_opened_block, selected_attach);
					$(document).trigger('pix_builder:serialize');
				}
			}
		};

		$(wp.media.PixBuilderSingleImage.init);

		// Image Block -- Replace Preview
		var preview_attachment_image = function (el, attachment) {
			$(el).closest('.item').find('.image_preview').attr("src" , attachment.attributes.url);
		};

		// just playing
		$('.pix_builder_container').show(500, function(){
			$(window).trigger('scroll');
		});

		$(".pixbuilder-controls").fixer({gap: 40});

		$(document).on('mouseover', '.position', function() {
			$('.pixbuilder-grid').addClass('is--over-controls');
		});

		$(document).on('mouseout', '.position', function() {
			$('.pixbuilder-grid').removeClass('is--over-controls');
		});

		// margins?
		var $grid = $('.pixbuilder-grid');

		$grid.on('click', '.position__ui-cell', function(e) {
			var $cell 		= $(this),
                $container  = $cell.closest('.position__ui'),
                $item       = $cell.find('.position__ui-handle'),
                step        = $item.attr('data-step'),
                $active     = $container.find('.position__ui-cell.active'),
                $turnOff    = $container.find('.position__ui-cell.middle');

			if ( $cell.is('.middle') ) $turnOff = $active;
            if ( $cell.is('.top') && $active.filter('.bottom').length ) $turnOff = $turnOff.add($active.filter('.bottom'));
            if ( $cell.is('.right') && $active.filter('.left').length ) $turnOff = $turnOff.add($active.filter('.left'));
            if ( $cell.is('.bottom') && $active.filter('.top').length ) $turnOff = $turnOff.add($active.filter('.top'));
            if ( $cell.is('.left') && $active.filter('.right').length ) $turnOff = $turnOff.add($active.filter('.right'));

            $turnOff.removeClass('active').find('.position__ui-handle').attr('data-step', 0);
            step = typeof step === "undefined" ? 1 : step == 3 ? 0 : parseInt(step) + 1;

            $item.attr('data-step', step);
            $cell.toggleClass('active', !!step);

            $active     = $container.find('.position__ui-cell.active');

            if ( ! $active.filter('.active').length ) {
                $container.find('.position__ui-cell.middle').addClass('active');
            }

            updateCell($cell);
		});

		$grid.on('mouseenter', '.position', function(e) {
			$grid.css('z-index', '1200');
		});

		$grid.on('mouseleave', '.position', function(e) {
			setTimeout(function() {
				$grid.css('z-index', '');
			}, 200);
		});

        $('.position__ui-cell').each(function() { updateCell($(this)); });

        function updateCell($cell) {
            var $container  = $cell.closest('.position__ui'),
                $active     = $container.find('.position__ui-cell.active'),
                $item       = $cell.find('.position__ui-handle'),
                step        = $item.attr('data-step'),
                $content    = $item.closest('.item').find('.item__content'),
                props       = ['top', 'right', 'bottom', 'left'];

            for (var i = 0; i < props.length; i++)
                for (var j = 0; j < 4; j++)
                    $content.removeClass(props[i] + '-' + j);

            // update block
            for (var i = 0; i < props.length; i++) {
                var $prop = $active.filter('.'+props[i]);
                if ( $prop.length ) $content.addClass(props[i] + '-' + $prop.find('.position__ui-handle').attr('data-step'));
            }
        }

	}); /* Window.load */

})(jQuery);

/*!
 * jquery.fixer.js 0.0.3 - https://github.com/yckart/jquery.fixer.js
 * Fix elements as `position:sticky` do.
 *
 *
 * Copyright (c) 2013 Yannick Albert (http://yckart.com/) | @yckart
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
 * 2013/07/02
 **/
;(function($, window) {

	var $win = $(window);
	var defaults = {
		gap: 0,
		horizontal: false,
		isFixed: $.noop
	};

	var supportSticky = function(elem) {
		var prefixes = ['', '-webkit-', '-moz-', '-ms-', '-o-'], prefix;
		while (prefix = prefixes.pop()) {
			elem.style.cssText = 'position:' + prefix + 'sticky';
			if (elem.style.position !== '') return true;
		}
		return false;
	};

	$.fn.fixer = function(options) {
		options = $.extend({}, defaults, options);
		var hori = options.horizontal,
			cssPos = hori ? 'left' : 'top';

		return this.each(function() {
			var style = this.style,
				$this = $(this),
				$parent = $this.parent();

			if (supportSticky(this)) {
				style[cssPos] = options.gap + 'px';
				return;
			}

			$win.on('scroll', function() {
				var scrollPos = $win[hori ? 'scrollLeft' : 'scrollTop'](),
					elemSize = $this[hori ? 'outerWidth' : 'outerHeight'](),
					parentPos = $parent.offset()[cssPos],
					parentSize = $parent[hori ? 'outerWidth' : 'outerHeight']();

				if (scrollPos >= parentPos - options.gap && (parentSize + parentPos - options.gap) >= (scrollPos + elemSize)) {
					style.position = 'fixed';
					style[cssPos] = options.gap + 'px';
					options.isFixed();
				} else if (scrollPos < parentPos) {
					style.position = 'absolute';
					style[cssPos] = 0;
				} else {
					style.position = 'absolute';
					style[cssPos] = parentSize - elemSize + 'px';
				}
			}).resize();
		});
	};

}(jQuery, this));