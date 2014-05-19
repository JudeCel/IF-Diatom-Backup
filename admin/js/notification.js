(function($){
	function delete_notification(){
		var delete_link_span = $('span.delete, span.ui-icon-delete');

		if(delete_link_span.length){
			var delete_link = delete_link_span.parent().filter(function(){
				if(!$(this).hasClass('confirm')){
					return true;
				} else {
					return false;
				}
			});

			/* Ensure that the item won't delete without notification */
			$('a.confirm').off('click');
			$('a.confirm').on('click', set_delete_link);

			//If links found
			if(delete_link.length){

				//Add class to delete links
				$.each(delete_link, function(){
					var self = $(this),
							url = self.attr('href'),
							unixtime = Math.round((new Date()).getTime() / 1000);

					//If this hasn't been processed before
					if(!self.hasClass('confirm') && typeof url !== 'undefined'){
						//Set ajax
						self.attr('href', url + '&ajax=' + unixtime);

						self.addClass('confirm'); //make sure that this functionality isn't duplicated
					}					
				});
			}
		}
	}

	function set_delete_link(){
		var link = $(this),
				dialog_box = $('<div id="#dialog_box" />');

		dialog_box.text('Are you sure you want to delete this item?');

		dialog_box.dialog({	
			height:150,
			modal:true,
			buttons:{
				'Cancel': function(){
					$(this).dialog('close');
				},
				'Confirm': function(){
					window.location = link.attr('href'); //perform_action
					$(this).dialog('close');					
				}
			}	
		});

		return false;
	}

	$(document).ready(function(){
		var notification = $('.notification'); //notification		

		if(notification.length && typeof no_notifications === 'undefined'){ //if the notification box was found
			/* Wait a couple of seconds */
			/*setTimeout(function(){
				//Slide up notification box
				notification.slideUp(500).queue(function(){
					notification.remove(); //remove notification box

					$(this).dequeue();
				});
			}, 5000);*/
		}

		/* Find Delete links */
		setInterval(delete_notification, 200);	

		var help_btn = $('a#help_btn');
		if(help_btn.length){
			help_btn.fancybox({
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe',
				'height' : 400,
				'autoScale' : 'false'		
			});
		}	
	});
})(jQuery);