(function($){
	var dialog_confirm = null,
			ids = null;

	//Delete or export participants
	function delete_participants(self){
		var participant_table = $(this),
				action_url = '';		

		if (!ids){
			ids = participant_table.jqGrid('getGridParam','selarrrow');

			if(!ids.length){
				ids = get_selected_checkboxes();
			}		
		}
	
		if(ids.length>0) {	
			//var names = [];
			var participant_id=[];
			
			for (var i=0, il=ids.length; i < il; i++) {
				var part_id = ids[i];					

				participant_id.push(part_id);
			}
			
			//Set Dialog box message
			dialog_confirm.text('Are you sure want to delete the Participant(s):');				
			
			dialog_confirm.dialog({	
				height:150,
				modal:true,

				buttons:{

					'Cancel': function(){

						$(this).dialog('close');

					},

					'Confirm': function(){
						var unixtime = Math.round((new Date()).getTime() / 1000);

						action_url = "participant-delete.php?participant_id=" + participant_id + "&brand_project_id=" + brand_project_id + '&ajax=' + unixtime;

						window.location = action_url; //perform_action
						$(this).dialog('close');					
					}
				}	
			});
		} else {	
			var message = 'Please select a Participant to delete first.';

			alert(message);			
		}
	}

	function export_participants(){
		var participant_table = $(this),
				action_url = '';

		if (!ids){
			ids = participant_table.jqGrid('getGridParam','selarrrow');

			if(!ids.length){
				ids = get_selected_checkboxes();
			}		
		}

		if(ids.length>0) {	
			//var names = [];
			var participant_id=[];
			
			for (var i=0, il=ids.length; i < il; i++) {
				var part_id = ids[i];					

				participant_id.push(part_id);
			}
			
			//Set Dialog box message
			dialog_confirm.text('Are you sure want to export the Participant(s):');				
			
			dialog_confirm.dialog({	
				height:150,
				modal:true,

				buttons:{

					'Cancel': function(){

						$(this).dialog('close');

					},

					'Confirm': function(){
						action_url = "export-selected.php?participant_id=" + participant_id + "&brand_project_id=" + brand_project_id;

						window.location = action_url; //perform_action
						$(this).dialog('close');					
					}
				}	
			});
		} else {	
			var message = 'Please select a Participant to export first.';

			alert(message);			
		}
	}

	/**
	* Get selected participants, if jqgrid's method fails
	**/
	function get_selected_participants(self){
		var checked_rows = $('#participantPanel .cbox');

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
		}
	}

	function get_selected_checkboxes(){
		var ids = new Array(),
				checked_rows = $('#participantPanel .cbox');

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

	$(document).ready(function(){
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

		//Place grid on page
		var content_items = $('#content > .inner .items'), //content div
				participant_table = $('<table id="participantPanel" />'),
				participant_div = $('<div id="participantPanelPage" />');

		content_items.text('');

		//Append participant panel content
		content_items.append(participant_table).append(participant_div);

		participant_table.jqGrid({
			url:'jqgrid-json-client_participant.php?brand_project_id=' + brand_project_id,
			datatype: "json",
			autoheight: true,
			//autowidth: true,
			width:1000,
			
			//editurl is the url where the data will be posted
			editurl:"participantPanel-delete.php",				
			
			colNames:[
				'Name', 
				'Gender', 
				'Email',
				'Mobile',
				'Phone',
				'Fax',
				'Street', 
				'State', 
				'Suburb',
				'Postcode',
				'Age Value',
				'Ethnicity', 
				'Occupation',
				'Brand Segment', 
				'Country', 
				'#Invites',
				'Accept',
				'NotNow',
				'NotInt',
				'NoReply',
				'LastSess',
				'Interest',
				'Rating',
				'InvAgain',
				'Comment',								
				'Optional 1',
				'Optional 2',
				'Optional 3',
				'Optional 4',
				'Optional 5',
				'Edit',
			],
			colModel:[
			 {name:'name',index:'name', editable: false},
			 {name:'gender',index:'gender', editable: false, hidden: true},
			 {name:'email',index:'email', editable: false, hidden: true},
			 {name:'mobile',index:'mobile', editable: false, hidden: true},
			 {name:'phone',index:'phone', editable: false, hidden: true},
			 {name:'fax',index:'fax', editable: false, hidden: true},
			 {name:'street',index:'street', hidden: true},
			 {name:'state',index:'state', hidden: true},
			 {name:'suburb',index:'suburb', hidden: true},
			 {name:'post_code',index:'post_code', hidden: true},
			 {name:'age_value',index:'age_value', hidden: true},
			 {name:'ethnicity',index:'ethinicity', hidden: true},
			 {name:'occupation',index:'occupation', hidden: true},
			 {name:'brand_segment',index:'brand_segment', hidden: true},
			 {name:'country_name',index:'country_name', hidden: true},
			 {name: 'number_of_invites', index: 'number_of_invites', editable: false, sortable: true, hidden: false},
			 {name: 'invites_accepted', index: 'invites_accepted', editable: false, sortable: true, hidden: false},
			 {name: 'invites_not_now', index: 'invites_not_now', editable: false, sortable: true, hidden: false},
			 {name: 'invites_not_interested', index: 'invites_not_interested', editable: false, sortable: true, hidden: false},
			 {name: 'invites_no_reply', index: 'invites_no_reply', editable: false, sortable: true, hidden: false},
			 {name: 'last_invited_session_name', index: 'last_invited_session_name', editable: false, sortable: true, hidden: false},
			 {name: 'interested', index: 'interested', editable: false, sortable: true, hidden: false},				 
			 {name: 'rating15', index: 'rating15', editable: false, sortable: true, hidden: false},			 
			 {name: 'invite_again', index: 'invite_again', editable: false, sortable: true, hidden: false},
			 {name: 'comments', index: 'comments', editable: false, sortable: true, hidden: false},			 
			 {name:'optional1', index:'optional1', hidden: true},
			 {name:'optional2', index:'optional2', hidden: true},
			 {name:'optional3', index:'optional3', hidden: true},
			 {name:'optional4', index:'optional4', hidden: true},
			 {name:'optional5', index:'optional5', hidden: true},
			 {name:'more',index:'more', search: false, hidden: false},
			],
			rowNum:10,
			rowTotal: 100000,
			rowList : [50,100,200],
			loadonce: false,
			mtype: "GET",
			rownumbers: true,
			rownumWidth: 40,
			gridview: true,
			pager: '#participantPanelPage',
			sortname: 'pid',
			viewrecords: true,
			sortorder: "asc",
	    ignoreCase:true,
			multiselect: (user_type == 3 ? false : true)
		});
		
		var prmSearch = {multipleSearch:true,overlay:false};

		participant_table.jqGrid(
			'navGrid',
			'#participantPanelPage', 
			{
				edit: false, 
				view:false, 
				add: false, 
				del: false,
				search:false,
				refresh:true, 
				searchtitle: "Find Records",  
				reloadAfterSubmit:true
			},
      {},
      {},
      {},
      prmSearch
    );
		
		participant_table.jqGrid(
			'navButtonAdd', 
			'#participantPanelPage', 
			{ 
				caption: "", 
				buttonicon: "ui-icon-calculator", 
				title: "Choose Columns",
        onClickButton: function() {
					participant_table.jqGrid('columnChooser');
				}
			}
		);
		
		participant_table.jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false,  defaultSearch : "cn"});		
		
		//Add create button
		if(user_type < 2){
			// Add the delete participant option here		
			participant_table.navGrid('#participantPanelPage',{edit:true,add:true,del:false,search:false})
			.navButtonAdd('#participantPanelPage',{
			   caption:"",
			   title: "Delete",
			   buttonicon:"ui-icon-delete",
			   onClickButton: delete_participants,
			   position:"last"
			});

			participant_table.navGrid('#participantPanelPage',{edit:true,add:true,del:false,search:false})
			.navButtonAdd('#participantPanelPage',{
			   caption:"",
			   title: "Export",
			   buttonicon:"ui-icon-export",
			   onClickButton: export_participants,
			   position:"last"
			});
		}

		dialog_confirm = $('<div id="dialog-confirm" />');
		dialog_confirm.text('Are you sure want to delete the selected Participant(s)');

	 	$("#add_item div.other_downloads a#dd").fancybox({
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'width' : 800,
			'height' : 700,
			'autoScale' : 'false',
			'onClosed': function() {
				parent.location.reload(true);
			}			
		});

	 	if(user_type < 2){
			//Wait for HTML to build
			$(document).ajaxSuccess(function(){
				/* Set select all title */
				var select_all = $('#cb_participantPanel');
				if(select_all.length){
					select_all.attr('title', 'Select All');
				}
				
				setTimeout(function(){
					if(select_all.length){
						//Set fancyboxes						
						if(!$("td a").data().fancybox){
							//Set that JS is available
							$("td a").each(function(){
								var self = $(this),
									url = self.attr('href');

								self.attr('href', url + '&js=1');
							});

							$("td a").show().fancybox({
								'transitionIn'		: 'none',
								'transitionOut'		: 'none',
								'type'				: 'iframe',
								'height' : 700,
								'autoScale' : 'false',
								'onClosed': function() {
									parent.location.reload(true);
								}			
							});
						}
					}
					
					//Set fancyboxes	
					if(!$("#add_item > a").not('.export').data().fancybox){
						$("#add_item > a").not('.export').fancybox({
							'transitionIn'		: 'none',
							'transitionOut'		: 'none',
							'type'				: 'iframe',
							'height' : 700,
							'autoScale' : 'false',
							'onClosed': function() {
								parent.location.reload(true);
							}			
						});
					}
					
					//Select participants
					var checked_rows = $('#participantPanel .cbox');
					$('#participantPanel .cbox').off('click', click_on_checkbox);
					$('#participantPanel .cbox').on('click', click_on_checkbox);
					
					function click_on_checkbox(event){
						var self = $(this);

						checked_rows = $('#participantPanel .cbox');

						if(self.is(':checked')){
							get_selected_participants(event.target);
						} else {
							self.removeAttr('checked'); //remove own checked value
							
							if(self.filter('#cb_participantPanel').length){ //remove all checked values
								checked_rows.removeAttr('checked');
							} else {
								var select_all = checked_rows.filter('#cb_participantPanel');
								
								//If select all is still selected
								if(select_all.length){
									select_all.removeAttr('checked');
								}
							}															
						}
						
						ids = get_selected_checkboxes();
					}
				}, 400);
							
			});
		}	
	});
})(jQuery);