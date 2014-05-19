<?php
require_once('Connections/ifs.php');     
require_once('core.php');

/* Get user type id */
$user_type = null;
if(isset($_SESSION['MM_UserTypeId'])){
	$user_type = $_SESSION['MM_UserTypeId'];
}

if($user_type && $user_type > 2){
	mysql_close($ifs);

	header(sprintf("Location: %s", $updateGoTo));
	die();	
}

//Check if javascript set an ajax value
$ajax = false;
if(isset($_GET['ajax'])){
  $ajax_str = strip_tags(mysql_real_escape_string($_GET['ajax']));

  //Check if ajax string is a stamptime
  if(is_numeric($ajax_str) && strlen($ajax_str) == 10){
    $ajax = true;
  } else {
    $_SESSION['notification'] = 'Please enable javascript';
  }
}

if(!$ajax){
  mysql_close($ifs);

  header('Location: index.php');
  die();
}

if(isset($_GET['participant_id']))
{
	$participant_id_raw = strip_tags(mysql_real_escape_string($_GET['participant_id']));	
	
	$participant_id=explode(",",$participant_id_raw);
}
else
{
	$participant_id=-1;
}
if(isset($_GET['brand_project_id']))
{
	$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));	
}
	
	//loop through the participants array
	if(sizeof($participant_id) > 0)
	{
		for($i=0; $i<sizeof($participant_id);$i++)
		{

			//retrieve the sessions users
			mysql_select_db($database_ifs, $ifs);
			$query_retParticipantUserId = "
			SELECT 
			  participants.user_id
			FROM
			  participants
			WHERE
				participants.id=".$participant_id[$i]."
			";
			$retParticipantUserId = mysql_query($query_retParticipantUserId, $ifs) or die(mysql_error());
			$row_retParticipantUserId = mysql_fetch_assoc($retParticipantUserId);
			$totalRows_retParticipantUserId = mysql_num_rows($retParticipantUserId);
			
			
			
			$updateGoTo = "participantPanel.php?brand_project_id=".$brand_project_id;
			
			//delete session staff entry
					$insert3SQL = sprintf("DELETE from participants WHERE id=".$participant_id[$i]."");
					mysql_select_db($database_ifs, $ifs);
					$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
		}
	}

	mysql_close($ifs);

	header(sprintf("Location: %s", $updateGoTo));