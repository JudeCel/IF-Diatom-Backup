
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Insider Focus</title>
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/misc.css" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="bootstrap/css/bootstrap.min.css" />

	<link rel="stylesheet" type="text/css" media="screen" href="css/custom-theme-green/jquery-ui-1.8.20.custom.css" />
	    
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="js/jquery.validate.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.8.17.custom.min.js" type="text/javascript"></script>
	
	<!-- Google's CDN's SWFObject -->
  	<script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

	<script type="text/javascript" src="js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>	
</head>

<body id="template_admin" >
	<!-- Container -->
	<div id="session_container">
		<div class="inner">
			<!-- Notification -->
			<?php if($updated): ?>
				<div class="notification">
					<?php 
						/* Display notification */
						$message = (isset($message) && $message ? $message : 'The update of the template was not successful. Please try again or contact the administrator.');
						if($update_value){
							switch($update_value){
								case 1:
									$message = 'The green template was updated successfully.';
								break;
								}							
						}
						echo $message;
					?>
				</div>
			<?php endif; ?>
			<form id="green_room_admin" name="green_room_admin" method="post" action="<?php echo $form_action; ?>" class="row-fluid" enctype="multipart/form-data">
				<!-- Main Content -->
				<div id="main_content" class="span12 content">
					<!-- Greeting-->
					<fieldset id="greeting" class="template_box full">
						<legend>Greeting <span class="corner"></span></legend>
						<input class="textbox" type="text" value="<?php echo (isset($green_room_session['greeting']) ? stripslashes(htmlspecialchars($green_room_session['greeting'])) : '');?>" name="greeting" />
					</fieldset>
					<!-- Overview -->
					<fieldset id="overview" class="template_box full">
						<legend>Overview <span class="corner"></span></legend>
						<textarea class="textbox" cols="100" rows="4" name="overview"><?php echo (isset($green_room_session['overview']) ? stripslashes(htmlspecialchars($green_room_session['overview'])) : NULL); ?></textarea>
					</fieldset>


					<!-- Usable Tags -->
					<fieldset id="usable_tags" class="template_box">
						<legend class="collapse_legend">Usable Tags <span class="corner"></span></legend>
						<div class="collapsible">
							<div class="inner">
								<div class="description">
									<p>
										These tags can be used as placeholders within the template's content, which will be replaced 
										when the actual green room is viewed.
									</p>
									<p>
										Any word can be bolded or italicised by surrounding it with the proper tag as seen 
										in the example below.
									</p>
								</div>
								<ul>
									<li>Participant First Name <span class="tag">[[Participant First Name]]</span></li>
									<li>Participant Last Name <span class="tag">[[Participant Last Name]]</span></li>
									<li>Session Name <span class="tag">[[Session Name]]</span></li>
									<li>Facilitator Name <span class="tag">[[Facilitator Name]]</span></li>
									<li>Facilitator Email <span class="tag">[[Email]]</span></li>
									<li>Facilitator Mobile <span class="tag">[[Mobile]]</span></li>
									<li>Bold <span class="tag">e.g. [b[Participant First Name]]</span></li>
									<li class="last">Italic <span class="tag">e.g. [i[Participant First Name]]</span></li>
								</ul>
								<!--<a href="#" title="Help" class="question"></a>-->
							</div>
						</div>
					</fieldset>
				</div>

				<div id="email_buttons">
					<input type="image" src="images/email_save.png" alt="Save" name="btnSubmit" />
					<input type="image" src="images/email_preview.png" alt="Preview" name="btnPreview" />
				</div>

			</form>
		</div>
	</div>
  <script src="IFS/js/script.js"></script>
  <script src="js/email_template.js"></script>
</body>
</html>



