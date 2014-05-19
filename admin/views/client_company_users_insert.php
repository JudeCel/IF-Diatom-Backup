<form  name="signup_form" id="signup_form" method="post" action="<?php echo $form_action; ?>">
  <?php if(isset($import) && $import):
  	require_once($import_page);
	endif; ?>

  <fieldset class="last<?php echo ($import ? ' imported active' : '') ?> client_insert">
  	<legend><?php echo (isset($user_legend) ? $user_legend : 'Add User'); ?></legend>
		
		<div class="form_item">
			<label for="name_first">First Name <span class="required">*</span></label>     
			<input id="name_first" name="name_first" type="text" value="<?php echo stripslashes(htmlspecialchars($name_first)); ?>" 
			<?php echo (in_array('name_first', $fields) ? 'class="required"' : ''); ?>/>
		</div>

		<div class="form_item">
			<label for="name_last">Last Name <span class="required">*</span></label> 
			<input id="name_last" name="name_last" type="text"
						 value="<?php echo stripslashes(htmlspecialchars($name_last)); ?>"
			<?php echo (in_array('name_last', $fields) ? 'class="required"' : ''); ?>/>
		</div>

		<?php if(!isset($row_retClientUser['Gender']) && (!$type_id || ($type_id && $type_id != 4))): ?>
			<div class="form_item">
				<label for="gender">Gender <span class="required">*</span></label>
				<select id="gender" name="gender">
					<option value="Male"<?php echo ($gender && $gender == 'Male' ? ' selected="selected"' : ''); ?>>Male</option>
					<option value="Female"<?php echo ($gender && $gender == 'Female' ? ' selected="selected"' : ''); ?>>Female</option>
				</select>
			</div>
		<?php endif; ?>		

		<div class="form_item">
			<label for="job_title">Job Title <span class="required">*</span></label> 
			<input id="job_title" name="job_title" type="text" value="<?php echo stripslashes(htmlspecialchars($job_title)); ?>"
			<?php echo (in_array('job_title', $fields) ? 'class="required"' : ''); ?>/>
		</div>

		<div class="form_item">
			<label for="email">Email <span class="required">*</span></label>
			<input id="email" name="email" type="text" value="<?php echo $email; ?>"
			<?php echo (in_array('email', $fields) ? 'class="required"' : ''); ?>/>
		</div>


          <div class="form_item">
              <label for="uses_landline">Preferred Communication</label>
              <select id="uses_landline" name="uses_landline" onchange='$("#mobile").valid(); $("#phone").valid();'>
                  <option value=0 <?php echo (!$uses_landline ? ' selected="selected"' : ''); ?>>Mobile</option>
                  <option value=1 <?php echo ($uses_landline ? ' selected="selected"' : ''); ?>>Land line</option>
              </select>
          </div>

		<div class="form_item">
			<label for="mobile">Mobile</label>
			<input id="mobile" name="mobile" type="text" value="<?php echo $mobile; ?>"
			<?php echo (in_array('mobile', $fields) ? 'class="required"' : ''); ?>/>
		</div>

		<div class="form_item">
			<label for="phone">Phone</label> 
			<input id="phone" name="phone" type="text" value="<?php echo $phone; ?>" />
		</div>

		<div class="form_item">
			<label for="fax">Fax</label>
			<input id="fax" name="fax" type="text" value="<?php echo $fax; ?>" />
		</div>						
	</fieldset>
	<footer>
		<input class="buttons darker" name="btnSubmit" id="btnSubmit" value="<?php echo (isset($row_retClientUser) ? 'Update' : 'Register'); ?>" type="submit" />				
		<?php if(!isset($row_retClientUser) && $email_sent): ?>
			<aside>
				<div class="inner">
					Confirmation will be emailed.<br />
					Check spam folder.
				</div>
			</aside>
		<?php endif; ?>
	</footer>
</form>