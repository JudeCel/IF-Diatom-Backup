<?php
//data connection file
require_once('Connections/ifs.php');     
require_once('core.php'); 
require_once('getCounts.php');
require_once('models/users_model.php');

if(isset($_REQUEST['participant_lists_id']) || isset($_REQUEST['participant_id'])){
	/* Set information according to data type */
	$participant_id = null;
	if(isset($_REQUEST['participant_lists_id'])){
		$participant_lists_id = $_REQUEST['participant_lists_id']; //participant list id
	} else {
		$participant_id = $_REQUEST['participant_id']; //participant id
	}

	mysql_select_db($database_ifs, $ifs);

	$created = date('Y-m-d H:i:s'); 
	
	if(!$participant_id){
		$participant_id = getValue($database_ifs, $ifs, $participant_lists_id,'participant_lists','participant_id');
	}	

	$user_id = null; //user id

	/* Find participant information */
	$participant_info = find_participant($database_ifs, $ifs, $participant_id);
	if($participant_info && !is_string($participant_info)){
		$participant_row = mysql_fetch_assoc($participant_info);

		$user_id = $participant_row['user_id']; //set user id
	}
	
	if($user_id){
		//we update type in client users table		
		$comments = FALSE;
		if(isset($_REQUEST['comments'])){
			$comments=$_REQUEST['comments'];
			
			$updateSQL4 = sprintf("UPDATE participant_lists SET  comments='$comments', updated='$created' WHERE id= $participant_lists_id" );
			mysql_select_db($database_ifs, $ifs);
			$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());		
		
		
			if($Result4){
				echo $comments;
			} else {
				echo "";
			}	
		}		
		
		$mobile = FALSE;
		if(isset($_REQUEST['mobile'])){
			$mobile=$_REQUEST['mobile'];
			$updateSQL4 = sprintf("UPDATE users SET  mobile='$mobile', updated='$created' WHERE id= $user_id" );
			mysql_select_db($database_ifs, $ifs);
			$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());		
		
			if($Result4){
				echo $mobile;
			} else {
				echo 0;	
			}
		}
	} else {
		echo '';
	}
} else {
	echo '';
}

mysql_close($ifs);
