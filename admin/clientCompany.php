<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');

	$user_type = $_SESSION['MM_UserTypeId']; //get user Type
		
	//Page properties
	$page = 'Companies';
	$title = $page;
	$main_script = 'client_company';
	$other_content = false;

	$grid = true;	
	$validate = false;
	$inline_scripting = false;
	$page_help = 'companies';
	
	$sub_navigation = false;
	$sub_nav_url = false;
	$sub_id = null;
	$sub_group = null;	

	if(admin($database_ifs, $ifs) && ($user_type == -1 || $user_type == 1)){
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

		mysql_close($ifs);

		header("Location: index.php");
		die();
	}

	mysql_close($ifs);

