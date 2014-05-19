(function($){
	$(document).ready(function(){
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

		$("#signupForm").validate({
			rules: {
				topic_name: "required"
			},
			messages: {
				name: "Please enter a topic name"
				
			}
		});
	
		$("#signupForm input:not(:submit)").addClass("ui-widget-content");

		/* edit a brand project*/
		$("td a.editEmail1").fancybox({
			'titleFormat': function() { 
			 	return $('td a.editEmail1').attr('alt'); 
			 }, 
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'width'             : 1000, 
			'height'            : '100%', 
			'autoScale':true,
			'type'				: 'iframe',
		 	'onClosed': function() {
				parent.location.reload(true);
		  }		
		});
	});
})(jQuery);