<div class="table_heading">
  <div class="title">
  	<h2>Email Templates</h2>
  </div>
  <aside>
  	<div class="inner">
  		Please ensure you have added content to all email templates and saved them before proceeding.
  	</div>
  </aside>
</div>
<div class="table_wrap">
	<table class="normal_table" cellspacing="0" cellpadding="0">
	  <thead>             
	    <tr>           
				<th>Email Template</th>
	      <th>Template Status</th>
	      <th>Edit Template</th>
	    </tr>
	  </thead>
	  <tbody>
	  <?php
			while($row_retEmailTypes = mysql_fetch_assoc($retEmailTypes)):
				$email_type_id = $row_retEmailTypes['email_type_id'];
				
				//this is to check is a email msg have been saved for a email type for a session
				mysql_select_db($database_ifs, $ifs);
				$query_retSessionEmail = "
				SELECT 
				  session_emails.id
				FROM
				  session_emails
				WHERE
					session_emails.session_id=$session_id
					AND session_emails.email_type_id=$email_type_id				  
				";
				$retSessionEmail = mysql_query($query_retSessionEmail, $ifs) or die(mysql_error());
				$totalRows_retSessionEmail = 0;
				
				if($retSessionEmail){
					$totalRows_retSessionEmail = mysql_num_rows($retSessionEmail);
				}
				
				if($totalRows_retSessionEmail > 0){
					$email_image = "images/correct.png";	
				} else {
					$email_image = "images/cross.png";	
				}?>
	   		<tr>
	        <td class="template_name"><?php echo strtolower($row_retEmailTypes['name']);?></td>
	        <td><img src="<?php echo $email_image;?>" /></td>
	        <td><a title="Edit Email" class="editEmail1" href="participant-email-template-admin.php?session_id=<?php echo $session_id; ?>&email_type_id=<?php echo $email_type_id; ?>" alt=""><span class="ui-icon ui-icon-pencil"></span></a></td>
	  		</tr>
	    <?php endwhile;	
		?>
		</tbody>
	</table>
</div>