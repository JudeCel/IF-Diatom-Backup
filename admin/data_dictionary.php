<?php
	require_once('Connections/ifs.php');
	require_once('core.php');

	/* Type of User */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	//Ensure that only the Global Admin or IFS Admin can access this
	if(admin($database_ifs, $ifs) && $user_type == -1 || $user_type == 1){
		//Page properties
		$page = 'Data Dictionary';

		$title = $page;
		$main_script = 'data_dictionary';
		$other_content = 'data_dictionary';
		$validate = false;
		$inline_scripting = 'data_dictionary_inline';

		require_once('views/popup.php');
	} else {
		if(!$user_type){
			$_SESSION['notification'] = 'You are logged out, please login again.';
		} else {
			$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
		}

		if(!admin($database_ifs, $ifs)){
			$_SESSION['current_location'] = $form_action;
		}

		header('Location: index.php');
		die();
	}

	