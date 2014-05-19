(function($){
	var other_elements = new Array(),
			legends = new Array();

	/* Reveal hidden fields */
	function expand_hidden_form_items(){
		var self = $(this);

		if(other_elements.length){
			other_elements.slideDown(350);

			self.off('click', expand_hidden_form_items);

			/* Remove Ellipses */
			self.hide(250).queue(function(){
				self.remove();

				$(this).dequeue();
			});

			return false;			
		}
	}

	/* Expand Collapsible Fields */
	function expand_collapsible_fields(){
		var self = $(this),
				parent = self.parent(),
				collapse_area = parent.find('.collapse_area'),
				icon = parent.find('.collapse');

		if(collapse_area.length){
			collapse_area.slideToggle(250);
		}

		/* Switch classes to indicate open/close */
		if(icon.length){
			if(icon.hasClass('close')){
				icon.removeClass('close');
			} else {
				icon.addClass('close');
			}
		}
	}

	/* Add Ellipses to collapse certain fields */
	$.fn.add_ellipses = function(){
		var collapse_areas = $(this),
				ellipses = $('<a href="#" class="ellipses">...</a>');

		ellipses.attr('title', 'See All Details'); //Explain what it does
		ellipses.on('click', expand_hidden_form_items);

		collapse_areas.append(ellipses);

		other_elements.hide();
	}

	$(document).ready(function(){
		/* jQuery Objects */
		var imported_fieldset = $('fieldset.imported');

		//Set legends
		legends = imported_fieldset.find('legend');				

		/* If imported fieldset are available */
		if(imported_fieldset.length){
			/* Wrap in divs to collapse fieldset correctly */
			$.each(imported_fieldset, function(){
				var self = $(this);

				self.find('.form_item').wrapAll('<div class="collapse_area" />');
			})

			var inactive_fieldset = imported_fieldset.filter(function(){
				var self = $(this);

				return (!self.hasClass('active') ? true : false); 
			});

			/* use available inactive fieldsets  */
			if(inactive_fieldset.length){				
				var collapse_buttons = '<span class="ui-icon collapse"></span>',
						collapsable = false, //does it work as collapsable fields
						collapse_areas = inactive_fieldset.find('.collapse_area');

				other_elements = inactive_fieldset.find('div.form_item:not(:first)');
				
				legends.append(collapse_buttons);
				legends.css('cursor', 'pointer'); //make it clickable

				legends.on('click', expand_collapsible_fields); //Make legends clickable

				//Use all form items except the first one if they are avaialble
				if(other_elements.length){
					//collapse_areas.add_ellipses();
				}
			}
		}
	});
})(jQuery);