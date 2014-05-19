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

if(isset($_GET['observer_id']))
{
	$observer_id_raw = strip_tags(mysql_real_escape_string($_GET['observer_id']));	
	
	$observer_id = explode(",", $observer_id_raw);
}
else
{
	$observer_id = -1;
}

$brand_project_id = null;
if(isset($_GET['brand_project_id'])){
	$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));	
}

$updateGoTo = 'brand_project.php';
if($brand_project_id){
	$updateGoTo = "bp_observers.php?brand_project_id=".$brand_project_id;
}
	
//loop through the observer array
if(sizeof($observer_id) > 0){
	for($i=0; $i < sizeof($observer_id); $i++){		
		//Clear observer satus
		$update_sql = sprintf(
			"DELETE 
				FROM client_users 
			WHERE 
				user_id = %d",
			$observer_id[$i]
		);

		mysql_select_db($database_ifs, $ifs);
		$Result3 = mysql_query($update_sql, $ifs) or die(mysql_error());
	}
}

mysql_close($ifs);

header(sprintf("Location: %s", $updateGoTo));	
