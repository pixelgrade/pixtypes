(function ($) {
	$(window).load(function () {
		// Link any localized strings.
		var l10n = wp.media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

		wp.media.EditPixImage = {
			$currentTarget: '',
			/**
			 * Get the featured image post ID
			 *
			 * @global wp.media.view.settings
			 *
			 * @returns {wp.media.view.settings.post.featuredImageId|number}
			 */
			get: function() {
				return -1;
			},
			/**
			 * Set the featured image id, save the post thumbnail data and
			 * set the HTML in the post meta box to the new featured image.
			 *
			 * @global wp.media.view.settings
			 * @global wp.media.post
			 *
			 * @param {number} id The post ID of the featured image, or -1 to unset it.
			 */
			set: function( id ) {
				var settings = wp.media.view.settings,
					$elem = settings.post.$currentTarget,
					$field = $elem.closest( '.piximage_field' );

				$elem.find('.piximage_id').val(id);

				piximage_preview( $field );
			},
			/**
			 * Remove the featured image id, save the post thumbnail data and
			 * set the HTML in the post meta box to no featured image.
			 */
			remove: function() {
				wp.media.EditPixImage.set( -1 );

				var settings = wp.media.view.settings,
					$elem = settings.post.$currentTarget;

				$elem.find('.piximage_id').val('');
			},
			frame: function () {
				if ( this._frame ) {
					wp.media.frame = this._frame;
					return this._frame;
				}
				//var selection = this.select();
				// create our own media iframe
				this._frame = wp.media({
					id: 'piximage-frame',
					title: locals.pixtypes_l18n.setThumbnailImageTitle,
					button: {
						text: 'Use this image'
					},
					filterable: 'uploaded',
					library: {type: 'image'},
					multiple: false  // Set to true to allow multiple files to be selected
				});

				this._frame.on( 'select', this.select );

				return this._frame;
			},

			init: function () {
				var $field = $( '.piximage_field' );

				$field.each( function( id,  elem ) {
					piximage_preview( $( elem ) );
				});

				// piximage_review_number_of_images( $field );

				$field.on( 'click', '.open_piximage', function( event ) {
					var settings = wp.media.view.settings;

					this.$currentTarget =  $( event.currentTarget );
					settings.post.$currentTarget = this.$currentTarget;

					event.preventDefault();
					// Stop propagation to prevent thickbox from activating.
					event.stopPropagation();

					var selection = this.$currentTarget.find( '.piximage_id' ).val();
					wp.media.EditPixImage.set( selection ? selection : -1 );

					wp.media.EditPixImage.frame().open();
				}).on( 'click', '.clear_image', function( event ) {
					var settings = wp.media.view.settings;

					this.$currentTarget =  $( event.currentTarget ).closest('.open_piximage');
					settings.post.$currentTarget = this.$currentTarget;

					event.preventDefault();

					wp.media.EditPixImage.remove();
					return false;
				});
			},
			/**
			 * 'select' callback for Featured Image workflow, triggered when
			 *  the 'Set Featured Image' button is clicked in the media modal.
			 *
			 * @global wp.media.view.settings
			 *
			 * @this wp.media.controller.FeaturedImage
			 */
			select: function() {
				var selection = wp.media.EditPixImage._frame.state().get('selection').toJSON()[0];

				wp.media.EditPixImage.set( selection ? selection.id : -1 );
			}
		};

		$(wp.media.EditPixImage.init);
	});

	var piximage_preview = function( $elem ) {

		var $piximage_ul = $elem.find('ul'),
			id = $elem.find('.piximage_id').val();

		if ( id != '' && id != '-1' ) {
			$.ajax({
				type: "post", url: locals.ajax_url, data: {action: 'ajax_pixgallery_preview', attachments_ids: id},
				beforeSend: function () {
					$elem.find('.open_piximage i').removeClass('dashicons-images-alt2');
				}, //show loading just when link is clicked
				complete: function () {
					$elem.find('.open_piximage i').removeClass('dashicons-update');
				}, //stop showing loading when the process is complete
				success: function (response) {
					var result = JSON.parse(response);
					if (result.success) {
						$piximage_ul.html(result.output);

						piximage_review_number_of_images( $elem );
						$(document ).trigger('pixgallery_ajax_preview');
					}
				}
			});
		} else {
			$piximage_ul.html('');
			piximage_review_number_of_images( $elem );
		}
	};

	var piximage_review_number_of_images = function( $this ) {
		var $image = $this.children('ul'),
			nr_of_images = $image.children('li').length,
			metabox_class = '';

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
})(jQuery);
