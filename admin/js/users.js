(function($){
	var checked_rows = new Array();

	/**
	* Get selected administrators, if jqgrid's method fails
	**/
	function get_selected_administators(self){
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

	/**
	* Reset password of staff
	**/
	function reset_selected(){	
		var users_table = $(this),
				ids = [],
				dialog_box = $('<div id="dialog-confirm" />'),
				table = $('table#users'),
				message = 'Are you sure want to reset this user/s password? A mail will be sent to the user with the new login details.';

		if(!ids.length){
			var cuids = get_selected_participants();

			$.each(cuids, function(key, cuid){
				var parsed_cuid = parseInt(cuid),
						staff_row = table.find('tr#' + parsed_cuid),
						user_login_id = users_table.jqGrid('getCell', parsed_cuid, 'user_login_id'); //get user id

				/* If the user id can't be found */
				if(!user_login_id){
					var ulid_cell = staff_row.find('td').filter(function(){
						return ($(this).attr('aria-describedby') == 'users_user_login_id');
					});

					user_login_id = parseInt(ulid_cell.attr('title'));
				}

				ids.push(user_login_id);
			});
		}

		dialog_box.text(message);

		if (ids.length>0) {						
			/* Confirm */
			dialog_box.dialog({	
				height:280,	
				modal:true,	
				buttons:{	
					'Cancel': function(){	
						$(this).dialog('close');	
					},

					'Confirm': function(){	
						window.location = "reset_password.php?user_login_id_array="+ids;
						$(this).dialog('close');
					}	
				}	
			});	
		}
	}

	function activate_deactivate_staff(){
		var users_table = $(this),
				selected_staff = [], //find the selected staff
				table = $('table#users'),
				role_data = new Object(),
				message = null,
				dialog_box = $('<div id="dialog-confirm" />');

		message = 'This will activate or deactivate the roles of the staff according to which checkboxes are either checked or not. Are you sure you want to continue?';

		dialog_box.text(message); //set message

		if(!selected_staff.length){
			selected_staff = get_selected_participants();
		}

		//Find the role checkboxes for the staff to see which roles to activate and deactivate
		if(selected_staff.length){
			$.each(selected_staff, function(key, cuid){
				var parsed_cuid = parseInt(cuid),
						staff_row = table.find('tr#' + parsed_cuid),
						user_id = users_table.jqGrid('getCell', parsed_cuid, 'user_id'); //get user id

				/* If the user id can't be found */
				if(!user_id){
					var user_id_cell = staff_row.find('td').filter(function(){
						return ($(this).attr('aria-describedby') == 'users_user_id');
					});

					user_id = parseInt(user_id_cell.attr('title'));
				}

				//Check if the staff member can be found
				if(staff_row.length){
					var role_checkboxes = staff_row.find('div.checkboxes input');

					//Go through the checkboxes and add to array
					$.each(role_checkboxes, function(checkbox_key, checkbox){
						var self = $(checkbox),
								checked_status = self.val(),
								staff_info = new Object();

						//Set user id and active status
						if(!self.is(':checked')){
							checked_status = 0;
						}
						staff_info.active_status = checked_status;					

						staff_info.type_id = self.attr('data-typeid');

						//If it is a session staff role
						if(self.attr('data-sid')){
							staff_info.sid = self.attr('data-sid');
						}

						if(!role_data[user_id]){
							role_data[user_id] = new Array();
						}

						role_data[user_id].push(staff_info);
					});
				}
			});		

			setTimeout(function(){
				role_data_json = JSON.stringify(role_data);
	
				if(!$.isEmptyObject(role_data)){
					var url = 'deactivate_client_session_staff.php?json=' + role_data_json;

					//Use dialog confirm to process event
					dialog_box.dialog({
						height: 280,
						modal: true,
						buttons:{	
							'Cancel': function(){	
								$(this).dialog('close');	
							},

							'Confirm': function(){						
								window.location = url;
								$(this).dialog('close');
							}	
						}
					});
				}
			}, 500);
		}		
	}

	/**
	* Get selected participants, if jqgrid's method fails
	**/
	function get_selected_participants(){
		var ids = new Array(),
				checked_rows = $('#users .cbox');

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

	$(document).ready(function() {
		/**
		* Page Default actions
		**/

		//add the log out dialog code
		$("#logout").dialog({         
	    title:"Log off",   
			height: 'auto',
			width: 'auto',
			modal: true,
			autoOpen: false,     
			buttons:{
				
				Yes: function(){
					$(this).dialog('close');
				  document.getElementById('logoutlink').href="logout.php";
	       	window.location.href = "logout.php?doLogout=true";

					return true;
				}, // end continue button

				Cancel: function(){
					$(this).dialog('close');
					return false;
				} //end cancel button

			}//end buttons	
		});

	  $("#logoutlink").click(function (e) {         
	  	$('#logout').dialog('open');     
	  });
	
		var users_table = $('<table id="users" />'),
				users_div = $('<div id="usersPage" />'),
				content = $('#content > .inner');

		//add grid html
		content.append(users_table).append(users_div);

		//for inline edits
		function myelem (value, options) {
		  var el = document.createElement("input");
		  el.type="text";
		  el.value = value;
		  return el;
		}
	 
		function myvalue(elem, operation, value) {
			if(operation === 'get') {
			   return $(elem).val();
			} else if(operation === 'set') {
			   $('input',elem).val(value);
			}
		}

		var lastsel;		
		
		users_table.jqGrid({
			url:'jqgrid-json-users.php?q=1',
			datatype: "json",
			autoheight: true,
			autowidth: true,
							
			colNames:['ID', 'user_id', 'user_login_id', 'Username', 'Name', 'Global Admin', 'Session Facilitator', 'Edit', 'Delete'],
			colModel:[
				{ name: 'ID', index:'ID', key: true, hidden: true },
				{ name: 'user_id', index:'user_id', key: true, hidden: true },
				{ name: 'user_login_id', index:'user_login_id', key: true, hidden: true },				 
				{ name: 'username', index:'username', search: true },
				{ name: 'name', index: 'name', search: true},	
				{ name: 'globaladmin', index:'globaladmin', search: false },
				{ name: 'sessionmod', index: 'sessionmod', search: false },
				{ name: 'edit', index: 'edit', search: false },
				{ name: 'delete', index: 'delete', search: false }						
			],

			rowNum:10,
			multiselect: true,
			rowTotal: 100000,
			rowList : [50,100,200],
			loadonce:false,
			mtype: "GET",
			rownumbers: true,
			rownumWidth: 40,
			gridview: true,
			pager: '#usersPage',
			sortname: 'id',
			viewrecords: true,
			sortorder: "asc",
	    ignoreCase:true,
			toppager:false,
			cloneToTop:true,	
			
			//hiddengrid: true,
			caption: "<h2>Administrators</h2>"   
		});

		//grid.jqGrid('navGrid','#ptoolbar',{del:false,add:false,edit:false,search:false});
	  //grid.jqGrid('navGrid','#usersPage', { edit: true, add: true, del: false},
		// grid.jqGrid('navGrid','#usersPage', {  edit: false, add: false, del: false}, {sopt:['eq','ne','cn','bw','bn']});

		var  prmSearch = {
			multipleSearch:true,
			overlay:false
		};

		users_table.jqGrid(
			'navGrid',
			'#usersPage',
			{
				edit: false, 
				add: false, 
				del: false,
				refresh:true,search:false, 
				searchtitle: "Search",
				cloneToTop:true
			},
			{},
			{},
			{},
			prmSearch
		);

		//grid.jqGrid('navGrid','#usersPage',{del:false,add:false,edit:false,search:false });
		users_table.jqGrid(
			'filterToolbar',
			{
				stringResult: true,
				searchOnEnter : false,  
				defaultSearch : "cn"
			});		

			//add the clear button to the top bar
			users_table.jqGrid('navButtonAdd', '#' + users_table[0].id + '_toppager_left', { 
			// "#list_toppager_left"

			caption: "Clear Selection",
			
			id:"clear",
			
			buttonicon: 'ui-icon-arrowrefresh-1-s',

			onClickButton: function() {						

			}	
		});
		
		//add the reset password button to the top bar
		users_table.navGrid('#usersPage',{edit:true,add:true,del:false,search:false})
		.navButtonAdd('#usersPage',{
		   caption:"",
		   buttonicon:"ui-icon-export",
		   onClickButton: reset_selected,
		   position:"last"
		});

		$('.ui-pg-button .ui-icon-export').attr('title', 'Reset Password');

		//add the clear button to the top bar
		users_table.navGrid('#usersPage',{edit:true,add:true,del:false,search:false})
		.navButtonAdd('#usersPage',{
		   caption:"",
		   buttonicon:"ui-icon-delete",
		   onClickButton: activate_deactivate_staff,
		   position:"last"
		});

		$('.ui-pg-button .ui-icon-delete').attr('title', 'Activate/Deactivate');
		
		// remove some double elements from one place which we not need double	
		var topPagerDiv = $('#' + users_table[0].id + '_toppager')[0];         // "#list_toppager"	
		$("#edit_" + users_table[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"	
		$("#del_" + users_table[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"	
		$("#search_" + users_table[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"	
		$("#refresh_" + users_table[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"	
		$("#" + users_table[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"	
		$(".ui-paging-info", topPagerDiv).remove();	

		//Set bottomPagerDiv and then remove along with add grid
		var bottomPagerDiv = $("div#pager")[0];					
		$("#add_" + users_table[0].id, bottomPagerDiv).remove();               // "#add_list"	
				
		/* Select All */
		$("#selectAll").click(function(){	

			users_table.jqGrid('resetSelection');

			var ids = users_table.jqGrid('getDataIDs');

			for (var i=0, il=ids.length; i < il; i++) {	
				users_table.jqGrid('setSelection',ids[i], true);	
			}

		});	

		/* If clearing, reset selection */
		$("#clear").click(function(){	
			users_table.jqGrid('resetSelection');	
		});

		var checkbox_parent = $('input.cbox').parent().filter(function(){
			return ($(this).attr('role') == 'gridcell');
		});


		//Wait for HTML to build
		$(document).ajaxSuccess(function(){
			/* Set select all title */
			var select_all = $('#cb_users');
			if(select_all.length){
				select_all.attr('title', 'Select All');
			}	
			
			setTimeout(function(){
				checked_rows = $('#users .cbox');
				$('#users .cbox').on('click', function(event){
					var self = $(this);
	
					checked_rows = $('#users .cbox');
	
					if(self.is(':checked')){
						get_selected_administators(event.target);
					} else {
						self.removeAttr('checked'); //remove own checked value
			
						if(self.filter('#cb_users').length){ //remove all checked values
							checked_rows.removeAttr('checked');
						} else {
							var select_all = checked_rows.filter('#cb_users');
							
							//If select all is still selected
							if(select_all.length){
								select_all.removeAttr('checked');
							}
						}
					}					
				});
				
				//Make sure the fancybox is applied
				if(!$("td a.editContact").data().fancybox){
					$("td a.editContact").fancybox({
						'transitionIn'		: 'none',
						'transitionOut'		: 'none',
						'type'				: 'iframe',
						'height' : 600,
						'autoScale' : 'false',
						'onClosed': function() {
							parent.location.reload(true);
						}			
					});
				}
			}, 400);					
		});

		//Add the No. label
		var no_labels = $('#users_rn');
		
		no_labels.prepend('No.');		

		/**
		* Activate or deactivate roles according to selections
		**/
		$('#activate_btn').click();
	});//end of doc ready
})(jQuery);