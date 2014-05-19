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

		// validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				name: "required"				
			},
			messages: {
				name: "Please enter your brand project name"
			}
		});

		var start_date_input = $('#start_date'),
				end_date_input = $('#end_date');

		//Set up the datepicker
		start_date_input.datepicker({
			showButtonPanel: true, 
			dateFormat: 'dd-mm-yy',
			minDate: min_date,
			maxDate: max_date
		});
		
		end_date_input.datepicker({
			showButtonPanel: true, 
			dateFormat: 'dd-mm-yy',
			minDate: min_date,
			maxDate: max_date
		});

		if(start_date_input.val() == start_date){
			//Set date for datepicker	
			start_date_input.datepicker("setDate", start_date);
		}

		if(end_date_input.val() == end_date){
			end_date_input.datepicker("setDate", end_date);
		}			
	});
})(jQuery);