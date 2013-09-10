(function ($) {
	"use strict";
	$(function () {

		/**
		 *  Checkbox value switcher
		 *  Any checkbox should switch between value 1 and 0
		 *  Also test if the checkbox needs to hide or show something under it.
		 */
		$('#PixTypes_form input:checkbox').each(function(i,e){
			check_checkbox_checked(e);
			$(e).check_for_extended_options();
		});
		$('#PixTypes_form').on('click', 'input:checkbox', function(){
			check_checkbox_checked(this);
			$(this).check_for_extended_options();
		});
		/** End Checkbox value switcher **/

	});

	/*
	 * Usefull functions
	 */

	function check_checkbox_checked( input ){ // yes the name is an ironic
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