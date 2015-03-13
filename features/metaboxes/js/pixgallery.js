(function ($) {
	$(window).load(function () {

		// Link any localized strings.
		var l10n = wp.media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

		wp.media.EditPixGallery = {
			frame: function () {
				if (this._frame)
					return this._frame;
				var selection = this.select();
				// create our own media iframe
				this._frame = wp.media({
					id: 'pixgallery-frame',
					title: 'PixGallery',
					filterable: 'uploaded',
					frame: 'post',
					state: 'gallery-edit',
					library: {type: 'image'},
					multiple: true,  // Set to true to allow multiple files to be selected
					editing: true,
					selection: selection
				});

				var controler =  wp.media.EditPixGallery._frame.states.get('gallery-edit');
				// force display settings off
				controler.attributes.displaySettings = false;

				//but still keep the reverse button in our modal
				controler.gallerySettings = function( browser ) {
					var library = this.get('library');
					if ( ! library || ! browser ) {
						return;
					}

					library.gallery = library.gallery || new Backbone.Model();
					browser.toolbar.set( 'reverse', {
						text:     l10n.reverseOrder,
						priority: 80,
						click: function() {
							library.reset( library.toArray().reverse() );
						}
					});
				};

				wp.media.EditPixGallery._frame.states.add('gallery-edit', controler);

				// on update send our attachments ids into a post meta field
				this._frame.on('update', function () {
					var controller = wp.media.EditPixGallery._frame.states.get('gallery-edit'),
						library = controller.get('library');
						// Need to get all the attachment ids for gallery
						ids = library.pluck('id');

					$('#pixgalleries').val(ids.join(','));

					// update the galllery_preview
					pixgallery_ajax_preview();
				});

				return this._frame;
			},

			init: function () {
				$('#pixgallery').on('click', '.open_pixgallery', function (e) {
					e.preventDefault();
					wp.media.EditPixGallery.frame().open();
				});
			},

			select: function () {
				var galleries_ids = $('#pixgalleries').val(),
					shortcode = wp.shortcode.next('gallery', '[gallery ids="' + galleries_ids + '"]'),
					defaultPostId = wp.media.gallery.defaults.id,
					attachments, selection;

				// Bail if we didn't match the shortcode or all of the content.
				if (!shortcode)
					return;

				// Ignore the rest of the match object.
				shortcode = shortcode.shortcode;

				// quit when we don't have images
				if ( shortcode.get('ids') == '' ) {
					return;
				}

				if (_.isUndefined(shortcode.get('id')) && !_.isUndefined(defaultPostId))
					shortcode.set('id', defaultPostId);

				attachments = wp.media.gallery.attachments(shortcode);
				selection = new wp.media.model.Selection(attachments.models, {
					props: attachments.props.toJSON(),
					multiple: true
				});

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done(function () {
					// Break ties with the query.
					selection.props.set({query: false});
					selection.unmirror();
					//selection.props.unset('orderby');
				});

				return selection;
			}
		};

		pixgallery_ajax_preview();
		$(wp.media.EditPixGallery.init);

	});

	var pixgallery_ajax_preview = function () {

		var ids = '',
			$pixgallery_ul = $('#pixgallery > ul');
		ids = $('#pixgalleries').val();

		if ( ids !== '' ) {
			$.ajax({
				type: "post", url: locals.ajax_url, data: {action: 'ajax_pixgallery_preview', attachments_ids: ids},
				beforeSend: function () {
					$('.open_pixgallery i').removeClass('icon-camera-retro');
					$('.open_pixgallery i').addClass('icon-spin icon-refresh');
				}, //show loading just when link is clicked
				complete: function () {
					$('.open_pixgallery i').removeClass('icon-spin icon-refresh');
					$('.open_pixgallery i').addClass('icon-camera-retro');
				}, //stop showing loading when the process is complete
				success: function (response) {
					var result = JSON.parse(response);
					if (result.success) {
						$pixgallery_ul.html(result.output);
					}
				}
			});
		} else {
			$pixgallery_ul.html('');
		}
	};

	//init
	if ($("[id$='_post_slider_visiblenearby']").val() == 1) {
		//we need to hide the transition because it will not be used
		$("[id$='_post_slider_transition']").closest('tr').hide();
	}

	$("[id$='_post_slider_visiblenearby']").on('change', function () {
		if ($(this).val() == 1) {
			//we need to hide the transition because it will not be used
			$("[id$='_post_slider_transition']").closest('tr').fadeOut();
		} else {
			$("[id$='_post_slider_transition']").closest('tr').fadeIn();
		}
	});

	//for the autoplay
	//init
	if ($("[id$='_post_slider_autoplay']").val() != 1) {
		//we need to hide the delay because it will not be used
		$("[id$='_post_slider_delay']").closest('tr').hide();
	}

	$("[id$='_post_slider_autoplay']").on('change', function () {
		if ($(this).val() == 1) {
			//we need to hide the delay because it will not be used
			$("[id$='_post_slider_delay']").closest('tr').fadeIn();
		} else {
			$("[id$='_post_slider_delay']").closest('tr').fadeOut();
		}
	});

	// Clear gallery
	$('#pixgallery').on('click', '.clear_gallery', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();

		var curent_val = $('#pixgalleries').val();
		if ( curent_val !== '' ) {
			var conf = confirm(locals.pixtypes_l18n.confirmClearGallery);
			if ( conf ) {
				$('#pixgalleries').val('');
				pixgallery_ajax_preview();
			}
		} else {
			alert(locals.pixtypes_l18n.alertGalleryIsEmpty);
		}
	});

})(jQuery);
