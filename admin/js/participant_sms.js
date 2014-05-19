(function($){
	$(document).ready(function(){
		$('#message').NobleCount('#count4',{
			on_negative: 'go_red',
			on_positive: 'go_green',
			max_chars: 160
		});

		$.each(participants_found, function(){
			var self = $(this)[0],
					pid = self.id;

			$("#mobile_edit" + pid).editable("save-comments.php?participant_id=" + pid, { 
			  name: "mobile",
			  indicator : "Saving...",
			  tooltip   : "Click to edit...",
			  event     : "click",
			  style  : "inherit"
		  });

		});	

	});
})(jQuery);