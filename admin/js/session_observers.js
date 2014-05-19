(function($){
	/**
	* Get selected observers, if jqgrid's method fails
	**/
	function get_selected_observers(){
		var ids = new Array(),
				checked_rows = $('#observerPanel .cbox');

		/* Go through the checked rows and check which rows are really checked */
		if(checked_rows.length){
			$.each(checked_rows, function(){
				var checkbox = $(this);

				if(checkbox.is(':checked')){
					var key_item = checkbox.parent().parent().attr('id');

					/* Add checked row to id */
					if(typeof key_item !== 'undefined' && !isNaN(key_item)){
						ids.push(parseInt(key_item));
					}
				}
			});
		}

		return ids;
	}
	
	function get_selected_observers_from_click(self){
		var ids = new Array();

		/* Go through the checked rows and check which rows are really checked */
		if(checked_rows.length){
			var checkbox_title = $(self).attr('title');

			var visible_checkboxes = checked_rows.filter(function(){ //find checkboxes taht are checked
				if(!checkbox_title || checkbox_title != 'Select All'){
					return($(this).is(':checked'));
				} else {
					return false;
				}
			});

			if(!visible_checkboxes.length && checkbox_title == 'Select All'){
				visible_checkboxes = checked_rows;
			}

			//Ensure that everything is checked
			visible_checkboxes.attr('checked', 'checked');

			//Check if checkboxes are checked so that the id can be stored
			$.each(checked_rows, function(){
				var checkbox = $(this);

				if(checkbox.is(':checked')){
					var key_item = checkbox.parent().parent().attr('id');

					/* Add checked row to id */
					if(typeof key_item !== 'undefined' && !isNaN(key_item)){
						ids.push(parseInt(key_item));
					}
				}
			});
		}

		return ids;
	}

	$(document).ready(function(){
		var checked_rows = new Array();
		
		$(document).ajaxSuccess(function(){
			checked_rows = $('#observerPanel .cbox');
		
			if(checked_rows.length){				
				$('#observerPanel .cbox').off('click');
				$('#observerPanel .cbox').on('click', function(event){
					var self = $(this);
		
					checked_rows = $('#observerPanel .cbox');
		
					if(self.is(':checked')){
						get_selected_observers_from_click(event.target);
					} else {
						checked_rows.removeAttr('checked');
					}
				});
			}
		});
		
		$("#getSelected").click(function(){	
			var ids = get_selected_observers(),
					self = $(this),
					self_id = self.attr('id');

			if(ids.length){		
				var observer_id=[];
			
				for (var i=0, il=ids.length; i < il; i++) {
					var obs_id = ids[i];					

					observer_id.push(obs_id);
				}
			
				dialog_name = $('<div id="dialog-confirm" />');
				dialog_name.text('Are you sure want to sent a Ticket to the selected people');
			
				dialog_name.dialog({
					height:280,
					modal:true,	
					buttons:{	
						'Cancel': function(){

							$(this).dialog('close');

						},

						'Confirm': function(){	
							window.location = "session-edit-insert.php?return=1&session_id=" + session_id + "&user_id=" + observer_id;

							$(this).dialog('close');							
						}	
					}
				});	
			}

			return false;
		});

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

	  $("#logoutlink").click(function(e){         
	  	$('#logout').dialog('open');     
	  });	
	
	  //multi select checks
		var warning = $(".message");	
			
		 ///// fancy box for adding user
		$('#add_item a, #edit_observer').fancybox({		
			'transitionIn' : 'elastic',			
			'type': 'iframe',
			'height' : 400,
			'onClosed': function() {
				parent.location.reload(true);
			}			
		}); 
	});
})(jQuery);