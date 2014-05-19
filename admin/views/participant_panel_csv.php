<form action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data">
	<div class="form_item">
		<label for="csvFile">Upload CSV File</label>
		<input type="file" name="csvFile" id="csvFile" />
	</div>
	<footer>
    <div class="buttons darker">
      <input id="submit" name="submit" type="submit" value="Upload" />
    </div>
  </footer>
</form>