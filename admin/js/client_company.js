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
		var client_company_table = $('<table id="client_company" />'),
				client_company_div = $('<div id="client_companyPage" />');

		//Append client company content
		content.append(client_company_table).append(client_company_div);

		//Create Grid for client company
		client_company_table.jqGrid({
			url:'jqgrid-json-client_company.php?q=1',
			datatype: "json",
			autoheight: true,
			autowidth: true,
							
			colNames:['Company Name', 'Contact Name', 'Comments','Start Date','End Date','Address','URL','ABN','Configure','Delete'],
			colModel:[
			 	{name:'name',index:'name'},
			 	{name:'PrimaryContact',index:'PrimaryContact'},
			  {name:'CompanyComments',index:'CompanyComments'},
			  {name: 'start_date', index: 'start_date', sorttype:'date',  datefmt: "d-m-Y", searchoptions: { 
				 		sopt: ['eq', 'ne', 'gt', 'lt'], 
						dataInit: function (elem) { 
							$(elem).datepicker({ 
								showButtonPanel: true,  
								onClose:function(){
					 				var t = $("#client_company")[0];
                  t.triggerToolbar();
                 }  
              }); 

              $(elem).datepicker( "option", "dateFormat", "dd-mm-yy");
					 } 
					}, 
					search: true
				},
				{name: 'end_date', index: 'end_date', sorttype:'date',  datefmt: "d-m-Y", searchoptions: {
						sopt: ['eq', 'ne', 'gt', 'lt'], 
						dataInit: function (elem) { 
							$(elem).datepicker({ 
								showButtonPanel: true,  
								onClose:function(){
									var t=$("#client_company")[0];
	                t.triggerToolbar();
	              }
	            }); 
	            $(elem).datepicker( "option", "dateFormat", "dd-mm-yy");
						} 
					}, 
					search: true
				},
				{name:'Address',index:'Address'},
				{name:'URL',index:'URL'},
				{name:'ABN', hidden: true},								
				{name:'Configure',index:'Configure', search: false},
				{name:'Delete',index:'Delete', search: false}
			],
			rowNum:10,
			rowTotal: 100000,
			rowList : [50,100,200],
			loadonce: false,
			mtype: "GET",
			rownumbers: true,
			rownumWidth: 40,
			gridview: true,
			pager: '#client_companyPage',
			sortname: 'name',
			viewrecords: true,
			sortorder: "asc",
      ignoreCase:true,
      caption: "<h2>Companies</h2>"
		});		
	
		var prmSearch = {
			multipleSearch: true,
			overlay: false
		};

	  client_company_table.jqGrid('navGrid', '#client_companyPage', { 
	  		edit: false, 
	  		add: false, 
	  		del: false,
	  		search: false, 
	  		searchtitle: "Find Records",
	  		refresh: false
	  	},
      {},
      {},
      {}, 
      prmSearch
    );

	  //add button
	  client_company_table.jqGrid('navButtonAdd', '#client_companyPage', {
  		caption: "", 
  		buttonicon: "ui-icon-calculator", 
  		title: "Choose Columns",
			onClickButton: function(){
				client_company_table.jqGrid('columnChooser');
			}
		});

		// Add the delete participant option here		
		client_company_table.jqGrid('navButtonAdd', '#client_companyPage', {
  		caption: "", 
  		buttonicon: "ui-icon-add", 
  		title: "Add Client Company",
  		position: "first",
  		onClickButton: function(){
  			window.location.href = 'signup.php'; //go to signup
  		}
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
		var no_labels = $('#jqgh_client_company_rn');
		
		no_labels.prepend('No.');	
	});
})(jQuery);
