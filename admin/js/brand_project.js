(function($){
	function launch_fancybox(url){
		//Create a fancybox div
		var element = $('<div id="empty_fancybox" />');

		element.fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'href'				: url,
			'type'				: 'iframe'		
		});

		element.click();
	}


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

		var brand_project_table = $('<table id="brand_project" />'),
				brand_project_div = $('<div id="bpPage" />');

		//Append brand project content
		content.append(brand_project_table).append(brand_project_div);

	  //Create Grid for Brand Project
	  brand_project_table.jqGrid({
			url:'jqgrid-json-brand_project_new.php?q=1',
			datatype: "json",
			autoheight: true,
			autowidth: true,
							
			colNames:['Company Name','BP Name', 'Max Sessions','Start Date','End Date','Configure','Analysis','Delete'],
			colModel:[
			 	{name:'CompanyName',index:'CompanyName',width:'10%'},
				{name:'name',index:'name',width:'15%'},
			 	{name:'max_sessions',index:'max_sessions',width:'5%'},				 
			  {name: 'start_date_bp', index: 'start_date_bp', sorttype:'date',  datefmt: "d-m-Y", searchoptions: {
				  	sopt: ['eq', 'ne', 'gt', 'lt'], 
						dataInit: function (elem) { 
							$(elem).datepicker({ 
								showButtonPanel: true,  
								onClose:function(){
                  var t = $("#brand_project")[0];
                  
                  t.triggerToolbar();
                }  
            	}); 

            	$(elem).datepicker( "option", "dateFormat", "dd-mm-yy");
 						} 
 					}, 
 					search: true,
 					width: '10%'
 				},
				{name: 'end_date_bp', index: 'end_date_bp', sorttype:'date',  datefmt: "d-m-Y", searchoptions: { 
					sopt: ['eq', 'ne', 'gt', 'lt'], 
						dataInit: function (elem){ 
							$(elem).datepicker({ 
								showButtonPanel: true,  
								onClose:function(){
                  var t = $("#brand_project")[0];
                  
                  t.triggerToolbar();
                }  
              }); 

              $(elem).datepicker( "option", "dateFormat", "dd-mm-yy");
 						} 
 					}, 
 					search: true,
 					width:'10%'
 				},
				{name:'configure',index:'configure', search: false, width:'2.5%'},
				{name:'analysis',index:'analysis', search: false, width:'2.5%', hidden: true},
				{name:'delete', index:'delete', width:'10%', search: false}
			],
			rowNum:10,
			rowTotal: 100000,
			rowList : [50,100,200],
			loadonce: false,
			mtype: "GET",
			rownumbers: true,
			rownumWidth: 40,
			gridview: true,
			pager: '#bpPage',
			sortname: 'name',
			viewrecords: true,
			sortorder: "asc",
      ignoreCase:true,
			caption: "<h2>Brand Projects</h2>"
		});		
	
		var prmSearch = {
			multipleSearch:true,
			overlay:false
		};
	  
	  brand_project_table.jqGrid('navGrid','#bpPage', { 
		  	edit: false, 
		  	add: false, 
		  	del: false,
		  	search:false, 
		  	searchtitle: "Find Records",
		  	refresh:true
		  },
      {},
      {},
      {},
      prmSearch
    );
	
	 	brand_project_table.jqGrid('navButtonAdd', '#bpPage', { 
	 			caption: "", 
	 			buttonicon: "ui-icon-calculator", 
	 			title: "Choose Columns",
				onClickButton: function() {
					brand_project_table.jqGrid('columnChooser');
				}
			}
		);		 

		brand_project_table.jqGrid('filterToolbar',{
			stringResult: true,
			searchOnEnter: false,  
			defaultSearch : "cn"
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
		var no_labels = $('#jqgh_brand_project_rn');
		
		no_labels.prepend('No.');	
	});
})(jQuery);
