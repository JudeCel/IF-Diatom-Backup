(function($){
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

		/* Create HTML meny for tabs */
		var tabs_html = '<div id="tabs">',
				graphs_html = '<div class="content"><div class="inner">',
				content = $('#content .content > .inner'),
				browser_check = '<div class="text">Analytics not optimised for this browser. Google Chrome or Mozilla Firefox are recommended alternatives.</div>';

		tabs_html += '<div class="sub_navigation">';
		tabs_html += '<ul>';				

		/* Tabs that can be used */
		var tabs_used = new Object();

		tabs_used['Gender'] = '#tabs-1';
		tabs_used['Suburb'] = '#tabs-2';		
		tabs_used['State'] = '#tabs-3';
		tabs_used['Country'] = '#tabs-4';		
		tabs_used['Ethnicity'] = '#tabs-5';
		tabs_used['Occupation'] = '#tabs-6';
		tabs_used['Brand Segment'] = '#tabs-7';
		tabs_used['Optional 01'] = '#tabs-8';
		tabs_used['Optional 02'] = '#tabs-9';
		tabs_used['Optional 03'] = '#tabs-10';
		tabs_used['Optional 04'] = '#tabs-11';
		tabs_used['Optional 05'] = '#tabs-12';

		//Create menu items
		$.each(tabs_used, function(name, url){
			tabs_html += '<li>';
			tabs_html += '<a href="' + url + '">' + name + '</a>';
			tabs_html += '</li>';
		});

		tabs_html += '</ul>';
		tabs_html += '</div>'; 
		tabs_html += '</div>';

		content.append(tabs_html); //add tabs

		//Create graphs containers
		graphs_html += '<div id="tabs-1">';
		graphs_html += '<div id="pie1">'; //tab 1
		graphs_html += '</div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-2">'; //tab 2
		graphs_html += '<div id="chart1"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-3">'; //tab 3
		graphs_html += '<div id="chart2"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-4">'; //tab 4
		graphs_html += '<div id="chart3"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-5">'; //tab 5
		graphs_html += '<div id="chart4"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-6">'; //tab 6
		graphs_html += '<div id="chart5"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-7">'; //tab 7
		graphs_html += '<div id="chart6"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-8">'; //tab 8
		graphs_html += '<div id="chart7"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-9">'; //tab 9
		graphs_html += '<div id="chart8"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-10">'; //tab 10
		graphs_html += '<div id="chart9"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-11">'; //tab 11
		graphs_html += '<div id="chart10"></div>';
		graphs_html += '</div>';

		graphs_html += '<div id="tabs-12">'; //tab 12
		graphs_html += '<div id="chart11"></div>';
		graphs_html += '</div>';

		graphs_html += '</div>';
		graphs_html += '</div>';

		$('#tabs').append(graphs_html).tabs(); //add to tabs
		$("#accordion").accordion();

		//Add browser check
		content.find('.ui-tabs-panel').append(browser_check);

		if(male_participants || female_participants){
			var plot2 = $.jqplot('pie1', [[['Female', female_participants],['Male', male_participants]]], {
	      gridPadding: {top:0, bottom:38, left:0, right:0},

	      seriesDefaults:{
	          renderer: $.jqplot.PieRenderer, 
	          trendline: { show:true }, 
	          rendererOptions: { padding: 8, showDataLabels: true }
	      },

	      legend:{
	          show:true, 
	          placement: 'outside', 
	          rendererOptions: {
	              numberRows: 1
	          }, 
	          location:'s',
	          marginTop: '15px'
	      }       
	    }); 
	  } else {
	  	$('#pie1').text('No analytics available.');
	  } 

	  if(!$.isEmptyObject(suburb_participants_json)){
			$.jqplot.config.enablePlugins = false;
		 
			plot2 = $.jqplot('chart1', [suburb_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,

				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					pointLabels: { show: true }
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {			
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Suburb',
						
						ticks: suburb_json
					},

					yaxis:{
						//min:0,
						//max:5,
						tickOptions: { 
							formatString: '%d' 
						},
						//tickInterval: 1,  
						label:'Participants'
					}
				},

				highlighter: { //show: false			
				
				},

				cursor:{
	          show: true, 
	          zoom: true
	      } 
			});
		} else {
			$('#chart1').text('No analytics available.');
		} 

		if(!$.isEmptyObject(state_participants_json)){
			$.jqplot.config.enablePlugins = false;
		 
			plot3 = $.jqplot('chart2', [state_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,

				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					pointLabels: { show: true }
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {			
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'State',
						
						ticks: state_json
					},

					yaxis:{
						//min:0,
						//max:5,
						tickOptions: { 
							formatString: '%d' 
						},
						//tickInterval: 1,  
						label:'Participants'
					}
				},

				highlighter: { //show: false			
				
				},

				cursor:{
	          show: true, 
	          zoom: true
	      } 
			});
		} else {
			$('#chart2').text('No analytics available.');
		}

		if(!$.isEmptyObject(country_participants_json)){
			$.jqplot.config.enablePlugins = false;
		 
			plot4 = $.jqplot('chart3', [country_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,

				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					pointLabels: { show: true }
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {			
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Country',
						
						ticks: country_json
					},

					yaxis:{
						//min:0,
						//max:5,
						tickOptions: { 
							formatString: '%d' 
						},
						//tickInterval: 1,  
						label:'Participants'
					}
				},

				highlighter: { //show: false			
				
				},

				cursor:{
	          show: true, 
	          zoom: true
	      } 
			});
		} else {
			$('#chart3').text('No analytics available.');
		}

		if(!$.isEmptyObject(ethnicity_participants_json)){
			$.jqplot.config.enablePlugins = false;

			var plot5 = $.jqplot('chart4', [ethnicity_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					pointLabels: { show: true }
				},
				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
	          fontFamily: 'Arial, Helvetica, sans-serif',
	          fontSize: '12pt'
	        }
				}, 
			
				axes: {			
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Ethnicity',
						
						ticks: ethnicity_json
					},
					yaxis:{
						//min:0,
						//max:5,
						tickOptions: { 
							//formatString: '%d' 
						},
						//tickInterval: 1,  
						label:'Participants'
					}
				},

				highlighter: { //show: false			
				
				},

				cursor:{
	          show: true, 
	          zoom: true
	      } 
			});

			/*$.jqplot.config.enablePlugins = false;

			var plot5 = $.jqplot('chart4',[bubble_array_json],{
	        title: 'Ethnicity',
	        seriesDefaults:{
	          renderer: $.jqplot.BubbleRenderer,
	          rendererOptions: {
	            bubbleGradients: true,
							autoscaleBubbles:true				
	          },
	          shadow: true		
	        }
	    });*/
	  } else {
			$('#chart4').text('No analytics available.');
		}

		if(!$.isEmptyObject(occupation_participants_json)){
			$.jqplot.config.enablePlugins = false;

			plot6 = $.jqplot('chart5', [occupation_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					pointLabels: { show: true }
				},
				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
	          fontFamily: 'Arial, Helvetica, sans-serif',
	          fontSize: '12pt'
	        }
				}, 
			
				axes: {			
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Occupation',
						
						ticks: occupations_json
					},
					yaxis:{
						//min:0,
						//max:5,
						tickOptions: { 
							//formatString: '%d' 
						},
						//tickInterval: 1,  
						label:'Participants'
					}
				},

				highlighter: { //show: false			
				
				},

				cursor:{
	          show: true, 
	          zoom: true
	      } 
			});
		} else {
			$('#chart5').text('No analytics available.');
		}	

		if(!$.isEmptyObject(segment_participants_json)){
	    $.jqplot.config.enablePlugins = true;
		 
			plot7 = $.jqplot('chart6', [segment_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					//pointLabels: { show: true },
					trendline:{ show:true }, 
		      rendererOptions: { padding: 8, showDataLabels: true }
				},	

				title:{
					//text     : 'Brand Segments',
					//show     : true,
					//textAlign: "center",
					//textColor: "#C00000"
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Brand Segment',
						
						ticks: brand_segment_json
					},

					yaxis:{
						//min:0, 
						//max:5, 
						//numberTicks:10, 
						
						//tickInterval: 10, 
						tickOptions: { 
							formatString: '%d' 
						} ,
						
						label:'Participants'
					}
				},

				highlighter: { //show: false		
				
				},

				cursor:{
		      show: true 
		      //zoom: true
		    } 
			});
		} else {
			$('#chart6').text('No analytics available.');
		}

		//Optional 01
		if(!$.isEmptyObject(optional01_participants_json)){
	    $.jqplot.config.enablePlugins = true;
		 
			plot8 = $.jqplot('chart7', [optional01_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					//pointLabels: { show: true },
					trendline:{ show:true }, 
		      rendererOptions: { padding: 8, showDataLabels: true }
				},	

				title:{
					//text     : 'Brand Segments',
					//show     : true,
					//textAlign: "center",
					//textColor: "#C00000"
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label: 'Optional 01',
						
						ticks: optional01_json
					},

					yaxis:{
						//min:0, 
						//max:5, 
						//numberTicks:10, 
						
						//tickInterval: 10, 
						tickOptions: { 
							formatString: '%d' 
						} ,
						
						label:'Participants'
					}
				},

				highlighter: { //show: false		
				
				},

				cursor:{
		      show: true 
		      //zoom: true
		    } 
			});
		} else {
			$('#chart7').text('No analytics available.');
		}

		//Optional 02
		if(!$.isEmptyObject(optional02_participants_json)){
	    $.jqplot.config.enablePlugins = true;
		 
			plot9 = $.jqplot('chart8', [optional02_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					//pointLabels: { show: true },
					trendline:{ show:true }, 
		      rendererOptions: { padding: 8, showDataLabels: true }
				},	

				title:{
					//text     : 'Brand Segments',
					//show     : true,
					//textAlign: "center",
					//textColor: "#C00000"
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Optional 02',
						
						ticks: optional02_json
					},

					yaxis:{
						//min:0, 
						//max:5, 
						//numberTicks:10, 
						
						//tickInterval: 10, 
						tickOptions: { 
							formatString: '%d' 
						} ,
						
						label:'Participants'
					}
				},

				highlighter: { //show: false		
				
				},

				cursor:{
		      show: true 
		      //zoom: true
		    } 
			});
		} else {
			$('#chart8').text('No analytics available.');
		}

		//Optional 03
		if(!$.isEmptyObject(optional03_participants_json)){
	    $.jqplot.config.enablePlugins = true;
		 
			plot10 = $.jqplot('chart9', [optional03_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					//pointLabels: { show: true },
					trendline:{ show:true }, 
		      rendererOptions: { padding: 8, showDataLabels: true }
				},	

				title:{
					//text     : 'Brand Segments',
					//show     : true,
					//textAlign: "center",
					//textColor: "#C00000"
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Optional 03',
						
						ticks: optional03_json
					},

					yaxis:{
						//min:0, 
						//max:5, 
						//numberTicks:10, 
						
						//tickInterval: 10, 
						tickOptions: { 
							formatString: '%d' 
						} ,
						
						label:'Participants'
					}
				},

				highlighter: { //show: false		
				
				},

				cursor:{
		      show: true 
		      //zoom: true
		    } 
			});
		} else {
			$('#chart9').text('No analytics available.');
		}

		//Optional 04
		if(!$.isEmptyObject(optional01_participants_json)){
	    $.jqplot.config.enablePlugins = true;
		 
			plot11 = $.jqplot('chart10', [optional04_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					//pointLabels: { show: true },
					trendline:{ show:true }, 
		      rendererOptions: { padding: 8, showDataLabels: true }
				},	

				title:{
					//text     : 'Brand Segments',
					//show     : true,
					//textAlign: "center",
					//textColor: "#C00000"
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Optional 04',
						
						ticks: optional04_json
					},

					yaxis:{
						//min:0, 
						//max:5, 
						//numberTicks:10, 
						
						//tickInterval: 10, 
						tickOptions: { 
							formatString: '%d' 
						} ,
						
						label:'Participants'
					}
				},

				highlighter: { //show: false		
				
				},

				cursor:{
		      show: true 
		      //zoom: true
		    } 
			});
		} else {
			$('#chart10').text('No analytics available.');
		}

		//Optional 05
		if(!$.isEmptyObject(optional05_participants_json)){
	    $.jqplot.config.enablePlugins = true;
		 
			plot12 = $.jqplot('chart11', [optional05_participants_json], {
				// Only animate if we're not using excanvas (not in IE 7 or IE 8)..
				animate: !$.jqplot.use_excanvas,
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					//pointLabels: { show: true },
					trendline:{ show:true }, 
		      rendererOptions: { padding: 8, showDataLabels: true }
				},	

				title:{
					//text     : 'Brand Segments',
					//show     : true,
					//textAlign: "center",
					//textColor: "#C00000"
				},

				grid:{	
					background : "#E8E6E6"
				},
			
				axesDefaults:{ 
					//useSeriesColor: true, 
					
					//tickInterval: 0, 
					//tickOptions: { 
					//		formatString: '%d' 
					//	} 
					labelOptions: {
		        fontFamily: 'Arial, Helvetica, sans-serif',
		        fontSize: '12pt'
		      }
				}, 
			
				axes: {
					xaxis: {
						renderer: $.jqplot.CategoryAxisRenderer,
						
						label:'Optional 05',
						
						ticks: optional05_json
					},

					yaxis:{
						//min:0, 
						//max:5, 
						//numberTicks:10, 
						
						//tickInterval: 10, 
						tickOptions: { 
							formatString: '%d' 
						} ,
						
						label:'Participants'
					}
				},

				highlighter: { //show: false		
				
				},

				cursor:{
		      show: true 
		      //zoom: true
		    } 
			});
		} else {
			$('#chart11').text('No analytics available.');
		}

		//this is the function to hide/show diff tabs
		$('#tabs').bind('tabsshow', function(event, ui) {			
			//first if makes sure the 2nd tab is drawn	
			if (typeof plot2 !== 'undefined' && ui.index == 1 && plot2._drawCount == 0) {
				plot2.replot();

			} else if (typeof plot3 !== 'undefined' && ui.index == 2 && plot3._drawCount == 0) { //the 2nd if makes sure the 3rd tab is drawn
				plot3.replot();

			} else if (typeof plot4 !== 'undefined' && ui.index == 3 && plot4._drawCount == 0) { //the 3rd if makes sure the 4th tab is drawn	
				plot4.replot();

			}	else if (typeof plot5 !== 'undefined' && ui.index == 4 && plot5._drawCount == 0) { //the 4th if makes sure the 5th tab is drawn
				plot5.replot();

			} else if (typeof plot6 !== 'undefined' && ui.index == 5 && plot6._drawCount == 0) { //the 5th if makes sure the 6th tab is drawn
				plot6.replot();
			} else if (typeof plot7 !== 'undefined' && ui.index == 6 && plot7._drawCount == 0) { //the 6th if makes sure the 7th tab is drawn
				plot7.replot();
			}	else if (typeof plot8 !== 'undefined' && ui.index == 7 && plot8._drawCount == 0) { //the 7th if makes sure the 8th tab is drawn
				plot8.replot();
			} else if (typeof plot9 !== 'undefined' && ui.index == 8 && plot9._drawCount == 0) { //the 8th if makes sure the 9th tab is drawn
				plot9.replot();
			} else if (typeof plot10 !== 'undefined' && ui.index == 9 && plot10._drawCount == 0) { //the 9th if makes sure the 10th tab is drawn
				plot10.replot();
			} else if (typeof plot11 !== 'undefined' && ui.index == 10 && plot11._drawCount == 0) { //the 10th if makes sure the 11th tab is drawn
				plot11.replot();
			} else if (typeof plot12 !== 'undefined' && ui.index == 11 && plot12._drawCount == 0) { //the 10th if makes sure the 11th tab is drawn
				plot12.replot();
			}
		});

		var text = $('.text');

		text.remove();

	});
})(jQuery);