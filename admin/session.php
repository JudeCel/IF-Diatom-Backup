<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');

	$user_type = $_SESSION['MM_UserTypeId']; //get user Type
		
	//Page properties
	$page = 'Sessions';
	$title = $page;
	$main_script = 'session';
	$other_content = false;

	$grid = true;
	$validate = false;
	$inline_scripting = false;
	
	$sub_navigation = false;
	$sub_nav_url = false;
	$sub_id = null;
	$sub_group = null;
	$page_help = 'sessions';

	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
		require_once('views/home.php');
	} else {
		if(!$user_type){
			$_SESSION['notification'] = 'You are logged out, please login again.';
		} else {
			$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
		}
	
		if(!admin($database_ifs, $ifs)){
			$_SESSION['current_location'] = $form_action;
		}


		header("Location: index.php");
		die();
	}

