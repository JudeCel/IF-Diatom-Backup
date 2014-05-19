$(document).ready(function(){
	/**
	* Custom Actions
	**/
	
	/* After grid is loaded start using the actions avaialble */
	setTimeout(function(){
		var view_comments = $('.view_comments');

		//ensure that the correct checkbox is checked when activating checkboxes
		$('table#users label, table#users .checkboxes_inner').click(function(event){
			event.stopPropagation(); //stop previous code from activating
		});

		//View Comments	
		if(view_comments.length){
			$('.view_comments').fancybox({	
				'transitionIn' : 'elastic',
				
				'type': 'iframe',
				'onClosed': function() {
					
				}		
			});
		}			
	}, 500);
});