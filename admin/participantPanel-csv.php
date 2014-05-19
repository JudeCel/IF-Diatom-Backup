<?php
	require_once('Connections/ifs.php');
	require_once('core.php');
	require_once('models/participant-email-model.php');

	/* Type of User */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	/* Ensure that the global admin user may acces the CSV upload */
	if($user_type != -1 && $user_type != 1){
		$_SESSION['notification'] = 'You do not have the permission to upload CSV files';

		header("Location: index.php");
		die();
	}

	//Brand Project id
	$brand_project_id = NULL;
	if(isset($_GET['brand_project_id'])){
		$brand_project_id =	strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
	}

	//Page properties
	$page = 'Participant Panel CSV Upload';
	$title = 'Upload Participants';
	$main_script = false;
	$other_content = 'participant_panel_csv';
	$validate = false;
	$inline_scripting = false;

	/* Upload CSV */
	if(isset($_POST['submit'])){
		$URL="upload/userfiles/";

		$_SESSION['notification'] = upload_csv($URL, $brand_project_id, $database_ifs, $ifs);				
		
		//close fancybox
		echo '<script type="text/javascript" src="js/fancybox_close.js" />';
	}

	require_once('views/popup.php');
	mysql_close($ifs);
