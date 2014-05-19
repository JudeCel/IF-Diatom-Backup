(function($){
	var revert_table = null,
			revert_first = true;

	/* Revert the table back to its original form */
	function revert_to_form(){
		var view = $('.ui-jqgrid-view'),
				grids = $('.ui-jqgrid'),
				checkbox_parent = $('input.cbox').parent().filter(function(){
					return ($(this).attr('role') == 'gridcell');
				}),
				num = 0;
				
		view.each(function(){
			//If the view has an attribute of style
			if($(this).attr('style')){
				grids.remove_inline_styles();
				
				return false; //break
			}
			
			num++;
			//If it is the last item in the loop
			if(num == view.length && checkbox_parent.attr('style')){
				grids.remove_inline_styles();
			}				
		});
	}

	/* Combine the two tables */
	$.fn.combine_tables = function(key){
		var self = $(this),
				main_table = self.find('.ui-jqgrid-hdiv'),
				main_table_tbody = main_table.find('tbody'),						
				secondary_table = self.find('.ui-jqgrid-bdiv'),
				thead = self.find('.ui-jqgrid-htable'),
				tbody = self.find('.ui-jqgrid-btable'),
				tbody_html = $('<tbody />').append(tbody.find('tbody').html());				

		//Add the appropriate classes so grid can still act the same way
		secondary_table.addClass('hide');
		main_table.addClass('ui-jqgrid-bdiv');

		thead.append(tbody_html).addClass('ui-jqgrid-btable');

		thead.attr('id', tbody.attr('id'));

		//Remove id from tbody
		tbody.removeAttr('id');
	}

	/**
	* Remove all inline styles in the grid system
	*/
  $.fn.remove_inline_styles = function(){
		var items = this;

		//ensure that the grids have been loaded
		if(items.length){
			//Loop through items and remove inline styling
			$.each(items, function(key, grid){
				var grid_item = $(grid),
						grid_id = grid_item.attr('id'),
						grid_class = grid_item.attr('class'),
						grid_html = grid_item.html();

				//Get classes only html
				var classes_only = new Array(
					'ui-jqgrid-hdiv',
					'ui-jqgrid-bdiv',
					'ui-jqgrid-htable',
					'ui-pg-table',
					'ui-jqgrid-title',
					'ui-jqgrid-titlebar-close',
					'jqgfirstrow',
					'jqgrow',
					'ui-jqgrid-toppager'
				),
				replacements = {
		  		'ui-jqgrid-title': 'div'
		  	},
		  	containers = { //containers fo html without ids and classes
					'jqgfirstrow': 'td',
					'jqgrow': 'td'
				},
				ignore = new Array(
					'loading'
				),
				class_match = false,
				class_used = null,
				ignored = false;

				//Check if the html matches this item
				$.each(classes_only, function(){
					if(grid_item.hasClass(this)){
						class_match = true;
						class_used = this;
					}
				});

				//Check if this needs to be ignored
				$.each(ignored, function(){
					if(grid_item.hasClass(this)){
						ignored = true;
					}
				});

				//Replace any tags with the appropriate html
				grid_item.filter_grid_html(replacements);

				//If grid item has a style attribute
				if((grid_item.attr('style') || class_match) && !ignored){

					if(grid_id || class_match){
						var grid_reference = '',
								container_reference = '';
						
						if(class_used){ //if the grid id is empty
							grid_reference = '.' + class_used;
							
							container_reference = class_used;							
						} else {
							grid_reference = '#' + grid_id;
							
							container_reference = grid_id;
						}

						var container_found = false,
								html_tag_search = '';						

						//This is an container
						if(containers[class_used]){
							html_tag_search = containers[container_reference];
							container_found = true;
						}

						//clear styling
						if(class_match){
							$.each($(grid_reference), function(){
								var self = $(this);								

								//Check if this is an container
								if(container_found){
									var container_children = self.find(html_tag_search);

									//Go through the children and filter them as well
									container_children.process_container_children();
								}

								//Make sure that the item will only be processed if it has a style
								if(grid_item.attr('style')){
									self.process_grid_item();
								}							
							});
						} else { //id, so only 1 item
							$(grid_reference).process_grid_item();

							//Check if this is an container
							if(container_found){
								var container_children = $(grid_reference + ' ' + html_tag_search);

								//Go through the children and filter them as well
								container_children.process_container_children();
							}
						}						
					} else { //if an id or class cannot be found attemp to use the old reference
						grid_item.process_grid_item();
					}					
				}

				//Check that the HTML children does not match the HTML text
				if(grid_html && grid_item.text() != grid_html && grid_html != '&nbsp;'){
					grid_html = $(grid_html);

					//If the html item has children
					grid_html.remove_inline_styles(); //loop again
				}			

			});			
		}
  }

  /* Clear any style and re-apply hidden status if necessary */
  $.fn.process_grid_item = function(){
  	var item = this;
		
  	//If it is a tag
  	if(this[0]){
  		tag_name = this[0].tagName.toLowerCase();
  	}

  	//Check if hidden
		var hidden = false;
		if(item.css('display') == 'none'){
			hidden = true;
		}

		item.removeAttr('style'); //remove style attribute

		if(hidden && !item.hasClass('ui-jqgrid-titlebar-close')){
			item.hide();
		}
  }

  /* Filter the html in the grid to use specific html tags */
  $.fn.filter_grid_html = function(replacements){
  	var item = this;

  	//set the classes/ids that needs to be changed and the html tag they will be using 
		var item_id = item.attr('id'),
				item_class = item.attr('class');

  	$.each(replacements, function(reference, html_tag){
  		var found = false;

  		//Check that the class/id matches with the item's identification
  		if(item.hasClass(reference) || item_id == reference){
  			found = true, //an item that needs html tag replacement has been found
  			reference_item = '#' + reference;

  			//If a class was used to find the item
  			if(item.hasClass(reference)){
  				class_used = true;

  				reference_item = '.' + reference;
  			}
  		}

  		//If the tag was found
  		if(found){
  			var html_items_found = $(reference_item),
  					finalised_tag = '';  			

  			if(class_used){
	  			//a class, so there could be multiple
	  			$.each(html_items_found, function(){
	  				var self = $(this);
	  				
	  				item_class = self.attr('class');
	  						
	  				finalised_tag = '<' + html_tag + ' class="' + item_class + '"' + (item_id ? ' id="' + item_id + '"' : '') + '>';

	  				self.replaceWith($(finalised_tag + self.html() + '</' + html_tag + '>'));
	  			});
	  		} else {
	  			finalised_tag = '<' + html_tag + ' id="' + item_id + '"' + (item_class ? ' class="' + item_class + '"' : '') + '>';

	  			$(reference_item).replaceWith($(finalised_tag + reference_item.html() + '</' + html_tag + '>'));
	  		}
  		}
  	});
  }

  //Go through the children and filter them as well
  $.fn.process_container_children = function(){  	
		$.each(this, function(){
			var self = $(this),
					aria_description = self.attr('aria-describedby');

			self.process_grid_item();

			//add aria description as class if available
			if(aria_description){
				var classes = aria_description.split('_'),
						action_word = '';

				//Try to find the action word
				if(classes[2] !== undefined){
					action_word = classes[2];
				} else {
					action_word = classes[1];
				}

				action_word = action_word.replace(/\s/g, "");
				action_word = action_word.replace('/-/g', '_');

				self.addClass(action_word.toLowerCase());
			}
		});
  }

  $(document).ready(function(){
  	var grids = $('.ui-jqgrid');

  	//Change icon for opening/closing button
  	$('.ui-jqgrid-titlebar-close').on('click', function(){
  		var self = $(this);

  		self.toggleClass('collapsed');
  	});

  	//Remove styling and make sure it is not used after processesing
  	//revert_table = setInterval(revert_to_form, 320);
		$(document).ajaxSuccess(revert_to_form);
		$('button').live('mousedown', function(){
			setTimeout(revert_to_form, 350);
		});
		
  	var titlebar = $('.ui-jqgrid-titlebar'),
  			bar_visible = true;

  	/* Check if titlebar is visible */
  	if(grids.length){
  		if(titlebar.length && titlebar.is(':hidden')){
	  		grids.addClass('prepared');
	  		bar_visible = false;
	  	}
  	}

  	/* Overwrite width of grids */
		setTimeout(function(){
			//Add body of table to head
			$.each(grids, function(key){
				var self = $(this);

				self.combine_tables(key);

				//Make sure that the correct icon is used for the table's collapsed state
				if(grids.length > 1 && key && bar_visible){
					self.find('.ui-jqgrid-titlebar-close').addClass('collapsed');
				}
			}).promise().done(function(){
				if(bar_visible){
					$('a.HeaderButton').attr('href', '#');
					
					//Make grids visible				
					grids.addClass('prepared', 450);
				}
			});
						
		}, 350);		
  });
})(jQuery);