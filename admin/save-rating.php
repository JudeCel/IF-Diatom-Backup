<?php
//data connection file
require_once('Connections/ifs.php');     require_once('core.php');

$participant_lists_id=($_REQUEST['participant_lists_id']);
if($_REQUEST['participant_rating_id'])
{
	$participant_rating_id=$_REQUEST['participant_rating_id'];
}


if(isset($_REQUEST['invite_again']) && $_REQUEST['invite_again'] == 'true')
{
	$invite_again='Yes';
}
else
{
	$invite_again='No';
}

if($participant_lists_id)
{
	mysql_select_db($database_ifs, $ifs);
	$created=date('Y-m-d H:i:s'); 
	
	//we update type in client users table
	
		if($participant_rating_id)
		{
			$updateSQL4 = sprintf("UPDATE participant_lists SET  participant_rating_id=$participant_rating_id, updated='$created' WHERE id= $participant_lists_id" );
			mysql_select_db($database_ifs, $ifs);
			$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());
		}
		
		if($invite_again)
		{
			$updateSQL4 = sprintf("UPDATE participant_lists SET  invite_again=$invite_again, updated='$created' WHERE id= $participant_lists_id" );
			mysql_select_db($database_ifs, $ifs);
			$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());		
		
		}

		if($Result4)
			echo "Successfully saved";
		else
			echo "Unsuccessfull";	
			
}

mysql_close($ifs);
