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
			
		// validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				name_last: "required",
                name_first: "required",
				gender: "required",
				email: {
					required: true,
					email: true
				},
                mobile:
                {
                    required:
                    {
                        depends:function(element)
                        {
                            return $('#uses_landline').val() == (element.id=='phone');
                        }
                    },
                    minlength:9,
                    maxlength:12
                },
                phone:
                {
                    required:
                    {
                        depends:function(element)
                        {
                            return $('#uses_landline').val() == (element.id=='phone');
                        }
                    },
                    minlength:9,
                    maxlength:12
                },
                fax:
                {
                    minlength:9,
                    maxlength:12
                }

			},
			messages: {
				email: "Please enter a valid email address"
			}
		});

		//$("#dob").datepicker({ dateFormat: 'dd-mm-yy' , showButtonPanel: true } );
	});
})(jQuery);