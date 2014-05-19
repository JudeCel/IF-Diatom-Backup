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

		//radio button on change function
	$("input[name='active_topic']:radio").change(function() {
		//now make the ajax call to save active topic
		//get the checked value
		var active_topic_id=$("input[name='active_topic']:checked").val();
		
		$.ajax({
			  type: 'POST',
			  url: "save-topic.php",
			  data:  {active_topic_id: active_topic_id, session_id: session_id},
			  
			  success: "Successfully saved"
			 
			});
    });
	
	 	if(topics_json.length){
		 	$.each(topics_json, function(){
				var self = this,
						id = self['id'];

				//save the open and close topic value against every topic
				$("#topic_status_id" + id).click(function() {
					var checked = $(this).is(':checked');
					
					$.ajax({
					  type: 'POST',
					  url: "save-topic.php",
					  data:  {topic_status_id: checked, topic_id: id},
					  
					  success: "Successfully saved"				 
					});
				});
			});
		}

	  $("#logoutlink").click(function(e){         
	  	$('#logout').dialog('open');     
	  });

	  /* reorder topics*/
	 	$("a.reorderTopics").fancybox({
			'titleFormat': function() { 
			 	return $('td a.reorderTopics').attr('alt'); 
			}, 
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onClosed': function() {
				parent.location.reload(true);
			 }				
		});

		/* edit a session staff*/
	 	$("#add_item a").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onClosed': function() {
				parent.location.reload(true);
			}
		});
	});
})(jQuery);