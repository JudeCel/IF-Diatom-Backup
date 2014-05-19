(function($){
	function save_session_values(){
		var self = $(this),
				session_details = new Object();

		/* Set session details */
		session_details.session_name = $('input#name').val();
		session_details.start_date = $('input#start_date').val();
		session_details.end_date = $('input#end_date').val();

		//Set to JSON
		session_details = JSON.stringify(session_details);

		//Save cookie
		$.cookie('session_details', session_details);
	}

	$(document).ready(function(){
		var start_date_input = $('#start_date'),
				end_date_input = $('#end_date'),
				new_facil_link = $('footer a.buttons'),
				session_details = $.cookie('session_details'),
				client_insert = $('fieldset.client_insert'),
				submit_btn = $('#btnSubmit');

		/* If not imported */
		if(!client_insert.length){
			start_date_input.datetimepicker({
				showButtonPanel: true, 
				dateFormat: 'dd-mm-yy',
				timeFormat: 'hh:mm TT'
			});
			
			end_date_input.datetimepicker({
				showButtonPanel: true, 
				dateFormat: 'dd-mm-yy',
				timeFormat: 'hh:mm TT'
			});		

			start_date_input.datetimepicker("setDate", min_date);
			end_date_input.datetimepicker("setDate", max_date);

			//new_facil_link.on('click', save_session_values);
		}

		//Min date for datepicker
		start_date_input.datetimepicker("change",{minDate: min_date, maxDate: max_date});
		end_date_input.datetimepicker("change",{minDate: min_date, maxDate: max_date});

		//If add button is found
		// if(submit_btn.length){
		// 	submit_btn.on('click', function(e){
		// 		var start_midnight = start_date_input.val().match(/00:00/gi),
		// 				end_midnight = end_date_input.val().match(/00:00/gi),
		// 				session_name = $('#name'),
		// 				date_message = $('<label class="error status date">Are you sure you want to use midnight as a time?</label>');

		// 		date_message.hide();

		// 		if(start_midnight || end_midnight){
		// 			//If midnight was selected
		// 			if(start_midnight){
		// 				start_date_input.after(date_message);
		// 				date_message = date_message.clone();
		// 			}

		// 			//If midnight was selected
		// 			if(end_midnight){
		// 				end_date_input.after(date_message);
		// 			}

		// 			$('label.status.date').slideDown(100);

		// 			submit_btn.off('click');
					
		// 			e.preventDefault();
		// 		}				
		// 	});
		// }

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
		// validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				name: "required",
				start_time: "required",
				end_time: "required",
				max_session: {
					max: number_of_sessions
				},
				moderator_user_id: "required"				
			},
			messages: {
				name: "Please enter your session name",
				max_session:"You cannot exceed the max no of sessions"				
			}
		});
	});
})(jQuery);