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

		/* edit a session*/
	 	$("td a.editSession").fancybox({
			//'titlePosition'		: 'inside',
			'titleFormat': function() { 
				return $('td a.editSession').attr('alt'); 
			}, 
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'height' : 400,
			'onClosed': function() {
				parent.location.reload(true);
			}			
		});

	 	/* edit a session staff*/
	 	$("td a.editSessionStaff").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'height' : 400,
			'onClosed': function() {
				parent.location.reload(true);
			}
		});

		/* insert a session staff*/
	 	$("td a.insertSessionStaff").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'height' : 400,
			'onClosed': function() {
				parent.location.reload(true);
			}			
		});
		

		///// fancy box for adding user
		$('#add_item a').fancybox({		
			'transitionIn' : 'elastic',			
			'type': 'iframe',
			'autoScale': 'true',
			'height' : 700,
			'onClosed': function() {
				parent.location.reload(true);
			}			
		});
	});
})(jQuery);