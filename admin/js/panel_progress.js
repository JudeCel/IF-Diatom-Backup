(function($){
	$(document).ready(function(){
		var panel_info = $('<div id="panel_info" />'),
				section_information = $('.section_information'),
				sub_navigation = section_information.find('.sub_navigation');

		panel_info.hide();

		/* Set brand project id if not defined */
		if(typeof brand_project_id === 'undefined'){
			brand_project_id = null;
		}

		/* Set client company id if not defined */
		if(typeof client_company_id === 'undefined'){
			client_company_id = null;
		}

		if(section_information.length && (client_company_id || brand_project_id)){			
			var url_prefix = 'getCompanyInfo.php?',
					url = '';			

			/* If client company id is set */
			if(client_company_id){
				url = url_prefix + 'client_company_id=' + client_company_id;
			}

			/* If client company id is set */
			if(brand_project_id){
				if(url){ //client company id was used
					url += '&brand_project_id=' + brand_project_id;
				} else { //client company id was not used
					url = url_prefix + 'brand_project_id=' + brand_project_id;
				}
			}			

			section_information.append(panel_info);

			//load in
			$.when($.get(url + '&ajax=1', function(data){
				panel_info.append(data);
			})).done(function(resp){
				/* If there is nothing in panel info */
				if(!panel_info.is(':empty')){
					//If the sub navigation is found, then set the sub navigation as a grid
					if(sub_navigation.length){
						sub_navigation.addClass('grid').queue(function(){
							panel_info.show(250);
						});
					}
				} else {
					section_information.addClass('empty');
					panel_info.remove();
				}

				var widget_data = $('.ui-widget-header, .ui-widget-content');

				if(widget_data.length){
					var extra_spans = widget_data.find('> span');

					if(extra_spans.length){
						extra_spans.remove();
					}
				}
			});

			//this is used to load info from a page to a div
			//in this case we get the company info from from getCompanyInfo.php into div panelInfo
			// $.when(panel_info.load(url + '&ajax=1')).done(function(resp){
				
			// });				
		}
	});
})(jQuery);