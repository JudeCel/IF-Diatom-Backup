(function($){
	var ids = null;
	
	function launch_fancybox(url){
		//Create a fancybox div
		var element = $('<div id="empty_fancybox" />');

		element.fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'href'				: url,
			'height'			: '100%',
			'width'				: 1000,
			'type'				: 'iframe',
			'onClosed': function() {
				parent.location.reload(true);
			}		
		});

		element.click();
	}

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

	/**
	* Get selected participants, if jqgrid's method fails
	**/
	function get_selected_checkboxes(){
		var ids = new Array(),
				checked_rows = $('#participant .cbox');

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
	function get_selected_participants(self){
		var checked_rows = $('#participant .cbox');

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

	$(document).ready(function () {
		var participant_panel = $('<table id="participant" />"'),
				participant_panel_page = $('<div id="participantPage" />'),
				content = $('#content > .inner'), //content div
				items = content.find('.items');

		items.text('');		

		//Append participant_panel content
		items.append(participant_panel).append(participant_panel_page);

		var url = 'jqgrid-json-participant-panel.php?brand_project_id=' + brand_project_id + '&session_id=' + session_id;

		participant_panel.jqGrid({		
			url: url,
			
			datatype: 'json',				
			
			autoheight: true,
			
			autowidth: true,				
	    
	    mtype: 'GET',
			
			loadonce: false,
	    
	    colNames:[
				'ID',
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
				'Invites - Not Now',
				'Invites - Not Interested',
				'Invites - No Reply',
				'Optional 1',
				'Optional 2',
				'Optional 3',
				'Optional 4',
				'Optional 5',
				'LastSess',				
				'Interested',
				'Rating',
				'Inv Again',
				'Comment'		
			],
	    colModel: [
	      { name: 'ID', key: true,sorttype:"int" ,align: "center", hidden: true, hidedlg: true},
				{ name: 'name', index: 'name', hidden: false },

	      { name: 'gender', editable: true, edittype:"select", editoptions:{value:"Male:Male;Female:Female"} ,sortable: true, hidden: true},

	      { name: 'email',editable: true, sortable: false, hidden: false },

				{ name: 'mobile', editable: true,sortable: false, hidden: false },

				{ name: 'phone', editable: true,sortable: false, hidden: true },

				{ name: 'fax', editable: true,sortable: false, hidden: true},
				
				{ name: 'street', sortable: true, hidden: true },
				
				{ name: 'state', sortable: true, hidden: true },
				
				{ name: 'suburb', sortable: true, hidden: true },
				
				{ name: 'postcode', sortable: true, hidden: true },

				{ name: 'dob', sortable: true, hidden: true },
				
				{ name: 'ethnicity', editable: true, sortable: true, hidden: true },
				{ name: 'occupation', editable: true, sortable: true, hidden: true },
				{ name: 'brandsegment', editable: true, sortable: true, hidden: true },
				{ name: 'country', editable: true, sortable: true, hidden: true },
				{name: 'number_of_invites', index: 'number_of_invites', editable: false, sortable: true, hidden: false},
				{name: 'invites_accepted', index: 'invites_accepted', editable: false, sortable: true, hidden: false},
				{name: 'invites_not_now', index: 'invites_not_now', editable: false, sortable: true, hidden: true},
				{name: 'invites_not_interested', index: 'invites_not_interested', editable: false, sortable: true, hidden: true},
				{name: 'invites_no_reply', index: 'invites_no_reply', editable: false, sortable: true, hidden: true},
				{name:'optional1', index:'optional1', hidden: true},
				{name:'optional2', index:'optional2', hidden: true},
				{name:'optional3', index:'optional3', hidden: true},
				{name:'optional4', index:'optional4', hidden: true},
				{name:'optional5', index:'optional5', hidden: true},
				{name: 'last_invited_session_name', index: 'last_invited_session_name', editable: false, sortable: true, hidden: false},				
				{name: 'interested', index: 'interested', editable: false, sortable: true, hidden: true},
				{name: 'rating15', index: 'rating15', editable: false, sortable: true, hidden: false},
				{name: 'invite_again', index: 'invite_again', editable: false, sortable: true, hidden: false},
				{name: 'comments', index: 'comments', editable: false, sortable: true, hidden: false},			
	    ],
	    rowNum: 10,
	    rowTotal: 100000,
	    rowList: [10, 20, 300],
	    multiselect: true,
	    pager: "#participantPage",
	    viewrecords: true,
	    gridview: true,
	    rownumbers: true,
	    shrinkToFit: false,				
			ignoreCase:true,				
			toppager: false,
			rownumbers: true,
			sortname: 'ParticipantId'
		});
					
		//function for successful edit
		function reload(result) {
			participant_panel.trigger("reloadGrid"); 
		}

		//set up the basic jqgrid
		var  prmSearch = {multipleSearch:true,overlay:false};
		
		//this enables the buttons on the navbar	
		participant_panel.jqGrid(
			'navGrid',
			'#participantPage',
			{edit: false, add: false, del: false,refresh:true,search:false, searchtitle: "Find Records",cloneToTop:true},
			{},
			{},
			{},
			prmSearch
		);
		
		participant_panel.jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false,  defaultSearch : "cn"});
		
		//column chooser
	  participant_panel.jqGrid('navButtonAdd', '#participantPage', { caption: "", buttonicon: "ui-icon-calculator", title: "Choose Columns",
			onClickButton: function() {

				participant_panel.jqGrid('columnChooser');
			}
	  });
		
		//add the clear button to the top bar
		participant_panel.jqGrid('navButtonAdd', '#' + participant_panel[0].id + '_toppager_left', {
			caption: "Clear Selection",
			
			id:"clear",
			
			buttonicon: 'ui-icon-arrowrefresh-1-s',

			onClickButton: function() {	

			}

		});

		// remove some double elements from one place which we not need double

		var topPagerDiv = $('#' + participant_panel[0].id + '_toppager')[0];         // "#list_toppager"

		$("#edit_" + participant_panel[0].id + "_top", topPagerDiv).remove();        // "#edit_list_top"

		$("#del_" + participant_panel[0].id + "_top", topPagerDiv).remove();         // "#del_list_top"

		$("#search_" + participant_panel[0].id + "_top", topPagerDiv).remove();      // "#search_list_top"

		$("#refresh_" + participant_panel[0].id + "_top", topPagerDiv).remove();     // "#refresh_list_top"

		$("#" + participant_panel[0].id + "_toppager_center", topPagerDiv).remove(); // "#list_toppager_center"

		$(".ui-paging-info", topPagerDiv).remove();
		
		
		
		var bottomPagerDiv = $("div#pager")[0];

		//$("#add_" + participant_panel[0].id, bottomPagerDiv).remove();               // "#add_list"
				
		$("#selectAll").click(function(){

			participant_panel.jqGrid('resetSelection');

			var ids = participant_panel.jqGrid('getDataIDs');

			for (var i=0, il=ids.length; i < il; i++) {
				participant_panel.jqGrid('setSelection',ids[i], true);
			}

		});
		
		$("#clear").click(function(){

			participant_panel.jqGrid('resetSelection');

		});

		/* go to sms page*/
		$("#btnSMS").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'href'				: 'participant-sms.php?session_id=' + session_id,
			'height'			: '100%',
			'width'				: 1000,
			'type'				: 'iframe',

			'onComplete' : function(){
				$.fancybox.resize();
			},

		 	'onClosed': function() {
				parent.location.reload(true);
		  	}			
		});

		setTimeout(function(){
			/* Set select all title */
			var select_all = $('#cb_participant');
			if(select_all.length){
				select_all.attr('title', 'Select All');
			}
		}, 400);

		//code for the delete link dialog boxes
		$('.deleteButton').click(function(){
		   
			var id = this.id.replace(/[^0-9]/g, ""),
					unixtime = Math.round((new Date()).getTime() / 1000);
			//alert('Hello There'+id);
			
			$('#deleteText').dialog({
				modal:true,
				resizable: false,
				autoOpen: true,
				buttons: {
					'Yes': function() {
						window.location = "session-participant-delete.php?participant_lists_id="+id + '&ajax=' + unixtime;
						$(this).dialog('close');
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				}
			});   
		});

		//add the log out dialog code
		$("#logout").dialog(
		{         
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

		//logout
		$("#logoutlink").click(function (e) {         
	  	$('#logout').dialog('open');     
	});

	/* Send SMS */
	$("#getSelectedSMS").click(function(){
	  	var self = $(this);
			
		if (!ids){
			ids =  participant_panel.jqGrid('getGridParam','selarrrow');
	
			if(!ids.length){
				ids = get_selected_checkboxes();
			}		
		}

	  	//If ids selected
	  	if(ids.length){
	  		var selected_ids = new Array();

	  		//Make the ids usable
	  		$.each(ids, function(key, value){
	  			selected_ids.push(parseInt(value));
	  		});

	  		var url = 'participant-sms.php?session_id=' + session_id + '&part_ids=' + JSON.stringify(selected_ids);

	  		launch_fancybox(url);
	  	}

	  	return false;
	});

	$("#btnGeneric").click(function(){	
		console.log('in');
		var participant_id=[];
		var i=0;
		$('.idname').each(function(){
			participant_id[i]=parseInt($(this).attr('id'));
			i++;
		});

		if(participant_id.length){		
					console.log(participant_id);
			dialog_name = $('<div id="dialog-confirm" />');
			dialog_name.text('Are you sure want to sent an e-mail to the all invited participants?');
			
			dialog_name.dialog({
				height:280,
				modal:true,	
				buttons:{	
					'Cancel': function(){
						$(this).dialog('close');
					},
					'Confirm': function(){	
						window.location = "selected-session-participants.php?session_id=" + session_id +"&email_type_id=" +  6 + "&participant_id=" + participant_id;
						$(this).dialog('close');							
					}	
				}
			});	
		}
		return false;
	});
						
	$("#getSelected, #getSelectedGeneric").click(function(){	
		var self = $(this),
			self_id = self.attr('id');
		
		if (!ids){
			ids =  participant_panel.jqGrid('getGridParam','selarrrow');
	
			if(!ids.length){
				ids = get_selected_checkboxes();
			}		
		}
	
		if(ids.length){		
			var names = [];
			var participant_id=ids;
			
			dialog_name = $('<div id="dialog-confirm" />');
			dialog_name.text('Are you sure want to sent an e-mail to the selected people?');
			
			dialog_name.dialog({
				height:280,
				modal:true,	
				buttons:{	
					'Cancel': function(){
						$(this).dialog('close');
					},

					'Confirm': function(){	
						window.location = "selected-session-participants.php?session_id=" + session_id +"&email_type_id=" + (self_id == 'getSelected' ? 1 : 6) + "&participant_id=" + participant_id;
						$(this).dialog('close');							
					}	
				}
			});	
		}
		return false;
	});

		if(participants_json.length){
			$.each(participants_json, function(key, id){
				var mobile_edit = $("#mobile_edit" + id);

				if(mobile_edit.length){
					$("#mobile_edit" + id).editable("save-comments.php?participant_lists_id=" + id, { 
		        name: "mobile",
		        indicator : "Saving...",
		        tooltip   : "Click to edit...",
		        event     : "click",
		        style  : "inherit"
		      });
		    }
			});
		}
	});
	
	$(document).ajaxSuccess(function(){
		setTimeout(checkbox_click_setup, 400);
	});	
	
	function checkbox_click_setup(){
		checked_rows = $('#participant .cbox');
	
		$('#participant .cbox').off('click', checkbox_click);
		$('#participant .cbox').on('click', checkbox_click);	
	}
	
	function checkbox_click(event){
		var self = $(this);

		if(self.is(':checked')){
			get_selected_participants(event.target);
		} else {
			self.removeAttr('checked'); //remove own checked value
			
			if(self.filter('#cb_participant').length){ //remove all checked values
				checked_rows.removeAttr('checked');
			} else {
				var select_all = checked_rows.filter('#cb_participant');
				
				//If select all is still selected
				if(select_all.length){
					select_all.removeAttr('checked');
				}
			}
		}
		
		ids = get_selected_checkboxes();
	}
})(jQuery);