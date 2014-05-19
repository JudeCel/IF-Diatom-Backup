<form id="signupForm" method="post" action="<?php echo $form_action; ?>">
  <fieldset class="last">
    <legend>Add Global Admin</legend> 

    <div class="form_item">
      <label for="user_login_id">Existing Admin</label>
      
        <select name="user_id" id="user_id">
          <?php while ($row_retMods = mysql_fetch_assoc($retMods)):?>
            <option value="<?php  echo $row_retMods['user_id']?>"><?php echo stripslashes(htmlspecialchars($row_retMods['name_first'])).' '.stripslashes(htmlspecialchars($row_retMods['name_last'])); ?></option>
          <?php endwhile; ?>
        </select>
      
    </div>								
  </fieldset>
  <footer>
    <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Add" />
    <a class="buttons darker" href="clientCompanyUsers-insert.php?client_company_id=<?php echo $client_company_id; ?>&type_id=1">Register New Global Admin</a>
  </footer> 
</form>