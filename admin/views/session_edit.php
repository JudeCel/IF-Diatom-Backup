<form name="signupForm" id="signupForm" method="post" action="<?php echo $form_action; ?>">
	<fieldset class="last">
	  <legend>Edit Session</legend>
	             
    <div class="form_item">
      <label for="name">Session Name <span class="required"></label>
      <input id="name" name="name" value="<?php echo (isset($_POST['name']) && $_POST['name'] != $row_retSessionInfo['name'] ? htmlentities(mysql_real_escape_string($_POST['name'])) : stripslashes(htmlspecialchars($row_retSessionInfo['name']))); ?>"
      <?php echo (in_array('name', $fields) ? 'class="required"' : ''); ?> />
    </div>

    <div class="form_item">
      <label for="moderator_user_id">Session Facilitator</label>
      <select name="moderator_user_id" id="moderator_user_id">
        <?php while ($row_retMods = mysql_fetch_assoc($retMods)): ?>
        	<option<?php echo ($row_retSessionMod['user_id'] == $row_retMods['user_id'] ? " selected=\"selected\"" : '' ); ?> value="<?php  echo $row_retMods['user_id']?>"><?php echo $row_retMods['name_first'] . ' ' . $row_retMods['name_last']?></option>
      	<?php endwhile; ?>
      </select>
    </div>

    <div class="form_item">
      <label for="start_time">Start Date/Time:</label>
      <div class="data">
        <input id="start_time" name="start_time" value="<?php echo (isset($_POST['start_time']) && $_POST['start_time'] != $row_retSessionInfo['start_time'] ? htmlentities(mysql_real_escape_string($_POST['start_time'])) : date('d-m-Y', strtotime($row_retSessionInfo['start_time']))); ?>"
        <?php echo (in_array('start_time', $fields) ? 'class="required"' : ''); ?> />
        <p>Format: DD-MM-YYYY HH:MM (24 hours)</p>
      </div>
    </div>

    <div class="form_item">
      <label>End Date/Time:</label>
      <div class="data">
        <input id="end_time" name="end_time" value="<?php echo (isset($_POST['end_time']) && $_POST['end_time'] != $row_retSessionInfo['end_time'] ? htmlentities(mysql_real_escape_string($_POST['end_time'])) : date('d-m-Y', strtotime($row_retSessionInfo['end_time']))); ?>"
        <?php echo (in_array('end_time', $fields) ? 'class="required"' : ''); ?> />
        <p>Format: DD-MM-YYYY HH:MM (24 hours)</p>
      </div>
    </div>
  </fieldset>

  <footer>
    <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Save" />
  </footer>
</form>