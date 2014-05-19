(function($){
	var dialog_confirm = null,
			checked_rows = new Array(),
			ids = null;

	//Delete or export participants
	function delete_observers(){
		var observer_table = $(this),
				action_url = '';
		
		if (!ids){
			ids = observer_table.jqGrid('getGridParam','selarrrow');

			if(!ids.length){
				ids = get_selected_checkboxes();
			}		
		}
	
		if(ids.length) {	
			//var names = [];
			var observer_id=[];
			
			for (var i=0, il=ids.length; i < il; i++) {
				var obs_id = ids[i];					

				observer_id.push(obs_id);
			}
			
			//Set Dialog box message
			dialog_confirm.text('Are you sure want to delete the Potential Observer(s)');				
			
			dialog_confirm.dialog({	
				height:150,
				modal:true,

				buttons:{

					'Cancel': function(){

						$(this).dialog('close');

					},

					'Confirm': function(){
						var unixtime = Math.round((new Date()).getTime() / 1000);

						action_url = "StaffDelete.php?user_id=" + observer_id + '&ajax=' + unixtime + '&brand_project_id=' + brand_project_id;

						window.location = action_url; //perform_action
						$(this).dialog('close');					
					}
				}	
			});
		} else {	
			var message = 'Please select a observer to delete first';

			alert(message);			
		}
	}
	
	/**
	* Get selected participants, if jqgrid's method fails
	**/
	function get_selected_checkboxes(){
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
	
	/**
	* Make sure all the correct checkboxes are selected
	**/
	function get_selected_observers(self){
		var checked_rows = $('#observerPanel .cbox');

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
				observer_table = $('<table id="observerPanel" />'),
				observer_div = $('<div id="observerPanelPage" />');

		content_items.text('');

		//Append participant panel content
		content_items.append(observer_table).append(observer_div);

		/* If session is not set */
		if(typeof session_id === 'undefined'){
			session_id = false;
		}

		observer_table.jqGrid({
			url:'jqgrid-json-client_observer.php?brand_project_id=' + brand_project_id + (session_id ? '&session_id=' + session_id : ''),
			datatype: "json",
			autoheight: true,
			//autowidth: true,
			width:1000,
			
			colNames:[
				'Name',
				'Job Title', 
				'Email',
				'Mobile',				 
				'Edit'
			],
			colModel:[
			 {name:'name',index:'name', editable: false},
			 {name:'job_title',index: 'job_title', hidden: false},
			 {name:'email',index:'email', editable: false, hidden: false},
			 {name:'mobile',index: 'mobile', hidden: false},			 
			 {name:'edit',index:'edit', search: false, hidden: false}
			],
			rowNum:10,
			rowTotal: 100000,
			rowList : [50,100,200],
			loadonce: false,
			mtype: "GET",
			rownumbers: true,
			rownumWidth: 40,
			gridview: true,
			pager: '#observerPanelPage',
			sortname: 'id',
			viewrecords: true,
			sortorder: "asc",
	    ignoreCase:true,
			multiselect: (user_type == 3 && !session_id ? false : true)
		});
		
		var prmSearch = {multipleSearch:true,overlay:false};

		observer_table.jqGrid(
			'navGrid',
			'#observerPanelPage', 
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
		
		observer_table.jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false,  defaultSearch : "cn"});

		//Add create button
		if(user_type < 2 && !$('div.sessions').length){ //should not be available on the sessions page
			// Add the delete participant option here		
			observer_table.navGrid('#observerPanelPage',{edit:true,add:true,del:false,search:false})
			.navButtonAdd('#observerPanelPage',{
			   caption:"",
			   title: "Delete",
			   buttonicon:"ui-icon-delete",
			   onClickButton: delete_observers,
			   position:"last"
			});
		}	

		dialog_confirm = $('<div id="dialog-confirm" />');
		dialog_confirm.text('Are you sure want to delete the selected Observer(s)');

	 	if(user_type < 2){
		 	$("#add_item a").fancybox({
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe',
				'autoScale' : 'true',
				'onClosed': function() {
					parent.location.reload(true);
				}			
			});

			setTimeout(function(){
				/* Set select all title */
				var select_all = $('#cb_observerPanel');
				if(select_all.length){
					select_all.attr('title', 'Select All');
				}

				$("td a").fancybox({
					'transitionIn'		: 'none',
					'transitionOut'		: 'none',
					'type'				: 'iframe',
					'autoScale' : 'false',
					'height' : 600,
					'onClosed': function() {
						parent.location.reload(true);
					}			
				});
			}, 400);
		}
		
		$(document).ajaxSuccess(function(){
			setTimeout(checkbox_click_setup, 400);
		});		
		
		function checkbox_click_setup(){			
			$('#observerPanel .cbox').off('click');
			$('#observerPanel .cbox').on('click', checkbox_click);
		}
		
		function checkbox_click(event){
			var self = $(this);

			checked_rows = $('#observerPanel .cbox');

			if(self.is(':checked')){
				get_selected_observers(event.target);
			} else {
				self.removeAttr('checked'); //remove own checked value
							
				if(self.filter('#cb_observerPanel').length){ //remove all checked values
					checked_rows.removeAttr('checked');
				} else {
					var select_all = checked_rows.filter('#cb_observerPanel');
					
					//If select all is still selected
					if(select_all.length){
						select_all.removeAttr('checked');
					}
				}	
			}						

			ids = get_selected_checkboxes();
		}
	});
})(jQuery);