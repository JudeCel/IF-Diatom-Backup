<?php
	require_once("Connections/ifs.php");
	require_once('core.php');
	require_once('models/users_model.php');

	//Get user id
	$user_id = null;
	if(isset($_GET['user_id'])){
		$user_id = strip_tags(mysql_real_escape_string($_GET['user_id']));
	}

	$client_user_id = null;
	if(isset($_GET['client_user_id'])){
		$client_user_id = strip_tags(mysql_real_escape_string($_GET['client_user_id']));
	}


	/* Get brand project id */
	$brand_project_id = null;
	$goto = 'users.php';

	if(isset($_GET['brand_project_id'])){
		$goto = 'bp_observers.php?brand_project_id=' . strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
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

	//Check that the user does have access to the action and that the user id is set
	if((!admin($database_ifs, $ifs) || $_SESSION['MM_UserTypeId'] > 1) || !$user_id || !$ajax){
		mysql_close($ifs);

		header('Location: ' . $goto);

		die();
	}

	mysql_select_db($database_ifs, $ifs);

	//Delete user
	$deleted_user = delete_user($database_ifs, $ifs, $user_id, $client_user_id);

	//check if user is deleted
	$logout = false;

	//Get user's own user id
	$own_user_id = NULL;
	if(isset($_SESSION['MM_UserId']['user_id'])){
		$own_user_id = $_SESSION['MM_UserId'];
	}

	//User is deleted
	if($own_user_id == $user_id){
		$deleted_user->message[] = 'Your account has been deleted.';
		$logout = true;
	}

	//Set notification messages
	$_SESSION['notification'] = $deleted_user->message;

	mysql_close($ifs);

	if($logout){
		header("Location: logout.php");
	} else {
		header("Location: " . $goto);
	}

