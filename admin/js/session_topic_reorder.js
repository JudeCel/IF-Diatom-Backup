(function($){
	$(document).ready(function(){
		var sortable_items = $('#sortable');

		if(sortable_items.length){
			//sortable code
			sortable_items.sortable();
			sortable_items.disableSelection();
		}		
	});
})(jQuery);