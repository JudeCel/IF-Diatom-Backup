<?php 
require_once('Connections/ifs.php');     
require_once('core.php');

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

//Check that the user does have access to the action and that the user id is set
if((!admin($database_ifs, $ifs) || $_SESSION['MM_UserTypeId'] > 3) || !$ajax){
  mysql_close($ifs);

  header('Location: users.php');

  die();
}


if(isset($_GET['topic_id']))
{
	$topic_id = strip_tags(mysql_real_escape_string($_GET['topic_id']));	
}
else
{
	$topic_id=-1;
}


//retrieve the sessions id
mysql_select_db($database_ifs, $ifs);
$query_retSessionID = "
SELECT 
  topics.session_id
FROM
  topics
WHERE
  topics.id = $topic_id
";
$retSessionID = mysql_query($query_retSessionID, $ifs) or die(mysql_error());
$row_retSessionID = mysql_fetch_assoc($retSessionID);
$totalRows_retSessionID = mysql_num_rows($retSessionID);



$updateGoTo = "newTopic.php?session_id=".$row_retSessionID['session_id'];

//delete session staff entry
		$delete_topic = sprintf("DELETE from topics WHERE id=$topic_id");
		mysql_select_db($database_ifs, $ifs);
		$Result = mysql_query($delete_topic, $ifs) or die(mysql_error());
		

	header(sprintf("Location: %s", $updateGoTo));

  mysql_close($ifs);	
