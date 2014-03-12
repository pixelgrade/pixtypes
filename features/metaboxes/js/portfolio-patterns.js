;(function($){

	$(document).ready(function(){

		var portfolio_patterns = $('#portfolio-patterns'),
			$editor_modal = $('#wpgrade_portfolio_editor_modal');

		$(document).on('click', '.row-controls a', function(e){
			e.preventDefault();
		});

		// open modal
		$(document).on('click', '.edit_editor',function(e){
			e.preventDefault();
			var this_editor = $(this).data('editor'),
				content = $('#'+ this_editor).text();
			$editor_modal.show();

			if ( content !== "" ) {
				tinymce.get('the_only_editor').setContent( content.replace(/\n/ig,"<br>") , {format:'text'});
			}

			$editor_modal.find('.insert_editor_content').data('editor', this_editor );
		});

		// insert editor content
		$(document).on('click', '#wpgrade_portfolio_editor_modal .insert_editor_content',function(e){
			e.preventDefault();
			tinyMCE.triggerSave();
			var editor = $('#the_only_editor'), // the only portfolio's editor
				editor_val = editor.val(),
				to_send = $('#'+ $(this).data('editor') );

			$(to_send)
				.text(editor_val);

			$(to_send).next('.editor_preview').find('.editor_preview_wrapper').html(editor_val.replace(/\n/ig,"<br>"));
			tinymce.get('the_only_editor').setContent('');
			$editor_modal.hide();
		});

		// close modal
		$(document).on('click', '#wpgrade_portfolio_editor_modal .close_modal_btn',function(e){
			e.preventDefault();
			$editor_modal.hide();
		});

		// create a new row
		$(document).on('click', '.row-controls .add-new', function(e){

			e.preventDefault();
			var self = this,
				type = $(this).val();

			// prepare the new row id
			var key = 0;

			$('.portolio-pattern').each(function(){
				var new_key = $(this).data('key');
				if ( new_key >= key ) {
					key = new_key;
				}
			});

			key = parseInt(key) + 1;
			type = 1;
			var result = '';

			var preview = '<li class="portolio-pattern to_replace" id="row-'+ key +'" data-key="'+ key +'">'+
				'<div class="row-controls">'+
				'<a class="btn order">Order</a>'+
				'<div class="btn-group">'+
				'<a class="btn dropdown-toggle" data-toggle="disabled">Edit <span class="caret-down"></a>'+
				'<ul class="dropdown-menu row-versions"><li><a href="#" data-type="1"></a></li>'+
				'<div class="row-title">Choose a layout style:</div>'+
				'<div class="row-remove"> <a><span class="icon-trash"></span>Remove this row</a> </div>'+
				'</ul>'+
				'</div>'+
//                    '<a class="btn add-new">Add</a>'+
				'</div>'+
				'<div class="row-content">'+
				'<div class="row-fluid">'+
				'<input type="hidden" name=\'pattern_type\' value="1" class="to_meta"/>'+
				'<div class="span4">'+
				'<div class="image-box pattern_upload_button" id="image-'+ key +'-1">'+
				'<label class="hidden" for="image-'+ key +'-1">Image 1</label>'+
				'<input type="hidden" name=\'image-'+ key +'-1\' class="to_meta" />'+
				'<div class="image-preview"></div>'+
				'</div>'+
				'</div>'+

				'<div class="span4 span-border ">'+
				'<div class="row-fluid editor-box loading"><span></span><span></span><span></span></div>'+
				'<div class="row-fluid span-border-top">'+
				'<div class="image-box image-long pattern_upload_button" id="image-'+ key +'-2">'+
				'<label class="hidden" for="image-'+ key +'-2">Image 2</label>'+
				'<input type="hidden" name=\'image-'+ key +'-2\' class="to_meta" />'+
				'<div class="image-preview"></div>'+
				'</div>'+
				'</div>'+
				'</div>'+

				'<div class="span4">'+
				'<div class="image-box pattern_upload_button" id="image-'+ key +'-3">'+
				'<label class="hidden" for="image-'+ key +'-3">Image 3</label>'+
				'<input type="hidden" name=\'image-'+ key +'-3\' class="to_meta" />'+
				'<div class="image-preview"></div>'+
				'</div>'+
				'</div>'+
				'</div>'+
				'</div>'+
				'</li>';

			$(self).parents('li.portolio-pattern').after(preview);

			$.ajax({
				url:ajaxurl,
				type:'GET',
				data:'action=cmb_portfolio_handler&type=' + type +'&key='+ key,
				success:function(response)
				{
					result = JSON.parse(response);
					if ( result['success'] ) { // we the ajax request was successful we add the new row
						$(self).parents('#portfolio-patterns').find('li.to_replace').replaceWith(result['output']);
						var editor_id = 'editor-' + key + '-2';
						// init the wp_editor ...
//                        tinymce.execCommand('mceAddControl',false, editor_id);
						// slide the row down
						$('#row-'+ key).slideDown(800);
					}
//                    $('.add_new_row .css_loader').fadeOut(800);
				}
			});
		});

		// delete a row
		$(document).on('click', '.row-controls .row-remove a', function(e){
			e.preventDefault();
			$(this).parents('li.portolio-pattern').fadeOut(400).remove();
		});

		// changing a row type ... this should be fun
		$(document).on('click', '.row-controls .row-versions li a', function(){

			var type = $(this).data('type'),
				self = $(this),
				pattern = $(this).parents('.portolio-pattern');

			pattern.addClass('to_replace');
			pattern.prepend('<div class=" full-width loading"><span></span><span></span><span></span></div>');

			// prepare the new row id
			var key = $(pattern).attr('id');
			key = key.split('-');
			key = key[1];

			// check we already have values in our row
			tinyMCE.triggerSave(); // update wp_editors

			var pattern_content = {};
			$(pattern).find('.to_meta').each(function(i,e){
				pattern_content[ $(e).attr("name") ] = $(e).val();
			});

			pattern_content  = JSON.stringify( pattern_content );

			$.post(
				ajaxurl,
				{
					action: 'cmb_portfolio_row_type_change',
					new_type: type,
					row_id: key,
					pattern: pattern_content
				},
				function(response)
				{
					var $this_row = $('#row-'+key );
					result = JSON.parse(response);
					$this_row.replaceWith( result.output);
					$this_row.slideDown(300);
				}
			);
		});

		// drag and drop
		portfolio_patterns.sortable({
			handle: '.row-controls .btn.order',
			cursor: "move",
			containment: '#portfolio-patterns',
			forceHelperSize: true,
			forcePlaceholderSize: true,
			items: "> li",
			opacity: 0.4,
			placeholder: "sortable-placeholder",
			tolerance: 'pointer',
			stop: function( event, ui ) {
			},
			over: function( event, ui){
				ui.item.css({outline: '#00FF00 dotted thin'});
			},
			beforeStop: function( event, ui ) {
				ui.item.css('outline', 'none');
			}
		});
		portfolio_patterns.disableSelection();

		// on post save
		$('#post').on('submit', function(){
			tinyMCE.triggerSave(); // update wp_editors
			var meta_value = {},
				patterns = $(this).find('#portfolio-patterns .portolio-pattern');

			// get each pattern's meta, encode it and put let it go in db as portfolio_gallery_val
			patterns.each(function(i,e){
				meta_value[i] = {};
				var inputs = $(this).find('.to_meta');
				inputs.each(function(k,el){
					var name = $(el).attr('name');
					meta_value[i][name] = $(el).val();
				});
			});

			var stringed = JSON.stringify(meta_value);
//            var encoded = Base64.encode( stringed );
			$('#portfolio_gallery_val').val( stringed );

			return true;
		});

		var formfield;

		// create image upload preview
		$(document).on('click', '.pattern_upload_button, .cmb_edit_file', function () {
			var buttonLabel;
			formfield = $(this).attr('name');

			if ( !formfield ) {
				formfield = $(this).attr('id');
			}

			buttonLabel = 'Use as portfolio image';
			tb_show('', 'media-upload.php?post_id=' + $('#post_ID').val() + '&type=file&cmb_force_send=true&cmb_send_label=' + buttonLabel + '&TB_iframe=true');
			return false;
		});

		// remove image preview button
		$(document).on('click', '.cmb_remove_file_button', function () {
			formfield = $(this).attr('rel');

			$('#' + formfield +' > input').val('');
//            $('#' + formfield + '_id').val('');
			$(this).parent().remove();
			return false;
		});

		$(document).on('click', 'a.clear_upload_btn', function(ev){
			ev.preventDefault();
			$(this).parent().find('input.to_meta').val('');
		});

		window.portfolio_send_to_editor = window.send_to_editor;
		window.send_to_editor = function (html) {
			var itemurl, itemclass, itemClassBits, itemid, htmlBits, itemtitle,
				image, uploadStatus = true;

			if (formfield) {

				if ($(html).html(html).find('img').length > 0) {
					itemurl = $(html).html(html).find('img').attr('src'); // Use the URL to the size selected.
					itemclass = $(html).html(html).find('img').attr('class'); // Extract the ID from the returned class name.
					itemClassBits = itemclass.split(" ");
					$.each(itemClassBits,function(i,e){
						if (e.match( /wp-image-/g ) ) {
							itemid = e.replace('wp-image-', '');
						}
					});
				}

				if ( typeof itemurl === 'undefined' ) { // no image was found so it must other kind of file with a link attached

					itemurl = $(html).html(html).find('a').attr('href');
					var this_video = $('#' + formfield ).parent('.video_field');
					if ( this_video.length > 0 ) {
						this_video.find( 'input.to_meta').val(itemurl);
					}

				} else {

					image = /(jpe?g|png|gif|ico)$/gi;

					if (itemurl.match(image)) {
						uploadStatus = '<div class="img_status" style="background-image: url('+ itemurl +')"><a href="#" class="cmb_remove_file_button" rel="' + formfield + '" title="Remove Image">x</a> <a href="#" rel="' + formfield+'" class="cmb_edit_file" title="Edit Image">e</a></div>';
					}

					var meta_val = {};

					meta_val['link'] = itemurl;
					meta_val['id'] = itemid;
					meta_val = JSON.stringify( meta_val );

					if ( $('#' + formfield ).find('input').length > 0 ) {
						$('#' + formfield + ' > input').val( meta_val );
						$('#' + formfield).find('.image-preview').html(uploadStatus).slideDown();
					} else {
						$('#' + formfield).val(itemurl);
					}
				}

				tb_remove();

			} else {
				window.portfolio_send_to_editor(html);
			}
			formfield = '';
		};
	});
})(jQuery);

/**
 *  Base64 encode / decode
 *  http://www.webtoolkit.info/
 **/
var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
				this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
				this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}