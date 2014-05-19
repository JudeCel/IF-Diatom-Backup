<form id="signupForm" method="post" action="<?php echo $form_action; ?>">
	<fieldset class="last">
		<legend><?php echo ($participant_id ? 'Edit ' : ''); ?>Participant Details</legend>
		<input type="hidden" name="job_title" value="N/A" />

		<div class="form_item">
			<label for="name_first">First Name <span class="requried">*</span></label>     
			<input id="name_first" name="name_first" value="<?php echo (isset($row_retParticipantDetails['name_first']) && !isset($_POST['name_first']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['name_first'])) : $name_first);?>" type="text" 
			<?php echo (in_array('name_first', $fields) ? 'class="required"' : ''); ?>/>
		</div>

		<div class="form_item">
			<label for="name_last">Last Name <span class="requried">*</span></label> 
			<input id="name_last" name="name_last"  value="<?php echo (isset($row_retParticipantDetails['name_last']) && !isset($_POST['name_last']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['name_last'])) : $name_last);?>" type="text"
			<?php echo (in_array('name_last', $fields) ? 'class="required"' : ''); ?>/>
		</div>

		<div class="form_item">
			<label for="gender">Gender</label>
			<select id="gender" name="gender">
				<option value="Male" <?php echo ($gender == 'Male' ? 'selected="selected" ' : ''); ?>>Male</option>
				<option value="Female" <?php echo ($gender == 'Female' ? 'selected="selected" ' : ''); ?>>Female</option>
			</select>
		</div>	

		<div class="form_item">
			<label for="email">Email <span class="requried">*</span></label>
			<input id="email" name="email" value="<?php echo (isset($row_retParticipantDetails['email']) && !isset($_POST['email']) ? $row_retParticipantDetails['email'] : $email);?>" type="text"
			<?php echo (in_array('email', $fields) ? 'class="required"' : ''); ?>/>
		</div>

        <div class="form_item">
            <label for="uses_landline">Preferred Communication</label>
            <select id="uses_landline" name="uses_landline" onchange='$("#mobile").valid(); $("#phone").valid();'>
                <option value=0 <?php echo (!$uses_landline ? ' selected="selected"' : ''); ?>>Mobile</option>
                <option value=1 <?php echo ($uses_landline ? ' selected="selected"' : ''); ?>>Land line</option>
            </select>

        <div class="form_item">
			<label for="mobile">Mobile</label>
			<input id="mobile" name="mobile" value="<?php echo (isset($row_retParticipantDetails['mobile']) && !isset($_POST['mobile']) ? $row_retParticipantDetails['mobile'] : $mobile);?>" type="text"
			<?php echo (in_array('mobile', $fields) ? 'class="required"' : ''); ?> placeholder="CountryCode+Number-drop 0 eg.61403290248"/>
		</div>

		<div class="form_item">
			<label for="phone">Phone</label> 
			<input id="phone" name="phone"  value="<?php echo (isset($row_retParticipantDetails['phone']) && !isset($_POST['phone']) ? $row_retParticipantDetails['phone'] : $phone);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="fax">Fax</label>
			<input id="fax" name="fax"  value="<?php echo (isset($row_retParticipantDetails['fax']) && !isset($_POST['fax']) ? $row_retParticipantDetails['fax'] : $fax);?>" type="text" />
		</div>				

		<div class="form_item">
			<label for="street">Street</label>
			<input id="street" name="street" value="<?php echo (isset($row_retParticipantDetails['street']) && !isset($_POST['street']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['street'])) : $street);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="suburb">Suburb</label>
			<input id="suburb" name="suburb" value="<?php echo (isset($row_retParticipantDetails['suburb']) && !isset($_POST['suburb']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['suburb'])) : $suburb);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="state">State</label>
			<input id="state" name="state" value="<?php echo (isset($row_retParticipantDetails['state']) && !isset($_POST['state']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['state'])) : $state);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="postcode">Postcode</label>
			<input id="postcode" name="postcode" value="<?php echo (isset($row_retParticipantDetails['post_code']) && !isset($_POST['postcode']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['post_code'])) : $postcode);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="country">Country</label>					
			<select name="country">
	      <?php while ($row_retCountry = mysql_fetch_assoc($retCountry)):
					$set_country_id = $row_retCountry['id']; ?>
	        <option <?php echo ($country_id == $set_country_id ? "selected=\"selected\"" : ''); ?>  value="<?php echo $row_retCountry['id']?>"><?php echo stripslashes(htmlspecialchars($row_retCountry['country_name'])); ?></option>
	      <?php endwhile;

        /* Reset loop counter */
        if($totalRows_retCountry > 0):
          mysql_data_seek($retCountry, 0);                              
        endif; ?>                  
	    </select>
		</div>		

		<div class="form_item">
			<label for="dob">Age Value</label>
			<div class="data">
				<input id="dob" name="dob" value="<?php echo $dob; ?>" type="text" />
				<p>Format: 20-24</p>
			</div>
		</div>

		<div class="form_item">
			<label for="ethnicity">Ethnicity</label>
			<input id="ethnicity" name="ethnicity" value="<?php echo (isset($row_retParticipantDetails['ethnicity']) && !isset($_POST['ethnicity']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['ethnicity'])) : $ethnicity);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="occupation">Occupation</label>
			<input id="occupation" name="occupation" value="<?php echo (isset($row_retParticipantDetails['occupation']) && !isset($_POST['occupation']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['occupation'])) : $occupation);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="brand_segment">Brand Segment</label>
			<input id="brand_segment" name="brand_segment" value="<?php echo (isset($row_retParticipantDetails['brand_segment']) && !isset($_POST['brand_segment']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['brand_segment'])) : $brand_segment);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="optional1">Optional 1</label>
			<input id="optional1" name="optional1" value="<?php echo (isset($row_retParticipantDetails['optional1']) && !isset($_POST['optional1']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['optional1'])) : $optional1);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="optional1">Optional 2</label>
			<input id="optional2" name="optional2" value="<?php echo (isset($row_retParticipantDetails['optional2']) && !isset($_POST['optional2']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['optional2'])) : $optional2);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="optional3">Optional 3</label>
			<input id="optional3" name="optional3" value="<?php echo (isset($row_retParticipantDetails['optional3']) && !isset($_POST['optional3']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['optional3'])) : $optional3);?>" type="text" />
		</div>							

		<div class="form_item">
			<label for="optional4">Optional 4</label>
			<input id="optional4" name="optional4" value="<?php echo (isset($row_retParticipantDetails['optional4']) && !isset($_POST['optional4']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['optional4'])) : $optional4);?>" type="text" />
		</div>

		<div class="form_item">
			<label for="optional5">Optional 5</label>
			<input id="optional5" name="optional5" value="<?php echo (isset($row_retParticipantDetails['optional5']) && !isset($_POST['optional5']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['optional5'])) : $optional5);?>" type="text" />
		</div>

		<?php if($pl_id): ?>
			<div class="form_item">
				<label for="comments">Comments</label>
				<textarea id="comments" name="comments"><?php echo (isset($row_retParticipantDetails['comments']) && !isset($_POST['comments']) ? stripslashes(htmlspecialchars($row_retParticipantDetails['comments'])) : $comments);?></textarea>
			</div>
		<?php endif; ?>

	</fieldset>

	<footer>
    <input id="btnSubmit" name="btnSubmit" type="submit" value="<?php echo ($participant_id ? 'Update' : 'Add'); ?>" class="buttons darker" />
  </footer>
</form>	