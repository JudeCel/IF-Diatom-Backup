(function($){
	// function to compare the date
	var compareDate = function(aDate){
  	var cDate = new Date();
	
  	var tDate = aDate.split("-"),
  			day   = tDate[0],
  			month = tDate[1]-1,
  			year =  tDate[2],
  			aDate = new Date(year,month,day),		
  			tDate = Date.parse(aDate);

		if(isNaN(tDate) == false){    // if valid date
			var one_day  = 1000 * 60 * 60 *24,   // miliseconds * seconds * mins * hours
					curr_day = cDate.getTime(),
					old_day  = aDate.getTime(),
					day_diff_ms = 0,
					day_diff = 0;

			console.log("current: " + cDate)
			console.log("end date" + aDate)
			
			if(old_day > curr_day){
				day_diff_ms = old_day - curr_day;
				day_diff = Math.round(day_diff_ms / one_day);

				if(day_diff < 30){
						return 1;
				} else {
					return 3;
				}
			}
  	} else {
			return 2;
		} 	
  };

	$(document).ready(function(){
		var content = $('#content > .inner'); //content div

		//If user has permission to access a client company
		var session_table = $('<table id="session" />'),
				session_div = $('<div id="sessionPage" />');

		//Append brand project content
		content.append(session_table).append(session_div);

		session_table.jqGrid({
			url:'jqgrid-json-session.php?q=1',
			datatype: "json",
			autoheight: true,
			autowidth: true,
								
			colNames:['Company Name','BP Name','Session Name', 'Facilitator','Start Time', 'End Time', 'Status', 'Configure', 'Enter','Delete'],
			colModel:[
				{name:'CompanyName',index:'CompanyName',width:'15%'},			 
			 	{name:'BPName',index:'BPName',width:'15%'},
			 	{name:'name',index:'name',width:'15%'},
			 
			 	{name:'moderator_name',index:'moderator_name',width:'25%', search: true},
				{name: 'start_time', index: 'start_time', searchoptions: { 
						sopt: ['eq', 'ne', 'gt', 'lt'], 
						dataInit: function (elem) { 
							$(elem).datetimepicker({ 
								showButtonPanel: true,  
								onClose:function(){
	                var t = $("#session")[0];
	                
	                t.triggerToolbar();
	              }  
	          	}); 

	          	$(elem).datetimepicker( "option", "dateFormat", "dd-mm-yy");
	 					} 
	 				}, 
	 				search: true,
	 				width:'10%'
	 			},
				{name: 'end_time', index: 'end_time', searchoptions: { 
						sopt: ['eq', 'ne', 'gt', 'lt'], 
						dataInit: function (elem){ 
							$(elem).datetimepicker({ 
								showButtonPanel: true,  
								onClose:function(){
                  var t = $("#session")[0];
                  t.triggerToolbar();
                }  
              }); 

              $(elem).datetimepicker( "option", "dateFormat", "dd-mm-yy");
 						} 
 					}, 
 					search: true,
 					width:'10%'
 				},
 				{name:'status', index:'status',  search: true},
				{name:'configure', index:'configure', search: false, width:'5%'},
				{name:'enter', index:'enter', search: false, width:'5%'},
				{name:'delete', index:'delete', search: false, width:'5%'}
			],
			rowNum:10,
			rowTotal: 100000,
			rowList : [50,100,200],
			loadonce: false,
			mtype: "GET",
			rownumbers: true,
			rownumWidth: 40,
			gridview: true,
			pager: '#sessionPage',
			sortname: 'start_time',
			viewrecords: true,
			sortorder: "asc",
      ignoreCase:true,
			caption: "<h2>Sessions</h2>"
		});
		
		var prmSearch = {
			multipleSearch:true,
			overlay:false
		};
		 
		session_table.jqGrid('navGrid','#sessionPage', {
				edit: false, 
				add: false, 
				del: false, 
				search:false, 
				searchOnEnter: true,
				searchtitle: "Find Records",
				refresh:false
			},
      {},
      {},
      {},
      prmSearch
    );		
		 
		session_table.jqGrid('navButtonAdd', '#sessionPage', {
			caption: "", 
			buttonicon: "ui-icon-calculator", 
			title: "Choose Columns",
			onClickButton: function(){
				session_table.jqGrid('columnChooser');
			}
		});

		session_table.jqGrid('filterToolbar', {
				stringResult: true,
				searchOnEnter : false,  
				defaultSearch : "cn"
			}
		);

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

	  $("#logoutlink").click(function (e) {         
	  	$('#logout').dialog('open');     
	  });

	  //Add the No. label
		var no_labels = $('#jqgh_session_rn');
		
		no_labels.prepend('No.');	
	});
})(jQuery);
