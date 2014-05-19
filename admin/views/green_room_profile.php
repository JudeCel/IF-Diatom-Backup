<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gte IE 9]> <html class="ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?php echo $session_name; ?> Green Room | Profile</title>

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/i/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <meta name="description" content="">
 	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">  

  <link rel="stylesheet" href="<?php echo ($environment ? $environment . '/' : ''); ?>css/screen_green.css" />
  <link rel="stylesheet" href="../css/misc.css" />
  <link rel="stylesheet" href="../boilerplate/css/style.css" />
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../bootstrap/css/bootstrap-responsive.css" />

  <link rel="stylesheet" type="text/css" href="../js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

  <script type="text/javascript">
  	var session_id = <?php echo $session_id; ?>,
  			user_id = <?php echo $user_id; ?>; 
  </script>

  <!-- All JavaScript at the bottom, except this Modernizr build.
       Modernizr enables HTML5 elements & feature detects for optimal performance.
       Create your own custom Modernizr build: www.modernizr.com/download/ -->
  <script src="../boilerplate/js/libs/modernizr-2.5.3.min.js"></script>
  <script type="text/javascript" src="http://chat.insiderfocus.com:1139/socket.io/socket.io.js"></script>
  
  <script type="text/javascript" src="<?php echo ($environment ? $environment . '/' : ''); ?>resources/raphael/raphael.js"></script>
  <script type="text/javascript" src="<?php echo ($environment ? $environment . '/' : ''); ?>js/utilities.js"></script>

  <script type="text/javascript" src="<?php echo ($environment ? $environment . '/' : ''); ?>js/avatarsAsSets.js"></script>
  <script type="text/javascript" src="<?php echo ($environment ? $environment . '/' : ''); ?>classes/avatarRenderer.js"></script>
  <script type="text/javascript" src="<?php echo ($environment ? $environment . '/' : ''); ?>classes/avatarChooser.js"></script>
  
</head>
<body id="greenroom">  
  <div id="session_container" class="container">
	  <div class="inner">
		  <header class="row-fluid">

		  	<div id="branding" class="span9">
		  		<div class="inner">
			  		<h1><?php echo $session_name; ?> Green Room</h1>

			  		<div class="buttons">
							<a href="<?php echo $x_close_url; ?>" class="close_btn" title="Exit Green Room"></a>
			  			<a href="../help.php?page=green_room" class="question" title="Help"></a>
			  		</div>

			  		<div class="info">
			  			<a href="index.php?session_id=<?php echo $session_id ?>">Return to Green Room</a>
			  			<a href="profile.php?session_id=<?php echo $session_id ?>">Update Your Profile</a>
			  			<a href="../Terms_and_Conditions.pdf" class="last" target="_blank">Terms and Conditions</a>
			  		</div>

			  	</div>
		  	</div>

		  	<div id="logo" class="span3">
		  		<a href="index.php?session_id=<?php echo $session_id ?>" title="Insiderfocus Logo">
		  			<img src="<?php echo ($brand_project_logo_url ? '../' . $brand_project_logo_url : '../images/logo.jpg'); ?>" alt="Insiderfocus logo" />
		  		</a>
		  	</div>
		  </header>

		  <div id="content" class="row-fluid" role="main">
		  	<div id="information" class="span6">
			  	<?php if($update_message && isset($message)):?>
			    	<?php echo $message; ?>
			 	 	<?php endif; ?>
			  	<div id="profile">
			  		<h2>Update Your Profile</h2>
			  		<form name="profile_form" id="profile_form" method="post" action="<?php echo $form_action; ?>">
					  	<fieldset>
					    	<legend><?php echo $full_name; ?>'s Profile</legend>
                
                <input id="job_title" name="job_title"  value="N/A" type="hidden" />
					  		
					  		<div class="form_item">
					  			<label for="name_first">First Name <span class="requried">*</span></label>     
									<input id="name_first" name="name_first" value="<?php echo stripslashes($green_room_row['name_first']);?>" type="text" 
									<?php echo (in_array('name_first', $fields) ? 'class="required"' : ''); ?>/>
								</div>

								<div class="form_item">
									<label for="name_last">Last Name <span class="requried">*</span></label> 
									<input id="name_last" name="name_last"  value="<?php echo stripslashes($green_room_row['name_last']);?>" type="text"
									<?php echo (in_array('name_last', $fields) ? 'class="required"' : ''); ?>/>
								</div>

								<div class="form_item half">
									<label for="phone">Phone</label> 
									<input id="phone" name="phone"  value="<?php echo $green_room_row['phone'];?>" type="text" />
								</div>

								<div class="form_item half last">
									<label for="mobile">Mobile <span class="requried">*</span></label>
									<input id="mobile" name="mobile" value="<?php echo $green_room_row['mobile'];?>" type="text"
									<?php echo (in_array('mobile', $fields) ? 'class="required"' : ''); ?>/>
								</div>

								<div class="form_item">
									<label for="fax">Fax</label>
									<input id="fax" name="fax"  value="<?php echo $green_room_row['fax'];?>" type="text" />
								</div> 

								<div class="form_item">
									<label for="email">Email <span class="requried">*</span>
                                        </label>
                                    If you change your email it will not change your username for the current session.
									<input id="email" name="email" value="<?php echo $green_room_row['email'];?>" type="text"
									<?php echo (in_array('email', $fields) ? 'class="required"' : ''); ?>/>
								</div>																

								<div class="form_item">
									<input type="submit" name="btnSubmit" id="btnSubmit" class="enter" value="Save" />
								</div>
							</fieldset>
						</form> 
			  	</div>
		    </div>

		    <div class="span6" id="identity">
		    	<div class="inner">

			    	<div class="block" id="avatar">
			    		<h2 class="legend">Customise Your Avatar <span class="corner"></span></h2>
			    	</div>

			    	<figure id="video_player" class="block last">
			    		<figcaption class="legend">Video Gallery <span class="corner"></span></figcaption>

			    		<div class="row-fluid">
				    		<div class="span12">
					    		<div class="row-fluid" id="player_container">			    		
						    		<a href="http://youtu.be/IdCByVloccU" class="video_btn span3" id="video_1"><span>Intro</span></a>
			    				</div>
		    				</div>
	    				</div>    
			    	</figure>

		    	</div>
		    </div>	  		
		  </div> <!-- Content -->
		</div> <!-- Inner -->
	</div> <!-- Container  -->

  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="/boilerplate/js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>

  <!-- Google's CDN's SWFObject -->
  <script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

  <!-- Handlebars -->
  <script src="<?php echo ($environment ? $environment . '/' : ''); ?>js/handlebars-1.0.0.beta.6.js"></script>

  <!-- scripts concatenated and minified via build script -->
  <script src="../boilerplate/js/plugins.js"></script>

  <script type="text/javascript" src="../js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
  <script type="text/javascript" src="../js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>

  <script src="<?php echo ($environment ? $environment . '/' : ''); ?>js/script.js"></script>
  <!-- end scripts -->
</body>
</html>
