<div class="table_heading">
  <h2>Potential Participants</h2>
</div>
<div class="items">The content is optimised for Internet Explorer 8+, Mozilla Firefox and Google Chrome.</div>
<?php if($user_type < 2): ?>
	<div id="add_item">
	  <a class="buttons darker edit" href="participant-edit.php?brand_project_id=<?php echo $brand_project_id;?>"><span class="icon create"></span>Add New Participant</a>
	  <a class="buttons darker" href="participantPanel-csv.php?brand_project_id=<?php echo $brand_project_id;?>"><span class="icon import"></span>Upload CSV</a> 
    <a class="buttons darker export" href="export-selected.php?brand_project_id=<?php echo $brand_project_id;?>"><span class="icon export"></span>Export all Participants</a>  

	  <div class="other_downloads">
	  	<p>Click <a href="download_file.php?file=upload/userfiles/sample.csv">here</a> to download the CSV template</p>
	  	<p>Click <a href="data_dictionary.php" id="dd">here</a> to view the Data Dictionary</p>
	  </div>	
	</div>
<?php endif; ?>