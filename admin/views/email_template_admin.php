
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

<body id="template_admin">
	<!-- Container -->
	<div id="session_container">
		<div class="inner">
			<!-- Notification -->
			<?php if($updated || $message): ?>
				<div class="notification">
					<?php 
						/* Display notification */
						if(($updated && $message) || (!$updated && $nessage)){
							$message = ($message ? $message : 'The update of the template was not successful. Please try again or contact the administrator.');
						}

						if($update_value){
							switch($update_value){
								case 1:
									$update_message = 'The email template was updated successfully.';
								break;
								case 2:
									$update_message = 'The image was uploaded successfully';
								break;
							}
							
							$message = (isset($update_message) ? '<p>' . $update_message . '</p>' . "\n" : '') . $message; 							
						}
						echo $message;
					?>
				</div>
			<?php endif; ?>
			<form id="email_form" name="email_form" method="post" action="<?php echo $form_action; ?>" class="row-fluid" enctype="multipart/form-data">
				<!-- Side Functions -->
				<div id="sidebar" class="span6 content">

					<!-- Facilitator -->
					<fieldset id="faciliator" class="template_box">
						<legend class="collapse_legend">Facilitator <span class="corner"></span></legend>		
						<div class="collapsible">											
							<div class="inner">
								<?php if($facilitator_firstname): ?>
									<h2><?php echo stripslashes(htmlspecialchars($facilitator_firstname)) . ' ' . stripslashes(htmlspecialchars($facilitator_lastname)); ?></h2>
									<p><?php echo $facilitator_email . ' | ' . $facilitator_phone; ?></p>
								<?php else: ?>
									<p>No faciliator selected.</p>
								<?php endif; ?>
							</div>
						</div>
					</fieldset>

					<!-- Main Image Upload -->
					<fieldset id="image_upload" class="template_box">
						<legend class="collapse_legend">Top Image Upload <span class="corner"></span></legend>						

						<div class="collapsible">
							<div class="inner">
								<div class="description">
									<p>
										This field allows you to upload an image to be used just under the logo. Please note that the image has to be larger than 516px * 153px to upload correctly.
									</p>
									<p>
										The text area allows you to enter a description in order to make the image user-friendly for screen readers.
									</p>
									<p>
										You can change an existing image. Browse for another image, then press Upload to replace with the new one.
									</p>
								</div>
								<?php if(isset($row_retSessionEmail['email_image'])): ?>
									<img src="<?php echo $row_retSessionEmail['email_image']?>" class="image_top" alt="<?php echo $row_retSessionEmail['email_image_desc']?>" />
								<?php endif; ?>
							</div>						
							
							<textarea class="textbox" cols="100" rows="4" name="email_image_desc"><?php echo (isset($row_retSessionEmail['email_image_desc']) ? stripslashes(htmlspecialchars($row_retSessionEmail['email_image_desc'])) : '')?></textarea>
							<input class="textbox upload file<?php echo (!$row_retSessionEmail ? ' full' : ''); ?>" type="file" value="" name="email_image" placeholder="Image Description" id="email_image" />
							<?php if($row_retSessionEmail): ?>
								<input type="image" src="images/upload_btn.png" alt="Upload" name="btnImage" class="upload_btn" />
							<?php endif; ?>
						</div>
					</fieldset>
					<!-- Greeting -->
					<fieldset id="video_player" class="template_box">
						<legend class="collapse_legend">Video Linking <span class="corner"></span></legend>
						
						<div class="collapsible">
							<div class="inner">
								<div class="description">
									<p>
										By linking a video to the e-mail, it is possible to link to a video from inside the e-mail.
										Please note that this will not directly embed a video in the e-mail, since this is not supported by the system.
										It is only a link to a Youtube video.
									</p>
								</div>
								<?php if(isset($row_retSessionEmail['email_video']) && $row_retSessionEmail['email_video']): ?>
									<div id="player_container">
										<a href="<?php echo $row_retSessionEmail['email_video']; ?>" class="video_btn" id="video_1"><?php echo $brand_name . ' Email Video'; ?></a>
									</div>
								<?php endif; ?>
							</div>

							<input class="textbox upload<?php echo (!$row_retSessionEmail ? ' full' : ''); ?>" type="text" value="<?php echo (isset($row_retSessionEmail['email_video']) ? $row_retSessionEmail['email_video'] : '');?>" name="email_video" id="email_video" />
							<?php if($row_retSessionEmail): ?>
								<input type="image" src="images/upload_btn.png" alt="Upload" name="btnVideo" class="upload_btn" id="btnVideo" />
							<?php endif; ?>
						</div>
					</fieldset>
				</div>

				<!-- Main Content -->
				<div id="main_content" class="span6 content">
					<!-- Greeting -->
					<fieldset id="greeting" class="template_box">
						<legend>Greeting <span class="corner"></span></legend>
						<input class="textbox" type="text" value="<?php echo (isset($row_retSessionEmail['greeting']) ? stripslashes(htmlspecialchars($row_retSessionEmail['greeting'])) : '');?>" name="greeting" />
					</fieldset>
					<!-- Subject -->
					<fieldset id="subject" class="template_box">
						<legend>Subject <span class="corner"></span></legend>
						<input class="textbox" type="text" value="<?php echo (isset($row_retSessionEmail['subject']) ? stripslashes(htmlspecialchars($row_retSessionEmail['subject'])) : '');?>" name="subject" />
					</fieldset>
					<!-- Details -->
					<fieldset id="details" class="template_box">
						<legend>Opening <span class="corner"></span></legend>
						<textarea class="textbox" cols="100" rows="4" name="email_message_top"><?php echo (isset($row_retSessionEmail['email_message_top']) ? stripslashes(htmlspecialchars($row_retSessionEmail['email_message_top'])) : '');?></textarea>
						<?php if($details_num): ?>
							<h3>Details</h3>

							<?php 
								/* Set detail box */
								$detail_i = 0;
								foreach($details as $key=>$detail): ?>									
									<label for="detail_<?php echo ($detail_i + 1); ?>">Detail <?php echo ($detail_i + 1); ?></label>
									<textarea class="textbox" cols="100" rows="2" name="detail_<?php echo ($detail_i + 1); ?>"><?php echo (isset($row_retSessionEmail['detail_' . ($detail_i + 1)]) ? stripslashes(htmlspecialchars($row_retSessionEmail['detail_' . ($detail_i + 1)])) : $detail); ?></textarea>
										
								<?php

									$detail_i++;
								endforeach; 
							?>

						<?php endif; ?>
					</fieldset>
					
					<?php if($email_type_id != 3 && $email_type_id != 4 && $email_type_id != 6):?>
						<!-- Closing -->
						<fieldset id="closing" class="template_box">
							<legend>Closing <span class="corner"></span></legend>
							<textarea class="textbox" cols="100" rows="4" name="email_message_bottom"><?php echo (isset($row_retSessionEmail['email_message_bottom']) ? stripslashes(htmlspecialchars($row_retSessionEmail['email_message_bottom'])) : '');?></textarea>
						</fieldset>
					<?php endif; ?>					

					<!-- Usable Tags -->
					<fieldset id="usable_tags" class="template_box">
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
					<input type="image" src="images/email_save.png" alt="Save" name="btnSubmit" />
					<input type="image" src="images/email_preview.png" alt="Preview" name="btnPreview" />
				</div>

			</form>
		</div>
	</div>
  <script src="IFS/production/js/script.js"></script>
  <script src="js/email_template.js"></script>
</body>
</html>



