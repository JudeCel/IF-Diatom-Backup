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
				name: "required",
				
				number_of_brands: {
					max: number_of_brands
				}				
			},
			messages: {
				name: "Please enter your brand project name",
				number_of_brands:"<br>You cannot exceed the max no of brands"
			}
		});

		var start_date_input = $('#start_date'),
				end_date_input = $('#end_date');

		start_date_input.datepicker({
			showButtonPanel: true, 
			dateFormat: 'dd-mm-yy',
			minDate: start_date,
			maxDate: end_date
		});
		
		end_date_input.datepicker({
			showButtonPanel: true, 
			dateFormat: 'dd-mm-yy',
			minDate: start_date,
			maxDate: end_date
		});			
			
		//set date and lock in start date
		if(!start_date_input.val()){
			start_date_input.datepicker("setDate", start_date);			
		}

		//set date and lock in end date
		if(!end_date_input.val()){
			end_date_input.datepicker("setDate", end_date);
		}	
		
	});
})(jQuery);