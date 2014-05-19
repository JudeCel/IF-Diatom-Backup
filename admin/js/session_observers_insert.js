(function($){
	$(document).ready(function(){
		
		$("select").multiselect({ 
		   click: function(e){
			   if( $(this).multiselect("widget").find("input:checked").length > max_number_of_observers ){
				   warning.addClass("error").removeClass("success").html("You can only check "+ ax_number_of_observers +" checkboxes!");
				   return false;
			   } else {
				   warning.addClass("success").removeClass("error").html(""+ max_number_of_observers +" Observers allowed");
			   }
		   }
		}).multiselectfilter();
	});
})(jQuery);