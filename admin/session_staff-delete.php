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

  if(!$ajax || (!admin($database_ifs, $ifs) &&  $_SESSION['MM_UserTypeId'] > 1)){
    mysql_close($ifs);

    header('Location: index.php');
    die();
  }


if(isset($_GET['session_staff_id']))
{
	$session_staff_id = strip_tags(mysql_real_escape_string($_GET['session_staff_id']));	
}
else
{
	$session_staff_id = -1;
}


//retrieve the sessions users
mysql_select_db($database_ifs, $ifs);
$query_retSessionStaff = "
SELECT 
  sessions.id
FROM
  sessions
  INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
WHERE
  session_staff.id = $session_staff_id
";
$retSessionStaff = mysql_query($query_retSessionStaff, $ifs) or die(mysql_error());
$row_retSessionStaff = mysql_fetch_assoc($retSessionStaff);
$totalRows_retSessionStaff = mysql_num_rows($retSessionStaff);



$updateGoTo = "session-edit.php?session_id=".$row_retSessionStaff['id'];

//delete session staff entry
		$insert3SQL = sprintf("DELETE from session_staff WHERE id=$session_staff_id");
		mysql_select_db($database_ifs, $ifs);
		$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
		
  mysql_close($ifs);

	header(sprintf("Location: %s", $updateGoTo));	

