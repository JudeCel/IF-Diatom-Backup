<form name="profile_form" id="profile_form" method="post" action="profile.php">
	<fieldset id="profile_fieldset" class="last">
	  <div class="information_area">	
	  	<legend>Personal Data:</legend>

	  	<input type="hidden" name="gender" value="<?php echo $row_retClientUser['Gender'];?>" />
			
			<div class="form_item">
				<label for="name_first">First Name <span class="required">*</span></label>     
				<input id="name_first" name="name_first" value="<?php echo stripslashes(htmlspecialchars($row_retClientUser['name_first']));?>" type="text" 
				<?php echo (in_array('name_first', $fields) ? 'class="required"' : ''); ?>/>
			</div>

			<div class="form_item">
				<label for="name_last">Last Name <span class="required">*</span></label> 
				<input id="name_last" name="name_last"  value="<?php echo stripslashes(htmlspecialchars($row_retClientUser['name_last']));?>" type="text"
				<?php echo (in_array('name_last', $fields) ? 'class="required"' : ''); ?>/>
			</div>

			<div class="form_item">
				<label for="job_title">Job Title <span class="required">*</span></label> 
				<input id="job_title" name="job_title"  value="<?php echo stripslashes(htmlspecialchars($row_retClientUser['job_title']));?>" type="text" 
				<?php echo (in_array('job_title', $fields) ? 'class="required"' : ''); ?>/>
			</div>

			<div class="form_item">
				<label for="phone">Phone</label> 
				<input id="phone" name="phone"  value="<?php echo $row_retClientUser['phone'];?>" type="text" />
			</div>

			<div class="form_item">
				<label for="fax">Fax</label>
				<input id="fax" name="fax"  value="<?php echo $row_retClientUser['fax'];?>" type="text" />
			</div> 

			<div class="form_item">
				<label for="email">Email <span class="required">*</span></label>
				<input id="email" name="email" value="<?php echo $row_retClientUser['email'];?>" type="text"
				<?php echo (in_array('email', $fields) ? 'class="required"' : ''); ?>/>
			</div>

			<div class="form_item">
				<label for="mobile">Mobile <span class="required">*</span></label>
				<input id="mobile" name="mobile" value="<?php echo $row_retClientUser['mobile'];?>" type="text"
				<?php echo (in_array('mobile', $fields) ? 'class="required"' : ''); ?>/>
			</div>

			<div class="form_item">
				<label for="oldpassword">Old Password</label>
				<input id="oldpassword" name="oldpassword" value="" type="password"
				<?php echo (in_array('oldpassword', $fields) ? 'class="required"' : ''); ?>/>
			</div>

			<div class="form_item">
				<label for="newpassword">New Password</label>
				<input id="newpassword" name="newpassword" value="" type="password"
				<?php echo (in_array('newpassword', $fields) ? 'class="required"' : ''); ?>/>
			</div>

			<div class="form_item">
				<label for="confirmpassword">Confirm Password</label>
				<input id="confirmpassword" name="confirmpassword" value="" type="password"
				<?php echo (in_array('confirmpassword', $fields) ? 'class="required"' : ''); ?>/>
			</div>
		</div>

		<div class="submit_area">
			<div class="inner">
				<input type="submit" name="btnSubmit" id="profile_submit" class="buttons" value="Save Details" />
			</div>
		</div>
	</fieldset>
</form>