(function ($) {
	/**
	 * Global variables
	 */
	var media = wp.media,
		Attachment = media.model.Attachment;

	$(document).ready(function () {

		var gridster = $(".gridster ul"),
			gridster_params = $('#pix_builder').data('params');

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

		var widget_label = 0;

		// Add blocks
		$(document).on('click', '.add_block', function () {
			var type = $(this).siblings('#block_type').val(),
				args = {
					id: parseInt(widget_label) + 1,
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
			$.ajax({
				type: "post",
				url: exports.ajax.settings.url,
				data: {action: 'reset_style_section', type: 'get', _ajax_nonce: _ajax_nonce},
				//beforeSend: function() {jQuery("#loading").show("slow");}, //show loading just when link is clicked
				//complete: function() { jQuery("#loading").hide("fast");}, //stop showing loading when the process is complete
				success: function (response) {
					location.reload();
				},
				error: function () {
					alert('This is wrong!');
				}
			});
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

		return '<li id="' + args.id + '" class="pix_builder_block type-' + args.type + '" data-type="' + args.type + '">' +
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