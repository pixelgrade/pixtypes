(function( $ ) {

	$( '.cmb-type-pw_select_v2 .select2' ).each(function() {
		$( this ).select2({
			allowClear: true
		});
	});

	$( '.cmb-type-pw_multiselect_v2 .select2, .cmb-type-pw_multiselect_cpt_v2 .select2' ).each(function() {
		var instance = $( this );

		$( instance ).select2({
			multiple: true,
			tags: true
		});

		// $( instance ).select2( 'container' ).find( 'ul.select2-choices' ).sortable({
		// 	containment: 'parent',
		// 	start: function() { $( instance ).select2( 'onSortStart' ); },
		// 	update: function() { $( instance ).select2( 'onSortEnd' ); }
		// });
	});

	function pw_select2_find_text( id, instance_data ) {
		var i, l;

		for ( i = 0, l = instance_data.length; i < l; i++ ) {
			if ( id == instance_data[ i ].id ) {
				return instance_data[ i ].text;
			}
		}
	}

})(jQuery);
