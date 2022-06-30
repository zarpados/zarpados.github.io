jQuery(document).ready(function($) {
	$('#the-list').sortable({
		items: 'tr',
		opacity: 0.6,
		cursor: 'move',
		axis: 'y',
		update: function() {
			var order = $(this).sortable('serialize') + '&action=pa_order_update_posts';
			$.post(ajaxurl, order, function(response) {
			});
		}
		
	});
});