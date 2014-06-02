(function ($) {
	"use strict";
	$(function () {

		// on page load do a check on github for update

//		$(document).ready(function(){
//
//			// reload likes number
//			$.ajax({
//				type: "post",url: locals.ajax_url,data: { action: 'pixtypes_check_update' },
//				//beforeSend: function() {jQuery("#loading").show("slow");}, //show loading just when link is clicked
//				//complete: function() { jQuery("#loading").hide("fast");}, //stop showing loading when the process is complete
//				success: function( response ){
//					var result = JSON.parse(response);
//					console.log( result );
//				}
//			});
//
//		});

		/**
		 *  Checkbox value switcher
		 *  Any checkbox should switch between value 1 and 0
		 *  Also test if the checkbox needs to hide or show something under it.
		 */
//		$('#pixtypes_form input:checkbox').each(function(i,e){
//			check_checkbox_checked(e);
//			$(e).check_for_extended_options();
//		});
//		$('#pixtypes_form').on('click', 'input:checkbox', function(){
//			check_checkbox_checked(this);
//			$(this).check_for_extended_options();
//		});
		/** End Checkbox value switcher **/

		/* Ensure groups visibility */
		$('.switch input[type=checkbox]').each(function(){

			if ( $(this).data('show_group') ) {

				var show = false;
				if ( $(this).attr('checked') ) {
					show = true
				}

				toggleGroup( $(this).data('show_group'), show);
			}
		});

		$('.switch ').on('change', 'input[type=checkbox]', function(){
			if ( $(this).data('show_group') ) {
				var show = false;
				if ( $(this).attr('checked') ) {
					show = true
				}
				toggleGroup( $(this).data('show_group'), show);
			}
		});

		/** ajax callbacks */
		$('#unset_pixypes').on('click', 'button', function(e){
			var response = confirm('Be sure that you don\'t need this post type anymore');

			if ( response == false ) {
				e.preventDefault();
			} else {
				e.preventDefault();

				var ajax_nounce = $(this).parents('ul').siblings('.unset_nonce').val();
				// reload likes number
				jQuery.ajax({
					type: "post",url: locals.ajax_url,data: { action: 'unset_pixtypes', _ajax_nonce: ajax_nounce, post_type: $(this).val() },
					//beforeSend: function() {jQuery("#loading").show("slow");}, //show loading just when link is clicked
					//complete: function() { jQuery("#loading").hide("fast");}, //stop showing loading when the process is complete
					success: function( response ){
						var result = JSON.parse(response);

						if ( typeof result !== 'undefined' && result.success ) {
							alert( result.msg );
							location.reload();
						}
					}
				});

			}
		});
	});


	var toggleGroup = function( name, show ){
		var $group = $( '#' + name );

		if ( show ) {
			$group.show();
		} else {
			$group.hide();
		}
	};

	/*
	 * Usefull functions
	 */

	function check_checkbox_checked( input ){ // yes the name is ironic
		if ( $(input).attr('checked') === 'checked' ) {
			$(input).siblings('input:hidden').val('on');
		} else {
			$(input).siblings('input:hidden').val('off');
		}
	} /* End check_checkbox_checked() */

	$.fn.check_for_extended_options = function() {
		var extended_options = $(this).siblings('fieldset.group');
		if ( $(this).data('show-next') ) {
			if ( extended_options.data('extended') === true) {
				extended_options
					.data('extended', false)
					.css('height', '0');
			} else if ( (typeof extended_options.data('extended') === 'undefined' && $(this).attr('checked') === 'checked' ) || extended_options.data('extended') === false ) {
				extended_options
					.data('extended', true)
					.css('height', 'auto');
			}
		}
	};

}(jQuery));