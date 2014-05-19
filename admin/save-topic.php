<?php
//data connection file
require_once('Connections/ifs.php');     require_once('core.php');


if(isset($_REQUEST['active_topic_id']))
{
	$active_topic_id=$_REQUEST['active_topic_id'];
}
else
{
	$active_topic_id='';
}


if(isset($_REQUEST['session_id']))
{
	$session_id=$_REQUEST['session_id'];
}
else
{
	$session_id='';
}


if(isset($_REQUEST['topic_id']))
{
	$topic_id=$_REQUEST['topic_id'];
}
else
{
	$topic_id='';	
}

if(isset($_REQUEST['topic_status_id']))
{
	if($_REQUEST['topic_status_id'] == 'true')	
		$topic_status_id=1;
	else
		$topic_status_id=2;

}

mysql_select_db($database_ifs, $ifs);
$created=date('Y-m-d H:i:s'); 

if($session_id)
{
		//we update type in client users table
		if($active_topic_id)
		{
			$updateSQL4 = sprintf("UPDATE sessions SET  active_topic_id='$active_topic_id', updated='$created' WHERE id= $session_id" );
			mysql_select_db($database_ifs, $ifs);
			$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());		
			

			//add the topic entry in the active logs table
			$insertSQL4 = sprintf("INSERT INTO topic_activity_logs  (session_id, topic_id, created) VALUES ($session_id, $active_topic_id, '$created') ");
			mysql_select_db($database_ifs, $ifs);
			$Result4 = mysql_query($insertSQL4, $ifs) or die(mysql_error());		
			
		}
		
		
}



if($topic_id)
{
	if($topic_status_id)
	{
		echo $topic_status_id;
		$updateSQL4 = sprintf("UPDATE topics SET  topic_status_id='$topic_status_id', updated='$created' WHERE id= $topic_id" );
		mysql_select_db($database_ifs, $ifs);
		$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());		
	}
}

mysql_close($ifs);

