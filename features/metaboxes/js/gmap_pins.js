(function($){

	$(document ).on('click', '.add_new_location', function(){

		var $this_field = $(this).siblings('.gmap_pins' ),
			field_name = $this_field.data('field_name' ),
			order = $this_field.find('li' ).length + 1;

		var new_pin = get_pin_template(field_name, order);
		$this_field.append( new_pin );

	});

	$(document ).on('click', '.pin_delete', function(){

		var $pins_list = $(this).parents('.gmap_pins');
		if ( $pins_list.find('.gmap_pin').length < 2 ) {
			alert( l18n_gmap_pins.dont_delete_all_pins);
			return;
		}

		var confirm_delete = confirm( l18n_gmap_pins.confirm_delete );

		if ( confirm_delete === true ) {
			$(this ).parent().remove();
		}

	});

	var get_pin_template = function ( field_name, order ) {

		return output = '<li class="gmap_pin">' +
			'<fieldset class="pin_location_url">' +
				'<label for="' + field_name + '[' + order + '][location_url]" >#' + order + ' ' + l18n_gmap_pins.location_url_label + '</label>' +
				'<input type="text" name="' + field_name + '[' + order + '][location_url]"/>' +
			'</fieldset>' +
			'<fieldset class="pin_name">' +
				'<label for="' + field_name + '[' + order + '][name]" >' + l18n_gmap_pins.name_label + '</label>' +
				'<input type="text" name="' + field_name + '[' + order + '][name]"/>' +
			'</fieldset>' +
		'<span class="pin_delete"></span>' +
		'</li>';

	}

})(jQuery);