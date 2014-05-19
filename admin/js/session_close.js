(function($){
	$(document).ready(function(){
		//add the log out dialog code
		$("#logout").dialog({         
		  title:"Log off",   
			height: 'auto',
			width: 'auto',
			modal: true,
			autoOpen: false,     
			buttons: 
			{
				Yes: function() 
				{
					$(this).dialog('close');
					//alert('You clicked continue');
				   	document.getElementById('logoutlink').href="logout.php";

	       			   window.location.href = "logout.php?doLogout=true";

					return true;
				}, // end continue button
				Cancel: function() 

				{
					$(this).dialog('close');
					return false;
				} //end cancel button
			}//end buttons	
		
		});

	  $("#logoutlink").click(function (e) {         
	  	$('#logout').dialog('open');     
	  }); 
	
	 	///// fancy box for adding user
		$('.addUser').fancybox({		
			'transitionIn' : 'elastic',			
			'type': 'iframe',
			'onClosed': function() {
				parent.location.reload(true);
			}			
		});
    
		/* tag a session participant*/
	 	$("td a.tagSession").fancybox({
		 	'titleFormat': function() { 
			  return $('td a.tagSession').attr('alt'); 
			}, 
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onClosed': function() {
				parent.location.reload(true);
			}				
		});

		if(participants_json.length){

			$.each(participants_json, function(key, id){
				var rating = $("#pRating" + id),
						invite_again = $("#invite_again" + id),
						comments = $("#comments" + id);

				if(rating.length){
					rating.stars(
						{ 
							inputType: "select"
						},
						{
							callback: function(ui, type, value){
								$.ajax({
								  type: 'POST',
								  url: "save-rating.php",
								  data:  {participant_rating_id: value, participant_lists_id: id},
								  
								  success: "Successfully saved"
								 
								});
							}
						}				
					);
				}
			
				if(invite_again.length){
					invite_again.click(function(){
						var checked = $(this).is(':checked');
							
						$.ajax({
						  type: 'POST',
						  url: "save-rating.php",
						  data:  {invite_again: checked, participant_lists_id: id},
						  
						  success: "Successfully saved"					 
						});
					});
				}
			
				if(comments.length){
					//this the code that will enabled the comments to become editable on hover, click etc
					comments.editable("save-comments.php?participant_lists_id=" + id, { 
						  name: "comments",
						 //this adds buttons
						  //submit    : 'OK',
						 //cancel    : 'Cancel',
						 
						 //type      : 'textarea',
						  indicator : "Saving...",
						  tooltip   : "Click to edit...",
						  event     : "click",
						  style  : "inherit"
				  });
				}
			});
		}
	});
})(jQuery);