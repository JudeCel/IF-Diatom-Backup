(function($){
	$(document).ready(function(){
		$.validator.setDefaults({
			//submitHandler: function() { alert("submitted!"); },
			highlight: function(input) {
				$(input).addClass("ui-state-highlight");
			},
			unhighlight: function(input) {
				$(input).removeClass("ui-state-highlight");
			}
		});

		$("#logout").dialog({         
	    title:"Log off",   
			height: 'auto',
			width: 'auto',
			modal: true,
			autoOpen: false,     
			buttons: {
				Yes: function() 
				{
					$(this).dialog('close');
			   	document.getElementById('logoutlink').href = "logout.php";
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

	  // validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				name_first: "required",
				name_last: "required",
				job_title: "required",
				email: {
					required: true,
					email: true
				},				
			},
			messages: {
				job_title: "Please enter your Job Title",
				email: "Please enter a valid email address",
				firstname:"Enter a name",
				lastname:"Enter surname",
			}
		});
	});
})