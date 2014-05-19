(function($){
	var fields = null,
			collapsable = false,
			collapse_buttons = '<span class="ui-icon collapse"></span>'

	//collapse fieldsets
	function collapse_fieldsets(){
		/* If there are fieldsets and they should be collapsible */
		if(fieldsets.length){
			/* Loop through all the fieldsets */
			$.each(fieldsets, function(key){
				var fieldset = $(this),
						legend = fieldset.find('legend'),
						form_item = fieldset.find('.form_item'),
						icon = null;

				if(legend.length){
					if(collapsable){
						legend.append(collapse_buttons);

						icon = legend.find('.collapse');

						legend.css('cursor', 'pointer'); //make it clickable
						
						//hide/close form-items
						legend.on('click', open_close_fieldset);

						//if it is not the first one
						if(key && !fieldset.hasClass('open')){
							if(!form_item.hasClass('fill_fields')){
								form_item.hide(); //hide form items
							}

							if(icon){
								icon.addClass('close'); //set it as open
							}
						} else {
							if(icon){
								icon.addClass('open'); //set it as open
							}
						}
					} else {
						legend.open_close_fieldset_object();
					}
				}				
			});
		}
	}

	function open_close_fieldset(legend){
		var self = null;
		
		/* Check if the object being passed is a click */
		if(typeof legend.type !== 'undefined' && legend.type == 'click'){
			self = $(this);
			collapsable = true;
		} else {
			self = $(legend);
		}

		var icon = self.find('.collapse'), //set icon
				parent = self.parent(),
				form_item = parent.find('.form_item');

		if(form_item.length){
			if(form_item.is(':visible') && collapsable){
				form_item.slideUp(250);
			} else {
				form_item.slideDown(250);
			}
		}

		/* Switch classes to indicate open/close */
		if(icon.length){
			if(icon.hasClass('close')){
				icon.removeClass('close');
			} else if(collapsable) {
				icon.addClass('close');
			}
		}
	}

	//open and close fieldset
	$.fn.open_close_fieldset_object = function(){
		open_close_fieldset(this);
	}

	//Function for duplicating address
	function address_copy(){
		var self = $(this),
				copy_address = $("#copyaddress"),
				fields = copy_address.parent().parent().find('.fields'),
				billing_street = $("input#billing_street"),
				billing_suburb = $("input#billing_suburb"),
				billing_state = $("input#billing_state"),
				billing_post_code = $("input#billing_post_code");


		// If checked
		if(copy_address.is(":checked")) {
			if(self.attr('id') == copy_address.attr('id')){
				fields.slideUp(300);
			} else {
				fields.hide();
			}

			//for each input field
			billing_street.val($("input#trading_street").val()); 
			billing_suburb.val($("input#trading_suburb").val()); 
			billing_state.val($("input#trading_state").val()); 
			billing_post_code.val($("input#trading_post_code").val());			
			
			//won't work on drop downs, so get those values
			var trading_country_id = $("#trading_country_id").val();
			$("#billing_country_id").selectOptions(trading_country_id);					

		} else if(client_company_id && billing_address && $(billing_address).length){
			//for each input field
			// Clear on uncheck
			
			billing_street.val(billing_address['street']);
			billing_suburb.val(billing_address['suburb']);
			billing_state.val(billing_address['state']);
			billing_post_code.val(billing_address['post_code']);
			 
			$("#billing_country_id").selectOptions("");

			//Slide down if the object being referenced is the same as copyaddress
			if(self.attr('id') == copy_address.attr('id')){
				fields.slideDown(300);
			} else {
				fields.show();
			}
		} else {
			billing_street.val('');
			billing_suburb.val('');
			billing_state.val('');
			billing_post_code.val('');
			 
			$("#billing_country_id").selectOptions("");

			//Slide down if the object being referenced is the same as copyaddress
			if(self.attr('id') == copy_address.attr('id')){
				fields.slideDown(300);
			} else {
				fields.show();
			}
		}
	}

	//Function for duplicating contact
	function contact_copy(){
		var self = $(this),
				copy_contact = $('#copycontact'),
				fields = copy_contact.parent().parent().find('.fields'),
				billing_first_name = $("input#name_first_billing"),
				billing_last_name = $("input#name_last_billing"),
				billing_phone = $("input#phone_billing"),
				billing_mobile = $("input#mobile_billing"),
				billing_email = $("input#email_billing");

		// If checked
		if(copy_contact.is(":checked")) {
			if(self.attr('id') == copy_contact.attr('id')){
				fields.slideUp(300);
			} else {
				fields.hide();			
			}

			//for each input field
			billing_first_name.val($("input#name_first_primary").val()); 
			billing_last_name.val($("input#name_last_primary").val()); 
			billing_phone.val($("input#phone_primary").val()); 
			billing_mobile.val($("input#mobile_primary").val()); 
			billing_email.val($("input#email_primary").val());
		} else if(client_company_id && billing_contact && $(billing_contact).length) {			

			//Slide down if the object being referenced is the same as copyaddress
			if(self.attr('id') == copy_contact.attr('id')){
				fields.slideDown(300).queue(function(){
					//for each input field
					// Clear on uncheck			
					billing_first_name.val(billing_contact['name_first']); 
					billing_last_name.val(billing_contact['name_last']); 
					billing_phone.val(billing_contact['phone']); 
					billing_mobile.val(billing_contact['mobile']); 
					billing_email.val(billing_contact['email']);

					$(this).dequeue();
				});
			} else {
				fields.show();

				//for each input field
				// Clear on uncheck			
				billing_first_name.val(billing_contact['name_first']); 
				billing_last_name.val(billing_contact['name_last']); 
				billing_phone.val(billing_contact['phone']); 
				billing_mobile.val(billing_contact['mobile']); 
				billing_email.val(billing_contact['email']);
			}			
		} else {
			//Slide down if the object being referenced is the same as copyaddress
			if(self.attr('id') == copy_contact.attr('id')){
				fields.slideDown(300).queue(function(){
					//Clear Fields
					billing_first_name.val(''); 
					billing_last_name.val(''); 
					billing_phone.val(''); 
					billing_mobile.val(''); 
					billing_email.val('');

					$(this).dequeue();
				});
			} else {
				fields.show();

				//Clear Fields
				billing_first_name.val(''); 
				billing_last_name.val(''); 
				billing_phone.val(''); 
				billing_mobile.val(''); 
				billing_email.val('');
			}		
		}
	}

	$(document).ready(function(){
		var submit_btn = $('#signup_submit');

		fieldsets = $('fieldset');

		//If it is the signup form
		if(!client_company_id){
			collapsable =  true;
		}

		//If submit button is clicked after the form redirect does not occur
		if(submit_btn.length){
			submit_btn.on('click', function(){
				//wait to see if these fieldsets are collapsible
				setTimeout(function(){
					collapsable = false;

					collapse_fieldsets();
				}, 2000);
			});
		}

		if(collapsable){
			collapse_fieldsets();
		}

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
				  document.getElementById('logoutlink').href = "logout.php";
	       	window.location.href = "logout.php";

					return true;
				}, // end continue button
				Cancel: function() 
				{
					$(this).dialog('close');
					return false;
				} //end cancel button
			}//end buttons		
		});

	  $("#logoutlink").click(function(e) {         
	  	$('#logout').dialog('open');     
	  });

	  $.validator.setDefaults({
			////submitHandler: function() { alert("submitted!"); },
			highlight: function(input) {
				$(input).addClass("ui-state-highlight");
			},
			unhighlight: function(input) {
				$(input).removeClass("ui-state-highlight");
			}
		});

		$("#client_company_logo_Upload").fancybox({
			'titlePosition'		: 'inside',
			'height'			: 615,
			'autoScale'			: true,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'onClosed': function() {
				parent.location.reload(true);
			}
		});

		if(client_company_id){
      //auto complete for addresses
      //trading address first
       $("#trading_suburb").autocomplete({
          //send the key to get what you want
          //eg: Suburb, State, etc basically anything from post_code_suburb_lookup table
          //also we can get country from the country look up table
          source: "json-address-lookup.php?key=Suburb",
          minLength: 2              
      });
     
      $("#trading_state").autocomplete({
          //send the key to get what you want
          //eg: Suburb, State, etc basically anything from post_code_suburb_lookup table
          //also we can get country from the country look up table
          source: "json-address-lookup.php?key=State",
          minLength: 1              
      });

      //auto complete the billing address
        //trading address first
       $("#billing_suburb").autocomplete({
          //send the key to get what you want
          //eg: Suburb, State, etc basically anything from post_code_suburb_lookup table
          //also we can get country from the country look up table
          source: "json-address-lookup.php?key=Suburb",
          minLength: 2              
      });
      
      $("#billing_state").autocomplete({
          //send the key to get what you want
          //eg: Suburb, State, etc basically anything from post_code_suburb_lookup table
          //also we can get country from the country look up table
          source: "json-address-lookup.php?key=State",
          minLength: 1              
      });
    }

    // validate signup form on keyup and submit
		$("#signup_form").validate({
			rules: {
				name: "required",
				client_company_type: "required",
				moderated: {
					required: true
				},

        //address blocks
        //first trading address
        trading_street: {
					required: true
				},

        trading_suburb: {
					required: true
				},

	      trading_post_code: {
					required: true
				},
	                        
        //then the billing address
        billing_street: {
					required: true
				},

	      billing_suburb: {
					required: true
				},

	      billing_post_code: {
					required: true
				},

	      URL: {
					url:true
				},

	      //contact details of primary contact            
				email_primary: {
					required: true,
					email: true
				},
				
				name_first_primary:{
					required:true
				},

				name_last_primary:{
					required:true
				},
				
				//contact details of billing contact            
				email_billing: {
					required: true,
					email: true
				},
				
				name_first_billing:{
					required:true
				},

				name_last_billing:{
					required:true
				},
				
				start_date:{
					required:true
				},

				end_date:{
					required:true
				},

				number_of_brands:{
					required:true,
					 digits: true //ensures only digits are accepted
				},

				max_number_of_observers:{
					required:true,
					digits:true	
				},

				max_sessions_brand:{
					required:true,
					digits:true
				}				
			},

			messages: {
				companyname: "Please enter your Company Name",
				companytype: "Please enter your Company Type",
				timeframe:"Please enter a timeframe",
				email: "Please enter a valid email address",
				url: "Please enter a valid URL",
				firstname:"Enter a name",
				lastname:"Enter surname"
			}
		});
		

		$("#start_date").datepicker({showButtonPanel: true, dateFormat: 'dd-mm-yy'});
		$("#end_date").datepicker({showButtonPanel: true, dateFormat: 'dd-mm-yy'} );			


		var copy_address = $('#copyaddress'),
				copy_contact = $('#copycontact'),
				signup_submit = $('#signup_submit');
		
		//If the checkbox is already checked
		if(copy_address.is(':checked')){
			address_copy();
		}

		//If the checkbox is already checked
		if(copy_contact.is(':checked')){
			contact_copy();
		}

		//when the checkbox for address is checked or unchecked
		copy_address.click(address_copy);
		
		//when the checkbox for contact is checked or unchecked
		copy_contact.click(contact_copy);

		/* If the submit button is pressed */
		signup_submit.click(function(){
			//If the checkbox is already checked
			if(copy_address.is(':checked')){
				address_copy();
			}

			//If the checkbox is already checked
			if(copy_contact.is(':checked')){
				contact_copy();
			}
		});
	});
})(jQuery);