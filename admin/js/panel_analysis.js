(function($){
	function save_analytics_image(event){
		var image_container = $('.jqplot-image-container'),
				image_data = image_container.find('img'),
				image_src = image_data.attr('src'),
				image_data_raw = image_src.substr(image_src.indexOf(',') + 1).toString(),
				active_tab = $('#tabs .ui-tabs-nav .ui-state-active a'),
				fake_form = $('<form name="imageform" />'),
				image_input = $('<input type="hidden" />').attr('name', 'image').val(image_data_raw),
				name_input = $('<input type="hidden" />').attr('name', 'image_name');

		name_input.val(active_tab.text() + ' Chart.png');

		image_container.hide(); //hide image container

		//Prepare form
		fake_form.attr('method', 'post');
		fake_form.attr('action', 'save_image.php');
		fake_form.append(image_input).append(name_input);

		image_data.remove(); //ensure that the right image is selected

		$('body').append(fake_form);

		fake_form.submit().queue(function(){
			fake_form.remove();
			$(this).dequeue();
		});		

		return false;
	}

	$(document).ready(function(){
		
		//Wait for all HTML to be processed
		setTimeout(function(){
			var save_image = $('.jqplot-image-button'); //Save button for analytics

			save_image.prepend('<span class="icon export" />');

			if(save_image.length){
				//Stop save image from running plugin code and just produce an image
				save_image.click(save_analytics_image);
			}
		}, 100);

	});
})(jQuery);