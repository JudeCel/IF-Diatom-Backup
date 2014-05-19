<form name="sms_form" id="sms_form" method="post" action="<?php echo $form_action; ?>">
	<div class="description">
    <p>Please ensure the mobile number is in this format:</p>
  	<p>
  		Country Code + Mobile Number, and delete the first 0 of the number.<br />
  		<strong>e.g. 61 + 04012345678 = 61412345678</strong>
  	</p>
  	<p>
  		To Edit, click on the number, make the change, and hit Enter to save.
  	</p>
  </div>

	<fieldset class="last">
    <legend>Send SMS</legend>   

    <div class="form_item">
	    <label for="message">Message</label>
	    <textarea name="message" cols="90" rows="3" id="message"><?php echo (isset($_POST['message']) ? $_POST['message'] : ''); ?></textarea>
	    <span id='count4'></span> characters  remaining.</span>
	  </div>

		<div class="table_wrap">
			<table class="normal_table">
		    <thead>
		      <tr>                           
		        <th>Select</th>
		        <th>Participant</th>
		        <th>Email</th>
		        <th>Mobile</th>
		      </tr>	                        
		    </thead>
		    <tbody>
		      <?php foreach($participants_found as $row_retSessionParticipant): ?>
		        <tr>
		            <td><input name="participants[]" type="checkbox" id="participants<?php echo $row_retSessionParticipant['part_id']; ?>" value="<?php echo $row_retSessionParticipant['mobile']; ?>">
		            <label for="participants<?php echo $row_retSessionParticipant['part_id']; ?>"></label></td>
		                 
		          	<td><?php echo $row_retSessionParticipant['name_first'].' '.$row_retSessionParticipant['name_last'];?></td>
		            <td><?php echo $row_retSessionParticipant['email']; ?></td>
		            <td><span id="mobile_edit<?php echo $row_retSessionParticipant['id'];?>" class="mouseover" style="display: inline"><?php echo $row_retSessionParticipant['mobile']; ?></span></td>
		         </tr>  
		       <?php endforeach; ?>
		    </tbody>
		  </table>
		</div>	  
	</fieldset>
	<footer>
	  <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Send SMS" />
	</footer>
</form>

