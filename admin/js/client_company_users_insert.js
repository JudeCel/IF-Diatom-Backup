(function($){
	$(document).ready(function(){
		var start_date = $('#start_date');

		/* Set imported cookie values */
		if(start_date.length){
			var name = $('input#name'),
					end_date = $('input#end_date'),
					session_details = $.cookie('session_details');

			//parse JSON
			session_details = $.parseJSON(session_details);

			if(session_details && !$.isEmptyObject(session_details)){
				if(session_details.hasOwnProperty("session_name")){
					name.val(session_details.session_name);
				}

				start_date.datetimepicker({
					showButtonPanel: true,
					dateFormat: 'dd-mm-yy',
					timeFormat: 'hh:mm TT'
				});

				end_date.datetimepicker({
					showButtonPanel: true,
					dateFormat: 'dd-mm-yy',
					timeFormat: 'hh:mm TT'
				});

				start_date.datetimepicker("setDate", session_details.start_date);
				end_date.datetimepicker("setDate", session_details.end_date);

				//remove session_details
				$.cookie('session_details', null);
				session_details = null;
			}
		}

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
		$("#signup_form").validate({
			rules: {
                email: {
                    required: true,
                    email: true
                },
				name_first: "required",
				name_last: "required",
				job_title: "required",
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
                }
			},
			messages: {
				job_title: "Please enter your Job Title",
				email: "Please enter a valid email address",
				firstname: "Enter a name",
				lastname: "Enter surname"
			}
		});
	});

})(jQuery);


