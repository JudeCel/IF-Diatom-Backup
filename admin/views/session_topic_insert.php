<form id="signupForm" method="post" action="<?php echo $form_action; ?>">
	<fieldset class="last">
    <legend>New Topic</legend>
    
    <div class="form_item">
      <label for="topic_name">Topic Name:</label>
      <input id='topic_name' name="topic_name" value="<?php echo (isset($_POST['topic_name']) ? $_POST['topic_name'] : ''); ?>"
      <?php echo (in_array('topic_name', $fields) ? 'class="required"' : ''); ?> placeholder="Maximum 15 Characters" maxlength="15"/></td>
    </div>
  </fieldset>

  <footer>
    <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Add Topic" />
  </footer>  
</form>