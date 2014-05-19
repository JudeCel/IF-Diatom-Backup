<?php 
	require_once('Connections/ifs.php'); 
	require_once('core.php');
	require_once('models/users_model.php');

	/* Get message information */
	$message = new StdClass;
	$message->other = array();

	if(isset($_SESSION['notification'])){
		$message->other = $_SESSION['notification'];

		unset($_SESSION['notification']);
	}

	//Page properties
	$page = 'Administrators';
	$title = 'Administrators';
	$main_script = 'users';
	$other_content = false;
	$page_help = 'admin';
	
	$grid = true;
	$validate = false;
	$inline_scripting = false;

	$sub_navigation = false;
	$sub_nav_url = false;
	$sub_id = null;
	$sub_group = null;

	if(!admin($database_ifs, $ifs) || ($_SESSION['MM_UserTypeId'] != 1 && $_SESSION['MM_UserTypeId'] != -1)){
		$_SESSION['notification'] = 'You are logged out, please login again.';
	
		if(!admin($database_ifs, $ifs)){
			$_SESSION['current_location'] = $form_action;
		}

		mysql_close($ifs);

		header('Location: index.php');
		die();
	} else {
		require_once('views/home.php');
	}

	//Set message
	if(!empty($message->other)){
		$message = process_messages($message);
	}

	mysql_close($ifs);
