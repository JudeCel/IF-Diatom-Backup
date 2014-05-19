
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

<body id="email">
	<!-- Container -->
	<div id="session_container">
		<div class="inner">
			<!-- Notification -->
			<?php if($updated): ?>
				<div class="notification">
					<?php 
						/* Display notification */
						$message = ($message ? $message : 'The update of the template was not successful. Please try again or contact the administrator.');
						if($update_value){
							switch($update_value){
								case 1:
									$message = 'The email template was updated successfully.';
								break;
								case 2:
									$message = 'The image was uploaded successfully';
								break;
							}							
						}
						echo $message;
					?>
				</div>
			<?php endif; ?>
			<form id="form1" name="form1" method="post" action="<?php echo $form_action; ?>" class="row-fluid" enctype="multipart/form-data">
				<!-- Side Functions -->
				<div id="sidebar" class="span6 content">

					<!-- Facilitator -->
					<fieldset id="faciliator" class="email_box">
						<legend class="collapse_legend">Facilitator <span class="corner"></span></legend>		
						<div class="collapsible">											
							<div class="inner">
								<?php if($facilitator_firstname): ?>
									<h2><?php echo $facilitator_firstname . ' ' . $facilitator_lastname; ?></h2>
									<p><?php echo $facilitator_email . ' | ' . $facilitator_phone; ?></p>
								<?php else: ?>
									<p>No faciliator selected.</p>
								<?php endif; ?>
							</div>
						</div>
					</fieldset>

					<!-- Main Image Upload -->
					<fieldset id="image_upload" class="email_box">
						<legend class="collapse_legend">Top Image Upload <span class="corner"></span></legend>						

						<div class="collapsible">
							<div class="inner">
								<div class="description">
									<p>
										This field allows you to upload an image to be used just under the logo. Please not that the image has to be larger than 516px * 153px to upload correctly.
									</p>
									<p>
										The text area allows you to enter a description in order to make the image user-friendly for screen readers.
									</p>
									<p>
										If an image is already uploaded, it can be deleted and a new one can be uploaded.
									</p>
								</div>
								<?php if(isset($row_retSessionEmail['email_image'])): ?>
									<img src="<?php echo $row_retSessionEmail['email_image']?>" class="image_top" alt="<?php echo $row_retSessionEmail['email_image_desc']?>" />
								<?php endif; ?>
							</div>						
							
							<textarea class="textbox" cols="100" rows="4" name="email_image_desc"><?php echo (isset($row_retSessionEmail['email_image_desc']) ? $row_retSessionEmail['email_image_desc'] : '')?></textarea>
							<input class="textbox upload file" type="file" value="" name="email_image" placeholder="Image Description" id="email_image" />
							<input type="image" src="/images/upload_btn.png" alt="Upload" name="btnImage" class="upload_btn" />
						</div>
					</fieldset>
					<!-- Greeting -->
					<fieldset id="video_player" class="email_box">
						<legend class="collapse_legend">Video Linking <span class="corner"></span></legend>
						
						<div class="collapsible">
							<div class="inner">
								<div class="description">
									<p>
										By linking a video to the e-mail, it is possible to link to a video from inside the e-mail.
										Please note that this will not embed a video in the e-mail, since this is not supported by the system.
									</p>
								</div>
								<?php if(isset($row_retSessionEmail['email_video']) && $row_retSessionEmail['email_video']): ?>
									<div id="player_container">
										<a href="<?php echo $row_retSessionEmail['email_video']; ?>" class="video_btn" id="video_1"><?php echo $brand_name . ' Email Video'; ?></a>
									</div>
								<?php endif; ?>
							</div>

							<input class="textbox upload" type="text" value="<?php echo (isset($row_retSessionEmail['email_video']) ? $row_retSessionEmail['email_video'] : '');?>" name="email_video" id="email_video" />
							<input type="image" src="/images/upload_btn.png" alt="Upload" name="btnVideo" class="upload_btn" id="btnImage" />
							</div>
					</fieldset>
				</div>

				<!-- Main Content -->
				<div id="main_content" class="span6 content">
					<!-- Greeting -->
					<fieldset id="greeting" class="email_box">
						<legend>Greeting <span class="corner"></span></legend>
						<input class="textbox" type="text" value="<?php echo (isset($row_retSessionEmail['greeting']) ? $row_retSessionEmail['greeting'] : '');?>" name="greeting" />
					</fieldset>
					<!-- Subject -->
					<fieldset id="subject" class="email_box">
						<legend>Subject <span class="corner"></span></legend>
						<input class="textbox" type="text" value="<?php echo (isset($row_retSessionEmail['subject']) ? $row_retSessionEmail['subject'] : '');?>" name="subject" />
					</fieldset>
					<!-- Details -->
					<fieldset id="details" class="email_box">
						<legend>Opening <span class="corner"></span></legend>
						<textarea class="textbox" cols="100" rows="4" name="email_message_top"><?php echo (isset($row_retSessionEmail['email_message_top']) ? $row_retSessionEmail['email_message_top'] : '');?></textarea>
						<?php if($details_num): ?>
							<label for="detail_time">Details</label>

							<?php 
								/* Set detail box */
								$detail_i = 0;
								foreach($details as $key=>$detail):									
									if(is_numeric($key)):
							?>
										<input class="textbox" type="text" value="<?php echo (isset($row_retSessionEmail['detail_' . ($detail_i + 1)]) ? $row_retSessionEmail['detail_' . ($detail_i + 1)] : $detail); ?>" name="detail_<?php echo ($detail_i + 1); ?>" />
							<?php						
									else:?>
										<textarea class="textbox" cols="100" rows="4" name="detail_<?php echo ($detail_i + 1); ?>"><?php echo (isset($row_retSessionEmail['detail_' . ($detail_i + 1)]) ? $row_retSessionEmail['detail_' . ($detail_i + 1)] : $detail); ?></textarea>
								<?php
									endif;
									$detail_i++;
								endforeach; 
							?>

						<?php endif; ?>
					</fieldset>
					
					<?php if($email_type_id != 3 && $email_type_id != 4 && $email_type_id != 6):?>
						<!-- Closing -->
						<fieldset id="closing" class="email_box">
							<legend>Closing <span class="corner"></span></legend>
							<textarea class="textbox" cols="100" rows="4" name="email_message_bottom"><?php echo (isset($row_retSessionEmail['email_message_bottom']) ? $row_retSessionEmail['email_message_bottom'] : '');?></textarea>
						</fieldset>
					<?php endif; ?>					

					<!-- Usable Tags -->
					<fieldset id="usable_tags" class="email_box">
						<legend class="collapse_legend">Usable Tags <span class="corner"></span></legend>
						<div class="collapsible">
							<div class="inner">
								<div class="description">
									<p>
										These tags can be used as placeholders within the template's content, which will be replaced 
										when the actual e-mail is set.
									</p>
									<p>
										Any word can be bolded or italicised by surrounding it with the proper tag as seen 
										in the example below.
									</p>
								</div>
								<ul>
									<li>First Name <span class="tag">[[First Name]]</span></li>
									<li>Last Name <span class="tag">[[Last Name]]</span></li>
									<li>Email <span class="tag">[[Email]]</span></li>
									<li>Start Date <span class="tag">[[Start Date]]</span></li>
									<li>End Date <span class="tag">[[End Date]]</span></li>
									<li>Login Link <span class="tag">[[Login Link]]</span></li>
									<li>Username <span class="tag">[[Username]]</span></li>
									<li>Password <span class="tag">[[Password]]</span></li>
									<li>Brand <span class="tag">[[Brand]]</span></li>
									<li>Bold <span class="tag">e.g. [b[First Name]]</span></li>
									<li class="last">Italic <span class="tag">e.g. [i[First Name]]</span></li>
								</ul>
								<!--<a href="#" title="Help" class="question"></a>-->
							</div>
						</div>
					</fieldset>					
				</div>

				<div id="email_buttons">
					<input type="image" src="/images/email_save.png" alt="Save" name="btnSubmit" />
					<input type="image" src="/images/email_preview.png" alt="Preview" name="btnPreview" />
				</div>

			</form>
		</div>
	</div>
 	<script type="text/javascript">
        function closeME() {
            //event.preventDefault();  //this has to be commented bcos the event thingie doesnt work in chrome/safari.
             return false;
			 
			 $('#signupForm').submit();
			 parent.$.fancybox.close();
        }
    </script>
    <script src="IFS/js/script.js"></script>
    <script src="js/email_template.js"></script>
</body>
</html>



