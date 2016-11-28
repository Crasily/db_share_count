jQuery(document).ready( function($) {
	$.getJSON(dbsc.ajax_url, {
			action: 'dbsc_get_counts',
			post_id: dbsc.post_id,
		}, function(data) {
			if(typeof data !== 'undefined') {
				for(var key in data) {
					var value = data[key];
					if(value > dbsc.min_count_display) {
						$("#dbsc_count_" + key).css('display', 'inherit');
					}
					var width = $("#dbsc_count_" + key).css('width');
					$("#dbsc_count_" + key).text(value);
					var newWidth = $("#dbsc_count_" + key).css('width');
					$("#dbsc_count_" + key).css('width', width);
					$("#dbsc_count_" + key).animate({'width': newWidth}, 400);
				}
			}
		}
	);

})
