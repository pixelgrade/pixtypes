(function ($) {
	/**
	 * Global variables
	 */
	var media = wp.media,
		Attachment = media.model.Attachment;

	$(document).ready(function () {

		var gridster = $(".gridster ul"),
			gridster_params = $('#pix_builder').data('params'),
			modal_container = $('.pix_builder_editor_modal_container');

		var theme_params_func = new Function(
			gridster_params.serialize_params[0],
			gridster_params.serialize_params[1],
			gridster_params.serialize_params[2]);
		gridster_params.serialize_params = theme_params_func;

		/**
		 * use this to serialize these params
		 * after that echo them in activation.php config
		 */
			//var gridster_params = {
			//	widget_margins: [30, 30],
			//	widget_base_dimensions: [150, 50],
			//	min_cols: 3,
			//	resize: {
			//		enabled: true,
			//		axes: ['x']
			//	},
			//	draggable: {
			//		handle: '.drag_handler'
			//	},
			//	serialize_params: function ($w, wgd) {
			//		var type = $w.data("type"),
			//			content = $w.find(".block_content").text();
			//		if (type == "text") {
			//			content = $w.find(".block_content textarea").val();
			//		} else if (type == "image") {
			//			content = $w.find(".open_media").attr("data-attachment_id");
			//		}
			//		return {
			//			id: $w.prop("id"),
			//			type: type,
			//			content: content,
			//			col: wgd.col,
			//			row: wgd.row,
			//			size_x: wgd.size_x,
			//			size_y: wgd.size_y
			//		};
			//	}
			//};

		gridster = gridster.gridster(gridster_params).data('gridster');

		//Build the gridster if the builder has value
		var serialized_value = $('#pix_builder').val();
		if (serialized_value !== 'undefined' && serialized_value.length !== 0) {
			var parsed = JSON.parse(serialized_value);

			// sort serialization
			parsed = Gridster.sort_by_row_and_col_asc(parsed);

			$.each(parsed, function (i, e) {
				var template_args = {
					id: this.id,
					type: this.type,
					content: this.content
				};
				var block_template = get_block_template(template_args);
				gridster.add_widget(block_template, this.size_x, this.size_y, this.col, this.row);
			});
		}

		/**
		 * Events
		 */
		$(document).on('mouseup', '.gridster ul li', function (ev) {
			// lets serialize again
			$(document).trigger('pix_builder:serialize');
		});

		// get the curent number of blocks
		var number_of_blocks = 0;

		if ( $(gridster)[0].$widgets.length > 0 ) {
			number_of_blocks = $(gridster)[0].$widgets.length;
		}

		// Add blocks
		$(document).on('click', '.add_block', function () {
			var type = $(this).siblings('#block_type').val(),
				args = {
					id: parseInt(number_of_blocks) + 1,
					type: type,
					content: ''
				};
			var block_template = get_block_template(args);
			number_of_blocks = parseInt(number_of_blocks) + 1;
			console.log(number_of_blocks);
			gridster.add_widget(block_template, 2, 2);
			//after we done update the json
			$(document).trigger('pix_builder:serialize');
		});

		// Remove block
		$(document).on('click', '.remove_block', function () {
			gridster.remove_widget($(this).parent());
			//after we done update the json
			$(document).trigger('pix_builder:serialize');
		});

		// serialize
		$(document).on('pix_builder:serialize', function () {
			var parsed_string = JSON.stringify(gridster.serialize());
			$('#pix_builder').val(parsed_string);
			console.log(parsed_string);
		});

		// open modal and prepare the editor
		$(document).on('click', '.edit_editor', function (e){

			e.preventDefault();
			var id = $(this).parents('.pix_builder_block').attr('id').replace('block_', '');

			if ( ! modal_container.hasClass('modal_opened') ) {
				modal_container.addClass('modal_opened')
					.show();

				var content = $('#block_'+ id + ' .editor_preview_wrapper').text();

				if ( content !== "" ) {
					tinymce.get('pix_builder_editor').setContent( content.replace(/\n/ig,"<br>") , {format:'text'});
				}

				modal_container.find('.insert_editor_content').attr('data-block_id', id );
			}
		});

		// close modal
		$(document).on('click', '.close_modal_btn', function (){
			close_editor_modal();
		});

		// insert editor content
		$(document).on('click', '.insert_editor_content',function(e){
			e.preventDefault();
			tinyMCE.triggerSave();
			var editor = $('#pix_builder_editor'), // the only portfolio's editor
				editor_val = editor.val(),
				to_send = $('#block_'+ $(this).data('block_id') + ' .to_send');

			$(to_send)
				.text(editor_val);

			$(to_send).next('.editor_preview').find('.editor_preview_wrapper').html(editor_val.replace(/\n/ig,"<br>"));

			insert_content_into_editor( '' );

			$(document).trigger('pix_builder:serialize');

			close_editor_modal();
		});


		var close_editor_modal = function () {
			modal_container.removeClass('modal_opened')
				.hide();
		};

		var insert_content_into_editor = function ( content ){

			var this_editor = tinyMCE.get('pix_builder_editor');

			if( typeof this_editor === "undefined" ) { // text editor
				$('#pix_builder_editor').val( content );
				$('#pix_builder_editor').text( content );

			} else { // visual editor
				this_editor.setContent( content.replace(/\n/ig,"<br>") , {format:'text'});
				this_editor.save( { no_events: true } );
			}
//			console.log( tinyMCE.triggerSave() );
		};

	}); /* Document.ready */

	// get the html for the block
	var get_block_template = function (args) {

		if (typeof args !== 'object') {
			return '';
		}

		var content = '';

		if (args.type === 'text') {
			content = '<textarea value="' + args.content + '">' + args.content + '</textarea>';
		} else if (args.type === 'editor') {
			content = '<div class="to_send" style="display: none">' + args.content + '</div>'+
				'<div class="editor_preview">' +
					'<div class="editor_preview_wrapper">' + args.content + '</div>' +
				'</div>' +
				'<span class="edit_editor">Edit</span>';
		} else if (args.type == 'image') {
			// in case of an image the content should hold only an integer which represents the id
			if (!isNaN(args.content) && args.content !== '') {
				var attach = Attachment.get(args.content);
				attach.fetch({
					async: false,
					success: function () {
						content = '<a class="open_media" href="#" class="wp-gallery" data-attachment_id="' + args.content + '">' + l18n_pix_builder.set_image + '</a>' +
						'<div class="image_preview" style="background-image: url(' + attach.attributes.url + ')"></div>';
					}
				});
			} else {
				content = '<a class="open_media" href="#" class="wp-gallery" data-attachment_id="' + args.content + '">'+ l18n_pix_builder.set_image +'</a>' +
				'<div class="image_preview" />';
			}
		}

		return '<li id="block_' + args.id + '" class="pix_builder_block type-' + args.type + '" data-type="' + args.type + '">' +
		'<span class="drag_handler"></span>' +
		'<div class="block_content">' +
		content +
		'</div>' +
		'<span  class="remove_block">X</span>' +
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
				syncSelection: false
			}, wp.media.controller.Library.prototype.defaults),
			updateSelection: function () {
				var selection = this.get('selection'),
					id = $(last_opened_block).attr('data-attachment_id'),
					attachment;
				if ('' !== id && -1 !== id) {
					console.log(id);
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

					preview_attachment_image(last_opened_block, selected_attach);
					$(document).trigger('pix_builder:serialize');
				}
			}
		};

		$(wp.media.PixBuilderSingleImage.init);

		var preview_attachment_image = function (el, attachment) {
			$(el).parent().find('.image_preview').attr('style' , 'background-image: url(' + attachment.attributes.url + ')');
		}

	}); /* Window.load */

})(jQuery);