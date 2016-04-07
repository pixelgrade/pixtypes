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

			gridster_params.on_resize_callback[3]);

		///**
		// * use this to serialize these params
		// * after that echo them in activation.php config
		// */
		// var gridster_params = {
		// 	widget_margins: [30, 30],
		// 	widget_base_dimensions: [150, 100],
		// 	min_cols: 3,
		// 	max_cols: 6,
		// 	autogenerate_stylesheet: true,
		// 	resize: {
		// 		enabled: true,
		// 		axes: ['x'],
		//		resize: function (el, ui, $widget) {
		//			var size_x = this.resize_wgd.size_x;
		//			if ( size_x == 5 ) {
		//				// get the closest widget size
		//				var cws = this.resize_last_sizex;
		//				// force the widget size to 6
		//				$(this.resize_wgd.el).attr('data-sizex', cws);
		//				this.resize_wgd.size_x = cws;
		//				// now the widget preview
		//				var preview = $(this.resize_wgd.el).find('.preview-holder');
		//				preview.attr('data-sizex', cws);
		//				this.$resize_preview_holder.attr('data-sizex', cws);
		//				$(document).trigger('pix_builder:serialize');
		//		}
		// 	},
		// 	draggable: {
		// 		handle: '.drag_handler'
		// 	},
		// 	serialize_params: function ($w, wgd) {
		// 		var type = $w.data("type"),
		// 			content = $w.find(".block_content").text();
		// 		if (type == "text") {
		// 			content = $w.find(".block_content textarea").val();
		// 		} else if (type == "image") {
		// 			content = $w.find(".open_media").attr("data-attachment_id");
		// 		} else if (type == "editor") {
		// 			content = $w.find(".to_send").text();
		// 		}
		// 		return {
		// 			id: $w.prop("id").replace("block_", ""),
		// 			type: type,
		// 			content: content,
		// 			col: wgd.col,
		// 			row: wgd.row,
		// 			size_x: wgd.size_x,
		// 			size_y: wgd.size_y
		// 		};
		// 	}
		// };

		var widget_width = $('#normal-sortables').width() / 6;
		gridster_params.widget_base_dimensions = [ widget_width - 67 , 40];

		gridster = gridster.gridster(gridster_params).data('gridster');

		//Build the gridster if the builder has value
		//var serialized_value = $pix_builder.val();
		//if (serialized_value !== 'undefined' && serialized_value.length !== 0) {
		//	var parsed = JSON.parse(serialized_value);
		//
		//	// sort serialization
		//	parsed = Gridster.sort_by_row_and_col_asc(parsed);
		//
		//	$.each(parsed, function (i, e) {
		//		var template_args = {
		//			id: this.id,
		//			type: this.type,
		//			content: this.content
		//		};
		//		//debugger;
		//		var block_template = get_block_template(template_args);
		//		gridster.add_widget(block_template, this.size_x, this.size_y, this.col, this.row);
		//	});
		//}

		// get the curent number of blocks
		var number_of_blocks = 0;

		if ( $(gridster)[0].$widgets.length > 0 ) {
			number_of_blocks = $(gridster)[0].$widgets.length;
		}

		// Functions
		/**
		 * Checks if a serialisation event is already ongoing
		 * or start one if not
		 */
		var intent_to_serialize = function() {
			if ( ! serialize_intention ) {
				serialize_timeout = setTimeout( serialize_pix_builder_values, 1000);
				serialize_intention = true;
			} else {
				// kill the timout and start a new one
				clearTimeout(serialize_timeout);
				serialize_timeout = setTimeout( serialize_pix_builder_values, 1000);
			}
		};

		var serialize_pix_builder_values = function(){
			var new_values = gridster.serialize();

			// sort_them
			new_values = Gridster.sort_by_row_and_col_asc(new_values);

			$.each( new_values, function ( i, j) {
				if ( j.hasOwnProperty('content') ) {
					if ( j.type === 'editor') {
						new_values[i].content = $.base64.encode( j.content );
					}
				}
			});


			var parsed_string = JSON.stringify(new_values);
			var content_editor = tinyMCE.get('content');

			if( typeof content_editor === "undefined" || content_editor === null) { // text editor
				$('#content').val( parsed_string );
				$('#content').text( parsed_string );
			} else { // visual editor
				content_editor.setContent( parsed_string.replace(/\n/ig,"<br>") , {format:'text'});
			}

			// $('#content').val(parsed_string);
			// $('#pix_builder').val(parsed_string);
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
				this_editor.setContent( content.replace(/\n/ig,"<br>") , {format:'text'});
				this_editor.save( { no_events: true } );
			}
		};

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

			var type = $(this).val(),
				args = {
					id: parseInt(number_of_blocks) + 1,
					type: type,
					content: ''
				};
			var block_template = get_block_template(args);
			number_of_blocks = parseInt(number_of_blocks) + 1;
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

		// $(document).on('click', '#publishing-action', function(){
		// 	serialize_pix_builder_values();
		// });

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

		// on builders pages keep the editori hidden
		$('#postdivrich').hide();
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
							'<div class="position__ui-cell left">' +
								'<div class="position__ui-handle">left</div>' +
							'</div>' +
							'<div class="position__ui-cell middle active">' +
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

		// margins?
		$('.pixbuilder-grid').on('click', '.position__ui-cell', function(e) {
			var $cell = $(this),
				$container = $cell.closest('.position__ui'),
				$active = $container.find('.position__ui-cell.active'),
				$item = $cell.find('.position__ui-handle'),
				step = $item.attr('data-step'),
				$target = $active.filter('.middle');

			if ( $cell.is('.middle') ) $target = $active;
			if ( $cell.is('.top') && $active.filter('.bottom').length ) $target = $active.filter('.bottom');
			if ( $cell.is('.right') && $active.filter('.left').length ) $target = $active.filter('.left');
			if ( $cell.is('.bottom') && $active.filter('.top').length ) $target = $active.filter('.top');
			if ( $cell.is('.left') && $active.filter('.right').length ) $target = $active.filter('.right');

			$target.removeClass('active');
			$target.find('.position__ui-handle').attr('data-step', 0);

			$cell.addClass('active');

			if ( typeof step === "undefined" ) {
				step = 1;
			} else if ( step == 3 ) {
				step = 0;
			} else {
				step = parseInt(step) + 1;
			}

			$item.attr('data-step', step);
		});

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

/**
 * jQuery Plugin - base64 codec
 * @lisence MIT License https://github.com/yatt/jquery.base64/blob/master/license.txt
 * @author yatt/brainfs http://d.hatena.ne.jp/yatt http://twitter.com/brainfs
 * @version 0.0.1
 * @info
 */

(function($){
	var base64module = {};

// *** begin
	/* /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/
	 charset = shift_jis

	 +++ Base64 Encode / Decode +++


	 LastModified : 2006-11/08

	 Powered by kerry
	 http://202.248.69.143/~goma/

	 動作ブラウザ :: IE4+ , NN4.06+ , Gecko , Opera6+


	 * [RFC 2045] Multipurpose Internet Mail Extensions
	 (MIME) Part One:
	 Format of Internet Message Bodies
	 ftp://ftp.isi.edu/in-notes/rfc2045.txt

	 /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/

	 *   Usage:

	 // エンコード
	 b64_string = base64.encode( my_data [, strMode] );

	 // デコード
	 my_data = base64.decode( b64_string [, strMode] );


	 strMode -> 入力データが文字列の場合 1 を

	 /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/ */


// [yatt] enclose
//base64 = new function()
	var base64 = new function()
//
	{
		var utfLibName  = "utf";
		var b64char     = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		var b64encTable = b64char.split("");
		var b64decTable = [];
		for (var i=0; i<b64char.length; i++) b64decTable[b64char.charAt(i)] = i;

		this.encode = function(_dat, _strMode)
		{
			return encoder( _strMode? unpackUTF8(_dat): unpackChar(_dat) );
		}

		var encoder = function(_ary)
		{
			var md  = _ary.length % 3;
			var b64 = "";
			var i, tmp = 0;

			if (md) for (i=3-md; i>0; i--) _ary[_ary.length] = 0;

			for (i=0; i<_ary.length; i+=3)
			{
				tmp = (_ary[i]<<16) | (_ary[i+1]<<8) | _ary[i+2];
				b64 +=  b64encTable[ (tmp >>>18) & 0x3f]
					+   b64encTable[ (tmp >>>12) & 0x3f]
					+   b64encTable[ (tmp >>> 6) & 0x3f]
					+   b64encTable[ tmp & 0x3f];
			}

			if (md) // 3の倍数にパディングした 0x0 分 = に置き換え
			{
				md = 3- md;
				b64 = b64.substr(0, b64.length- md);
				while (md--) b64 += "=";
			}

			return b64;
		}

		this.decode = function(_b64, _strMode)
		{
			var tmp = decoder( _b64 );
			return _strMode? packUTF8(tmp): packChar(tmp);
		}

		var decoder = function(_b64)
		{
			_b64    = _b64.replace(/[^A-Za-z0-9\+\/]/g, "");
			var md  = _b64.length % 4;
			var j, i, tmp;
			var dat = [];

			// replace 時 = も削っている。その = の代わりに 0x0 を補間
			if (md) for (i=0; i<4-md; i++) _b64 += "A";

			for (j=i=0; i<_b64.length; i+=4, j+=3)
			{
				tmp = (b64decTable[_b64.charAt( i )] <<18)
					| (b64decTable[_b64.charAt(i+1)] <<12)
					| (b64decTable[_b64.charAt(i+2)] << 6)
					|  b64decTable[_b64.charAt(i+3)];
				dat[ j ]    = tmp >>> 16;
				dat[j+1]    = (tmp >>> 8) & 0xff;
				dat[j+2]    = tmp & 0xff;
			}
			// 補完された 0x0 分削る
			if (md) dat.length -= [0,0,2,1][md];

			return dat;
		}

		var packUTF8    = function(_x){ return utf.packUTF8(_x) };
		var unpackUTF8  = function(_x){ return utf.unpackUTF8(_x) };
		var packChar    = function(_x){ return utf.packChar(_x) };
		var unpackChar  = function(_x){ return utf.unpackChar(_x) };
//    var packUTF8    = function(_x){ return window[utfLibName].packUTF8(_x) };
//    var unpackUTF8  = function(_x){ return window[utfLibName].unpackUTF8(_x) };
//    var packChar    = function(_x){ return window[utfLibName].packChar(_x) };
//    var unpackChar  = function(_x){ return window[utfLibName].unpackChar(_x) };
	}


	/* /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/
	 charset = shift_jis

	 +++ UTF8/16 ライブラリ +++


	 LastModified : 2006-10/16

	 Powered by kerry
	 http://202.248.69.143/~goma/

	 動作ブラウザ :: IE4+ , NN4.06+ , Gecko , Opera6+



	 * [RFC 2279] UTF-8, a transformation format of ISO 10646
	 ftp://ftp.isi.edu/in-notes/rfc2279.txt

	 * [RFC 1738] Uniform Resource Locators (URL)
	 ftp://ftp.isi.edu/in-notes/rfc1738.txt

	 /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/

	 Usage:

	 // 文字列を UTF16 (文字コード) へ
	 utf16code_array = utf.unpackUTF16( my_string );

	 // 文字列を UTF8 (文字コード) へ
	 utf8code_array = utf.unpackUTF8( my_string );

	 // UTF8 (文字コード) から文字列へ。 utf.unpackUTF8() したものを元に戻す
	 my_string = utf.packUTF8( utf8code_array );

	 // UTF8/16 (文字コード) を文字列へ
	 my_string = utf.packChar( utfCode_array );

	 // UTF16 (文字コード) から UTF8 (文字コード) へ
	 utf8code_array = utf.toUTF8( utf16code_array );

	 // UTF8 (文字コード) から UTF16 (文字コード) へ
	 utf16code_array = utf.toUTF16( utf8code_array );



	 // URL 文字列へエンコード
	 url_string = utf.URLencode( my_string );

	 // URL 文字列からデコード
	 my_string = utf.URLdecode( url_string );

	 /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/ */


// [yatt] enclose
//utf = new function()
	var utf = new function()
//
	{
		this.unpackUTF16 = function(_str)
		{
			var i, utf16=[];
			for (i=0; i<_str.length; i++) utf16[i] = _str.charCodeAt(i);
			return utf16;
		}

		this.unpackChar = function(_str)
		{
			var utf16 = this.unpackUTF16(_str);
			var i,n, tmp = [];
			for (n=i=0; i<utf16.length; i++) {
				if (utf16[i]<=0xff) tmp[n++] = utf16[i];
				else {
					tmp[n++] = utf16[i] >> 8;
					tmp[n++] = utf16[i] &  0xff;
				}
			}
			return tmp;
		}

		this.packChar  =
			this.packUTF16 = function(_utf16)
			{
				var i, str = "";
				for (i in _utf16) str += String.fromCharCode(_utf16[i]);
				return str;
			}

		this.unpackUTF8 = function(_str)
		{
			return this.toUTF8( this.unpackUTF16(_str) );
		}

		this.packUTF8 = function(_utf8)
		{
			return this.packUTF16( this.toUTF16(_utf8) );
		}

		this.toUTF8 = function(_utf16)
		{
			var utf8 = [];
			var idx = 0;
			var i, j, c;
			for (i=0; i<_utf16.length; i++)
			{
				c = _utf16[i];
				if (c <= 0x7f) utf8[idx++] = c;
				else if (c <= 0x7ff)
				{
					utf8[idx++] = 0xc0 | (c >>> 6 );
					utf8[idx++] = 0x80 | (c & 0x3f);
				}
				else if (c <= 0xffff)
				{
					utf8[idx++] = 0xe0 | (c >>> 12 );
					utf8[idx++] = 0x80 | ((c >>> 6 ) & 0x3f);
					utf8[idx++] = 0x80 | (c & 0x3f);
				}
				else
				{
					j = 4;
					while (c >> (6*j)) j++;
					utf8[idx++] = ((0xff00 >>> j) & 0xff) | (c >>> (6*--j) );
					while (j--)
						utf8[idx++] = 0x80 | ((c >>> (6*j)) & 0x3f);
				}
			}
			return utf8;
		}

		this.toUTF16 = function(_utf8)
		{
			var utf16 = [];
			var idx = 0;
			var i,s;
			for (i=0; i<_utf8.length; i++, idx++)
			{
				if (_utf8[i] <= 0x7f) utf16[idx] = _utf8[i];
				else
				{
					if ( (_utf8[i]>>5) == 0x6)
					{
						utf16[idx] = ( (_utf8[i] & 0x1f) << 6 )
							| ( _utf8[++i] & 0x3f );
					}
					else if ( (_utf8[i]>>4) == 0xe)
					{
						utf16[idx] = ( (_utf8[i] & 0xf) << 12 )
							| ( (_utf8[++i] & 0x3f) << 6 )
							| ( _utf8[++i] & 0x3f );
					}
					else
					{
						s = 1;
						while (_utf8[i] & (0x20 >>> s) ) s++;
						utf16[idx] = _utf8[i] & (0x1f >>> s);
						while (s-->=0) utf16[idx] = (utf16[idx] << 6) ^ (_utf8[++i] & 0x3f);
					}
				}
			}
			return utf16;
		}

		this.URLencode = function(_str)
		{
			return _str.replace(/([^a-zA-Z0-9_\-\.])/g, function(_tmp, _c)
			{
				if (_c == "\x20") return "+";
				var tmp = utf.toUTF8( [_c.charCodeAt(0)] );
				var c = "";
				for (var i in tmp)
				{
					i = tmp[i].toString(16);
					if (i.length == 1) i = "0"+ i;
					c += "%"+ i;
				}
				return c;
			} );
		}

		this.URLdecode = function(_dat)
		{
			_dat = _dat.replace(/\+/g, "\x20");
			_dat = _dat.replace( /%([a-fA-F0-9][a-fA-F0-9])/g,
				function(_tmp, _hex){ return String.fromCharCode( parseInt(_hex, 16) ) } );
			return this.packChar( this.toUTF16( this.unpackUTF16(_dat) ) );
		}
	}

// *** end

	// add functions
	$.extend({
		base64: {
			encode: base64.encode,
			decode: base64.decode,
			codec: typeof atob == 'function' ? 'builtin' : 'alternate'
		}
	})

	//
	// override jQuery.ajax:
	// if ajax 'dataType' option value ended by ':b64', then
	// decode base64 string automatically
	//
	$.ajax = (function(ajax){
		return function(option){
			var flg = 0

			// dataType string ended by ':b64' or not?
			if (option.dataType && option.dataType.match(/:b64/)){
				option.dataType = option.dataType.replace(':b64', '')
				flg = 1
			}

			if (flg){
				option.success = (function(callback){
					return function(data, status, xhr){
						data = $.base64.decode(data)
						callback(data, status, xhr)
					}
				})(option.success || function(data, status, xhr){})
			}

			return ajax.apply(this, arguments)
		}
	})($.ajax)

})(jQuery)