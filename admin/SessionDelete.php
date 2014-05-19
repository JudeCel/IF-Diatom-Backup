<?php
	require_once("Connections/ifs.php");
	require('core.php');
	require_once('models/brand_model.php');

	/* Get session id */
	$session_id = null;
	if(isset($_GET['session_id'])){
		$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
	}

	/* Get brand project id */
	$brand_project_id = null;
	if(isset($_GET['brand_project_id'])){
		$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
	}

	//Check if user needs to return to the panel
	$panel = false;
	if(isset($_GET['panel'])){
		$panel = true;
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

	//Make sure the user is supposed to have access
	if((!admin($database_ifs, $ifs) || $_SESSION['MM_UserTypeId'] != -1) || !$session_id || !$ajax){
		mysql_close($ifs);

		//Redirect
		if($brand_project_id){
			if(!$panel){
				header("Location: newSession.php?brand_project_id=" . $brand_project_id);
			} else {
				header("Location: session.php"); //go to main panel
			}
		} else {
			header("Location: index.php");
		}

		die();		
	}

	mysql_select_db($database_ifs, $ifs);

	//query to delete brand project id
	$delete_sql = sprintf(
		"DELETE	FROM
			sessions
		WHERE
			id = %d",
		$session_id
	);

	$result = mysql_query($delete_sql, $ifs);

	if(!$result){
		$_SESSION['notification'] = 'The session could not be deleted';
	}

	if($brand_project_id){
		mysql_close($ifs);

		if(!$panel){
			header("Location: newSession.php?brand_project_id=" . $brand_project_id);
		} else {
			header("Location: session.php"); //go to main panel
		}

		die();
	} else {
		mysql_close($ifs);

		header("Location: session.php"); //go to main panel

		die();
	}

	mysql_close($ifs);