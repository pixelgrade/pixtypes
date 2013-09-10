(function($){
	$(window).load(function(){

		wp.media.EditPixGallery = {

			frame: function() {
				if ( this._frame )
					return this._frame;
				var selection = this.select();
				// create our own media iframe
				this._frame = wp.media({
					displaySettings:    false,
					id:                 'pixgallery-frame',
					title:              'PixGallery',
					filterable:         'uploaded',
					frame:              'post',
					state:              'gallery-edit',
					library:            { type : 'image' },
					multiple:           true,  // Set to true to allow multiple files to be selected
					editing:            true,
					selection:          selection
				});

				// on update send our attachments ids into a post meta field
				this._frame.on( 'update',
					function() {
						var controller = wp.media.EditPixGallery._frame.states.get('gallery-edit');
						var library = controller.get('library');
						// Need to get all the attachment ids for gallery
						var ids = library.pluck('id');

						$('#pixgalleries').val( ids.join(',') );

						// update the galllery_preview
						pixgallery_ajax_preview();

						return false;
					});

				return this._frame;
			},

			init: function() {

				$('#pixgallery').on('click', '.open_pixgallery', function(e){
					e.preventDefault();
					wp.media.EditPixGallery.frame().open();
				});
			},

			select: function(){
				var galleries_ids = $('#pixgalleries').val(),
					shortcode = wp.shortcode.next( 'gallery', '[gallery ids="'+ galleries_ids +'"]' ),
					defaultPostId = wp.media.gallery.defaults.id,
					attachments, selection;
				// Bail if we didn't match the shortcode or all of the content.
				if ( ! shortcode )
					return;

				// Ignore the rest of the match object.
				shortcode = shortcode.shortcode;

				if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
					shortcode.set( 'id', defaultPostId );

				attachments = wp.media.gallery.attachments( shortcode );
				selection = new wp.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
				});

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
				});

				return selection;
			}
		};

		pixgallery_ajax_preview();
		$( wp.media.EditPixGallery.init );

	});

	var pixgallery_ajax_preview = function(){

		var ids = $('#pixgalleries').val();
		$.ajax({
			type: "post",url: locals.ajax_url,data: { action: 'ajax_pixgallery_preview', attachments_ids: ids },
			beforeSend: function() {
				$('.open_pixgallery i').removeClass('icon-camera-retro');
				$('.open_pixgallery i').addClass('icon-spin icon-refresh');
			}, //show loading just when link is clicked
			complete: function() {
				$('.open_pixgallery i').removeClass('icon-spin icon-refresh');
				$('.open_pixgallery i').addClass('icon-camera-retro');
			}, //stop showing loading when the process is complete
			success: function( response ){
				var result = JSON.parse(response);
				if (result.success ) {
					$('#pixgallery > ul').html(result.output);
				}
			}
		});
	};
})(jQuery);
