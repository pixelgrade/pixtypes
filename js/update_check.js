(function ($) {
	$(document).ready(function(){
		// check update
		$.ajax({ type: "post",url: ajaxurl,data: { action: 'check_for_pix_plugins_updates' }});
	});
})(jQuery);