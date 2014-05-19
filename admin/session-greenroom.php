<?php 
	require_once('Connections/ifs.php');
 	require_once('core.php');
 	require_once('models/green_room_model.php');
 	require_once('models/brand_model.php');

 	/* Get user type id */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}
 
	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){	
		//Page properties
		$main_script = 'session_emails';
		$other_content = 'session_greenroom';
		$grid = false;
		$validate = true;
		$inline_scripting = 'session_list_inline';
		
		//Only initialisation - if client company id is available, it is set further down
		$page = 'Sessions | Session Greenroom';
		$title = 'Sessions';
		$page_help = 'sessions';
		
		$sub_id = null;
		$sub_group = null;
		$sub_navigation = null;

		$footer = 'session_footer';

		if(isset($_GET['session_id'])){
			$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
		} else {
			$_SESSION['notification'] = 'An appropriate session hasn\'t been set';

			mysql_close($ifs);

			header('Location: session.php'); //return to main page
			die();		
		}

		$user_id = 0;
		if(isset($_SESSION['MM_UserId']['user_id'])){
			$user_id = $_SESSION['MM_UserId'];
		}

		//Brand Projects
		$brand_project_id = NULL;
		$client_company_id = NULL;
		$brand_project_name = 'New Brand Project';
		$client_company_name = 'New Client Company';
		$session_name = '';
		$status_id = 2;
		$brand_project_logo_url = '';
		$chatroom_logo_url = '';

		$subtitle_found = false;

		$brand_project_details = retrieve_brand_project($database_ifs, $ifs, $session_id);

		/* Get company client id and the brand project id */
		if($brand_project_details && !is_string($brand_project_details)){
			$brand_project_row = mysql_fetch_assoc($brand_project_details);
			
			$brand_project_id = $brand_project_row['id'];
			$client_company_id = $brand_project_row['client_company_id'];
			$status_id = $brand_project_row['status_id'];

			//Set Names
			$brand_project_name = $brand_project_row['brand_project_name'];
			$client_company_name = $brand_project_row['client_company_name'];
			$session_name = $brand_project_row['Session_Name'];
			$brand_project_logo_url = $brand_project_row['logo_url'];	

			$enable_chatroom_logo = $brand_project_row['enable_chatroom_logo'];
			$chatroom_logo_url = $brand_project_row['chatroom_logo_url'];	
		}

		//Set variable that will allow display of brand project name and client company name
		if($client_company_id && $brand_project_id){
 			$subtitle_found = true;
 		}

 		//Set session name as title
 		if($session_name){
 			$title = $session_name;
 		}

		//Get Room Details
		$green_room_details = retrieve_green_room_information($database_ifs, $ifs, $session_id);

		//Set e-mail image depending on whether the green template is avaialble or not
		if($green_room_details && !is_string($green_room_details)){
			$email_image = "images/correct.png";	
		} else {
			$email_image = "images/cross.png";	
		}

		//The sub navigation for the content
		if($session_id){
			$sub_group = 'sessions';
			$sub_navigation = array(
				'Emails' => 'session-emails.php?session_id=' . $session_id,
				'Green Room Template' => 'session-greenroom.php?session_id=' . $session_id,
				'Participants' => 'session-participants-jqgrid.php?session_id=' . $session_id,
				'Observers' => 'session-edit.php?session_id=' . $session_id,
				'Topics' => 'newTopic.php?session_id=' . $session_id
			);
			$sub_id = $session_id;
		}

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

		header('Location: index.php');
		die();
	}

	mysql_close($ifs);