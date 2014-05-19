<form action="<?php echo $form_action; ?>" method="POST">
  <fieldset class="last">
    <legend>Add Observer</legend>

    <?php if($totalRows_retObservers): ?>
      <div class="form_item">
        <label for="observers">Potential Observers</label>
        <select name="observers" id="observers">
        	<?php while($row_retObservers = mysql_fetch_assoc($retObservers)): ?>
  		      <option value="<?php echo $row_retObservers['user_id'];?>"><?php echo $row_retObservers['name_first']." ".$row_retObservers['name_last'];?></option>
          <?php endwhile; ?>
        </select>
      </div>    
    <?php else: ?>
      <div class="text"><p>No Available Client Users found.</p></div>
    <?php endif; ?>
  </fieldset>
  <footer>
    <?php if($totalRows_retObservers): ?>
      <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Add" />
    <?php endif; ?>
    <a class="buttons darker" href="clientCompanyUsers-insert.php?client_company_id=<?php echo $client_company_id; ?>&session_id=<?php echo $session_id; ?>&type_id=4">Register New Observer</a>
  </footer> 
</form>