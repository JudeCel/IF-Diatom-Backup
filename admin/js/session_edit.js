(function($){
	$(document).ready(function(){
		var start_date_input = $('#start_time'),
				end_date_input = $('#end_time');

		//Set up the datepicker
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
			
		//Set date for datepicker	
		start_date_input.datetimepicker("setDate", start_date);
		end_date_input.datetimepicker("setDate", end_date);

		//Min date for datepicker
		start_date_input.datetimepicker("change",{minDate: min_date, maxDate: max_date});
		end_date_input.datetimepicker("change",{minDate: min_date, maxDate: max_date});
	});
})(jQuery);