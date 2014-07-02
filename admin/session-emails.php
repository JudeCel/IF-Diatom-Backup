<?php 
	require_once('Connections/ifs.php');
 	require_once('core.php');

 	/* Set user type */
	$user_type = NULL;
	if($_SESSION['MM_UserTypeId']){
		$user_type = $_SESSION['MM_UserTypeId'];
	}
 
	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
		$user_id = 0;
		if(isset($_SESSION['MM_UserId']['user_id'])){
			$user_id = $_SESSION['MM_UserId'];
		}  elseif (isset($_SESSION['MM_UserId'])) { /* copied from IFS\index.php */
            $user_id = $_SESSION['MM_UserId'];
        }

		/* Get user type id */
		$user_type = null;
		if(isset($_SESSION['MM_UserTypeId'])){
			$user_type = $_SESSION['MM_UserTypeId'];
		}
	
		$session_id = null;
		if(isset($_GET['session_id'])){
			$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
		} else {
			$_SESSION['notification'] = 'An appropriate session hasn\'t been set';

			mysql_close($ifs);

			header('Location: session.php'); //return to main page
			die();
		}	

		
		//Page properties
		$main_script = 'session_emails';
		$other_content = 'session_emails';
		$grid = false;
		$validate = true;
		$inline_scripting = 'session_list_inline';
		
		//Only initialisation - if client company id is available, it is set further down
		$page = 'Sessions | Session Emails';
		$title = 'Sessions';
		$page_help = 'sessions';
		
		$sub_id = null;
		$sub_group = null;
		$sub_navigation = null;

		$footer = 'session_footer';

		//put the lookup code here	
		//get all the email types
		mysql_select_db($database_ifs, $ifs);
		$query_retEmailTypes = "
		SELECT 
		  email_types_lookup.name,
		  email_types_lookup.id AS email_type_id
		FROM
		  email_types_lookup
		";
		$retEmailTypes = mysql_query($query_retEmailTypes, $ifs) or die(mysql_error());
		$totalRows_retEmailTypes = 0;

		if($retEmailTypes){
			$totalRows_retEmailTypes = mysql_num_rows($retEmailTypes);
		}

		//retrieve the session info 
		mysql_select_db($database_ifs, $ifs);
		$query_retSessionInfo = "
		SELECT 
		  sessions.name,
		  sessions.status_id,
		  sessions.start_time,
		  sessions.end_time,
		  sessions.brand_project_id,
		  brand_projects.client_company_id,
		  brand_projects.name AS `brand_project_name`,
		  brand_projects.logo_url,
		  client_companies.max_number_of_observers,
		  client_companies.name AS `client_company_name`
		FROM
		  sessions
		  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
		  INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
		WHERE
		  sessions.id=$session_id
		";

		$retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error());
		$row_retSessionInfo = array();
		$totalRows_retSessionInfo = 0;

		$brand_project_id = NULL;
		$client_company_id = NULL;
		$status_id = 2;
		$brand_project_name = 'New Brand Project';
		$client_company_name = 'New Client Company Name';
		$session_name = ''; //session name
		$brand_project_logo_url = '';

		$subtitle_found = false;

		if($retSessionInfo){
			$row_retSessionInfo = mysql_fetch_assoc($retSessionInfo);
			$totalRows_retSessionInfo = mysql_num_rows($retSessionInfo);

			$brand_project_id = $row_retSessionInfo['brand_project_id'];
			$client_company_id = $row_retSessionInfo['client_company_id'];
			$status_id = $row_retSessionInfo['status_id'];

			//Set Names
			$brand_project_name = $row_retSessionInfo['brand_project_name'];

			$client_company_name = $row_retSessionInfo['client_company_name'];
			$session_name = $row_retSessionInfo['name'];

			//Logo
			$brand_project_logo_url = $row_retSessionInfo['logo_url'];			
		}

		//Set variable that will allow display of brand project name and client company name
		if($client_company_id && $brand_project_id){
 			$subtitle_found = true;
 		}

		//Set title as session name
		if($session_name){
			$title = $session_name;
		}			

		if(isset($_POST['btnSubmit'])){
			$created=date('Y-m-d H:i:s'); 				
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
