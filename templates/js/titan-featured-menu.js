jQuery(document).ready(function($) {
	$( ".listingwrapper" ).each(function() {
		var titanform = $(this).find('.appointment-forms');
		$(this).find(".appointment-forms-link").click(function() {
				$(titanform).dialog({width:500});
				return false;
		});
	});
});