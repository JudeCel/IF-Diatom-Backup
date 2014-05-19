(function($){
	$(document).ready(function(){
		/* edit a brand project*/
	 	$("td a.editBP").fancybox({
		  'titleFormat': function() { 
		 		return $('td a.editBP').attr('alt'); 
		 	}, 
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'autoScale':true,
			'height' : 500,
			'type'				: 'iframe',
		 	'onClosed': function() {
				parent.location.reload(true);
		  }		
		});

		//Ouput Add brand Project in a Fancybox
		$("#add_item a").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe',
			'height' : 400,
			'autoScale' : 'false',
			'onClosed': function() {
				parent.location.reload(true);
			}			
		});

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

		//confirmation dialog box for copying brand project
		$('.cloneBrandProject').click(function(){           
		  var id = this.id.replace(/[^0-9]/g, ""),
		  		clone_text = $('<div id="cloneText" />');

			//set text
		  clone_text.text('Are you sure you want to copy the brand project? This will copy all the sessions and topics as well.');
			
      clone_text.dialog({
        modal:true,
        resizable: false,
				autoOpen: true,
        buttons: {
            'Yes': function() {
                window.location ="clone-BP.php?brand_project_id=" + id + "&client_company_id=" + client_company_id;
                $(this).dialog('close');
            },
            Cancel: function() {
                $(this).dialog('close');
            }
        }
      });   
    });		
	});
})(jQuery);