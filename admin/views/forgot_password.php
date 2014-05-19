<form name="forgot_password" id="forgot_password" method="post">
	<fieldset class="last">
        <?php if(!isset($_POST['send_new'])): ?>
		<!-- <legend>Forgot password</legend> -->
        <?php endif ?>

		<?php if(!$email_array&&!(isset($_POST['send_new'])||isset($_GET['key']))): ?>
	  	<div class="form_item">
	    	<label for="email">Username/Email</label>
	    	<input type="email" id="email" name="email" value="<?php echo $username; ?>" />
	    </div>
	  <?php endif;

   	if(!empty($found_users->staff)): ?>
   		<fieldset>
   			<legend>Staff Activities Found</legend>
   			<div class="form_item">
   				<?php if(count($found_users->staff) > 1): ?>
     				<label for="staff_reset">Roles</label>
     				<select name="staff_reset" class="staff_reset" id="staff_reset">
     					<option value="none">None</option>
     					<?php foreach($found_users->staff as $ulid=>$type_id):
                /*  If Session Name is available */
                $type_output = $type_id;
                if(is_array($type_output)){
                  foreach($type_output as $type_id=>$session_name){
                    $select_output = $user_types[$type_id] . ' - ' . $session_name;
                  }
                } else { //Just display the role name
                  $select_output = $user_types[$type_id];
                }?>

                   <option value=<?php echo '"'.htmlspecialchars('{"id":'.$ulid.',"name":"'.addcslashes($select_output, '"').'"}', ENT_QUOTES).'"'; ?>><?php echo $select_output; ?></option>
     					<?php endforeach; ?>

     				</select>
     			<?php else:

     				foreach($found_users->staff as $ulid=>$type_id): ?>

       				<input type="checkbox" name="staff_reset" id="staff_reset_<?php echo $ulid; ?>" value=<?php echo '"'.htmlspecialchars('{"id":'.$ulid.',"name":"'.addcslashes($select_output, '"').'"}', ENT_QUOTES).'"'; ?> />

              <?php
                /*  If Session Name is available */
                $type_output = $type_id;
                if(is_array($type_output)){
                  foreach($type_output as $type_id=>$session_name){
                    $select_output = $user_types[$type_id] . ' - ' . $session_name;
                  }
                } else { //Just display the role name
                  $select_output = $user_types[$type_id];
                }
              ?>
              <label class="checkbox" for="staff_reset_<?php echo $ulid; ?>"><?php echo $select_output; ?></label>
       			<?php endforeach;

     			endif; ?>
   			</div>
   		</fieldset>
   	<?php endif;

   	if(!empty($found_users->observers)): ?>
   		<fieldset>
   			<legend>Observer Activities Found</legend>
   			<div class="form_item">
   				<?php if(count($found_users->observers) > 1): ?>
     				<label for="observer_reset">Sessions</label>
     				<select name="observer_reset" id="observer_reset" class="observer_reset">
     					<option value="none">None</option>
     					<?php foreach($found_users->observers as $ulid=>$type_id):
                /*  If Session Name is available */
                $type_output = $type_id;
                if(is_array($type_output)){
                  foreach($type_output as $type_id=>$session_name){
                    $select_output = $user_types[$type_id] . ' - ' . $session_name;
                  }
                } else { //Just display the role name
                  $select_output = $user_types[$type_id];
                }
                            ?>

                            <option value='"'.htmlspecialchars('{"id":'.$ulid.',"name":"'.addcslashes($select_output, '"').'"}', ENT_QUOTES).'"'; ?>"><?php echo $select_output; ?></option>
     					<?php endforeach; ?>

     				</select>
     			<?php else:
     				foreach($found_users->observers as $ulid=>$type_id):
              $type_output = $type_id;

              if(is_array($type_output)){
                foreach($type_output as $type_id=>$session_name){
                  $select_output = $user_types[$type_id] . ' - ' . $session_name;
                }
              } else { //Just display the role name
                $select_output = $user_types[$type_id];
              }?>

       				<input type="checkbox" name="observer_reset" id="observer_reset_<?php echo $ulid; ?>" value=<?php echo '"'.htmlspecialchars('{"id":'.$ulid.',"name":"'.addcslashes($select_output, '"').'"}', ENT_QUOTES).'"'; ?> />
       				<label for="observer_reset_<?php echo $ulid; ?>" class="checkbox"><?php echo $select_output; ?></label>
       			<?php endforeach;

     			endif; ?>
   			</div>
   		</fieldset>
   	<?php endif;

   	if(!empty($found_users->participants)): ?>
   		<fieldset>
   			<legend>Participant Activities Found</legend>
   			<div class="form_item">
   				<?php if(count($found_users->participants) > 1): ?>
     				<label for="participant_reset">Sessions</label>
     				<select name="participant_reset" class="participant_reset" id="participant_reset">
     					<option value="none">None</option>
     					<?php foreach($found_users->participants as $ulid=>$session_name): ?>
     						<option value=<?php echo '"'.htmlspecialchars('{"id":'.$ulid.',"name":"'.addcslashes($session_name, '"').'"}', ENT_QUOTES).'"'; ?></option>
     					<?php endforeach; ?>

     				</select>
     			<?php elseif(count($found_users->participants) == 1):

         			foreach($found_users->participants as $ulid=>$session_name): ?>
         				<input type="checkbox" name="participant_reset" id="participant_reset_<?php echo $ulid; ?>" value=<?php echo '"'.htmlspecialchars('{"id":'.$ulid.',"name":"'.addcslashes($session_name, '"').'"}', ENT_QUOTES).'"'; ?> />
         				<label for="participant_reset_<?php echo $ulid; ?>" class="checkbox"><?php echo $session_name; ?></label>
         			<?php endforeach;

         	endif; ?>
   			</div>
   		</fieldset>
   	<?php endif; ?>

		<div class="submit_area">
      <?php if((!$email_array || ($email_array && !$reset_found))&&!(isset($_POST['send_new'])||isset($_GET['key']))):?>
  			<input class="buttons darker" type="submit" name="btnSubmit" value="<?php echo ($reset_found ? 'Search Again' : 'Search for Activity'); ?>" />
  		<?php endif;

  		if($reset_found&&!(isset($_POST['send_new'])||isset($_GET['key']))): ?>
  			<input class="buttons darker" type="submit" name="send_new" value="Change Password" />
  		<?php endif; ?>

        <input class="buttons darker" type="submit" name="btnHome" value="Back To Homepage" />
    </div> 		
	</fieldset>
</form>	