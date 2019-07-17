(function( $ ) {
	$( window ).load( function() {
		// Link any localized strings.
		var l10n = wp.media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

		wp.media.EditPixPlaylist = {
			frame: function() {
				if ( this._frame )
					return this._frame;
				var selection = this.select();
				// create our own media iframe
				this._frame = wp.media( {
					id: 'pixvideos-frame',
					title: 'PixVideos',
					filterable: 'uploaded',
					frame: 'post',
					state: 'video-playlist-edit',
					library: true,
					multiple: true,  // Set to true to allow multiple files to be selected
					editing: true,
					selection: selection
				} );

				var controler = wp.media.EditPixPlaylist._frame.states.get( 'video-playlist-edit' );

				wp.media.EditPixPlaylist._frame.states.add( 'playlist-edit', controler );

				// on update send our attachments ids into a post meta field
				this._frame.on( 'update', function() {
					var controller = wp.media.EditPixPlaylist._frame.states.get( 'playlist-edit' ),
						library = controller.get( 'library' ),
					// Need to get all the attachment ids for gallery
						ids = library.pluck( 'id' );

					$( '#pixplaylist' ).val( ids.join( ',' ) );

					// update the galllery_preview
					pixplaylist_ajax_preview();
				} );

				return this._frame;
			},

			init: function() {
				pixplaylist_ajax_preview();
				$( '#pixvideos' ).on( 'click', '.open_pixvideos', function( e ) {
					e.preventDefault();
					wp.media.EditPixPlaylist.frame().open();
				} );
			},

			select: function() {

				var videos_ids = $( '#pixplaylist' ).val(),
					shortcode = wp.shortcode.next( 'playlist', '[playlist type="' + playlist_locals.pixtypes_l18n.playlist_type + '" ids="' + videos_ids + '"]' ),
					defaultPostId = wp.media.gallery.defaults.id,
					attachments, selection;

				// Bail if we didn't match the shortcode or all of the content.
				if ( !shortcode )
					return;

				// Ignore the rest of the match object.
				shortcode = shortcode.shortcode;

				// quit when we don't have images
				if ( shortcode.get( 'ids' ) == '' ) {
					return;
				}

				if ( _.isUndefined( shortcode.get( 'id' ) ) && !_.isUndefined( defaultPostId ) )
					shortcode.set( 'id', defaultPostId );

				attachments = wp.media.gallery.attachments( shortcode );
				selection = new wp.media.model.Selection( attachments.models, {
					props: attachments.props.toJSON(),
					multiple: true
				} );

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set( {query: false} );
					selection.unmirror();
					//selection.props.unset('orderby');
				} );

				return selection;
			}
		};

		$( wp.media.EditPixPlaylist.init );
	} );

	var pixplaylist_ajax_preview = function() {
		var $playlist =  $( '#pixvideos'),
			$pixgallery_ul = $playlist.children( 'ul' );

		ids = $( '#pixplaylist' ).val();

		if ( ids !== '' ) {
			$.ajax( {
				type: "post",
				url: playlist_locals.ajax_url,
				data: {
					action: 'pixplaylist_preview',
					attachments_ids: ids
				},
				success: function( response ) {
					if ( response.success ) {
						$pixgallery_ul.html( response.data );
						pixvideos_review_number_of_items( $playlist );
						$(document ).trigger('pixplaylist_ajax_preview');
					}
				}
			} );
		} else {
			$pixgallery_ul.html( '' );
			pixvideos_review_number_of_items( $playlist );
		}
	};

	// clear playlist
	$( '#pixvideos' ).on( 'click', '.clear_gallery', function( e ) {
		e.preventDefault();
		e.stopImmediatePropagation();

		var curent_val = $( '#pixplaylist' ).val();
		if ( curent_val !== '' ) {
			var conf = confirm( playlist_locals.pixtypes_l18n.confirmClearGallery );
			if ( conf ) {
				$( '#pixplaylist' ).val( '' );
				pixplaylist_ajax_preview();
			}
		} else {
			alert( playlist_locals.pixtypes_l18n.alertGalleryIsEmpty );
		}
	} );


	var pixvideos_review_number_of_items = function( $this ) {
		var $gallery = $this.children('ul'),
				nr_of_images = $gallery.children('li').length,
				metabox_class = '',
				options_container = $('.cmb-type-playlist');

		if ( nr_of_images < 1 ) {
			metabox_class = 'no-items';
		} else {
			metabox_class = 'has-items';
		}

		if ( metabox_class !== '' ) {
			$this
					.removeClass('no-items has-items hidden')
					.addClass(metabox_class);
		}
	};

})( jQuery );
