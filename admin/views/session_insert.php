<?php if(!$import): ?>
  <form id="signupForm" method="post" action="<?php echo $form_action; ?>">
<?php endif; ?>
  <div class="description">
    <p>
      If potential Facilitator is not on Existing Admin list,
      please Register New Facilitator before entering Session details.
      Then select from Existing Admin, complete Session details, Add. 
    </p>
  </div>

  <fieldset class="<?php echo (!$import ? 'last' : 'imported'); ?>">
    <legend><?php echo (isset($session_legend) ? $session_legend : 'Add Session'); ?></legend>

    <input id="max_session" name="max_session" type="hidden" value="<?php echo $totalRows_retSession; ?>" />

    <div class="form_item">
      <label for="name">Session Name <span class="required"></span></label>
      <input id="name" name="name" value="<?php echo (isset($_POST['name']) ? htmlentities(mysql_real_escape_string($_POST['name'])) : ''); ?>"
      <?php echo (in_array('name', $fields) ? 'class="required"' : ''); ?> /></td>
    </div>

    <?php if(!$import): ?>
      <div class="form_item">
        <label for="moderator_user_id">Existing Admin</label>
        <?php if($totalRows_retMods): ?>
          <select name="moderator_user_id" id="moderator_user_id">
            <?php while ($row_retMods = mysql_fetch_assoc($retMods)):?>
              <option value="<?php  echo $row_retMods['user_id']?>"<?php echo (isset($_POST['moderator_user_id']) && $_POST['moderator_user_id'] == $row_retMods['user_id'] ? ' selected="selected"' : ''); ?>>
                <?php echo $row_retMods['name_first'].' '.$row_retMods['name_last']?>
              </option>
            <?php endwhile; ?>
          </select>
        <?php else: ?>
          <div class="input_output">Please add a new facilitator before registering a new session.</div>
        <?php endif; ?>
      </div>
    <?php endif; ?>    

    <div class="form_item">
        <label for="start_date">Start Date/Time <span class="required"></span></label>
        <div class="data">
          <input id="start_date" name="start_date" value="<?php echo (isset($_POST['name']) ? htmlentities(mysql_real_escape_string($_POST['start_date'])) : ''); ?>"
          <?php echo (in_array('start_date', $fields) ? 'class="required"' : ''); ?> />
          <p>Format: DD-MM-YYYY HH:MM (24 hours)</p>
        </div>
    </div>
                            
    <div class="form_item">
      <label for="end_date">End Date/Time: <span class="required"></span></label>
      <div class="data">
        <input id="end_date" name="end_date" value="<?php echo (isset($_POST['name']) ? htmlentities(mysql_real_escape_string($_POST['end_date'])) : ''); ?>"
        <?php echo (in_array('end_date', $fields) ? 'class="required"' : ''); ?> />
        <p>Format: DD-MM-YYYY HH:MM (24 hours)</p>
      </div>
    </div>
  </fieldset>

  <?php if(!$import): ?>
    <footer>
      <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Add" />
      <a class="buttons darker" href="clientCompanyUsers-insert.php?client_company_id=<?php echo $client_company_id; ?>&type_id=2&brand_project_id=<?php echo $brand_project_id; ?>">Register New Facilitator</a>
    </footer> 
  <?php endif;

if(!$import): ?>
  </form>
<?php endif;