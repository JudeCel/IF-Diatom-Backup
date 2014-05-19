(function($){
	$(document).ready(function(){
		$("#signupForm").validate({
			rules: {
				topic_name: "required"
			},
			messages: {
				name: "Please enter a topic name"
				
			}
		});
	});
});