(function($){
	/* Update form if uploading video*/
	function update_email_video(){
		var self = $(this),
			form = $('form'),
			url = '..' + form.attr('action') + '&ajax=1',
			post_data = form.serialize(),
			self_name = self.attr('name'),
			self_container = self.parent(),
			uploader = self_container.find('.upload'),
			video_val = $('#email_video').val(),
			player_container = $("#player_container");

		//include submitted button's value
		post_data += '&' + self_name + '_x=1';

		$.post(url, post_data, function(data){
			var json = $.parseJSON(data);
			
			var message = 'The update of the template was not successful. Please try again or contact the administrator.';
			if(typeof json != 'undefined'){
				var notification = $('<div class="notification" />'),
						player_object = player_container.find('object');

				message = json.message;				
				notification.text(message);
	
				if(json.success){
					/* Remove previous video and use new one */
					player_object.remove();
					player_container.append('<a href="' + video_val + '" target="_blank" class="video_btn">Email Video</a>');

					var video_btn = player_container.find('a.video_btn');

					video_btn.createPlayer(true);

					video_btn.remove();
				}
			}

			if(uploader.length){
				self_container.append(notification);
			} else {
				self_container.prepend(notification);
			}

			var notif_output = $('.notification');

			/* Slide Down notification and then remove it */
			if(notif_output.length){
				notif_output.hide().slideDown(500);

				/* Remove notification after a certain time */
				setTimeout(function(){
					notif_output.slideUp(500).queue(function(){
						notif_output.remove();
						$(this).dequeue();
					})
				}, 5000);
			}
		});

		return false;
	}

	$(document).ready(function(){
		var collapsibles = $('fieldset .collapsible');

		//collapsibles.hide(); //collapse collapsibles

		$('input#btnVideo').on('click', update_email_video);

		$('.collapse_legend').on('click', function(){
			var self = $(this),
					collapsible_div = self.parent().find('div.collapsible');

			collapsible_div.toggle(500); //open collapsible

			return false;
		});
	});
})(jQuery);