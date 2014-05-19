(function($){
	$.validator.setDefaults({	
		highlight: function(input) {
			$(input).addClass("ui-state-highlight");
		},
		unhighlight: function(input) {
			$(input).removeClass("ui-state-highlight");
		}
	});

	$(document).ready(function() {		
		// validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				username: "required",
				password: "required"				
			},
			messages: {
				username: "* Please enter a username",
				password: "* Please enter a password"				
			}
		});
	});
})(jQuery);