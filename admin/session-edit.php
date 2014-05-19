<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/participant-email-model.php');
	require_once('models/users_model.php');

	/* Get user id */
	$user_id = 0;
	if(isset($_SESSION['MM_UserId']['user_id'])){
		$user_id = $_SESSION['MM_UserId'];
	}

	/* Get user type id */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){		
		/* Set session id */
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
		$main_script = 'session_observers';
		$other_content = 'session_observers';
		$grid = true;
		$validate = false;
		$inline_scripting = 'session_observers_inline';
		$page_help = 'sessions';
		
		//Only initialisation - if client company id is available, it is set further down
		$page = 'Sessions | Session Observers';
		$title = 'Sessions';
		
		$sub_id = null;
		$sub_group = null;
		$sub_navigation = null;

		$footer = 'session_footer';

		//retrieve user types
		mysql_select_db($database_ifs, $ifs);
		$query_retUserTypes = "SELECT * FROM client_user_types WHERE id NOT IN (1,2)";
		$retUserTypes = mysql_query($query_retUserTypes, $ifs) or die(mysql_error());
		//$row_retUserTypes = mysql_fetch_assoc($retUserTypes);
		$totalRows_retUserTypes = mysql_num_rows($retUserTypes);		

		//retrieve the session staff info 
		mysql_select_db($database_ifs, $ifs);
		$query_retSessionInfo = "
		SELECT 
		  sessions.name,
		  sessions.start_time,
		  sessions.end_time,
		  sessions.incentive_details,
		  sessions.brand_project_id,
		  sessions.status_id,
		  brand_projects.client_company_id,
		  brand_projects.name AS `brand_project_name`,
		  brand_projects.logo_url,
		  client_companies.max_number_of_observers,
		  client_companies.name AS `client_company_name`
		FROM
		  sessions
		  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
		  INNER JOIN client_users ON (client_users.client_company_id = brand_projects.client_company_id)
		  INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
		WHERE
		  sessions.id=$session_id
		  AND client_users.active = 1
		GROUP BY
			sessions.id
		";

		$retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error());
		$totalRows_retSessionInfo = 0;
		$row_retSessionInfo = array();

		$brand_project_id = NULL;
		$client_company_id = NULL;
		$max_number_of_observers = 0;
		$status_id = 2;

		$brand_project_name = 'New Brand Project';
		$client_company_name = 'New Client Company';
		$session_name = '';
		$brand_project_logo_url = '';

		$subtitle_found = false;

		if($retSessionInfo){
			$row_retSessionInfo = mysql_fetch_assoc($retSessionInfo);
			$totalRows_retSessionInfo = mysql_num_rows($retSessionInfo);

			$brand_project_id = $row_retSessionInfo['brand_project_id'];
			$client_company_id = $row_retSessionInfo['client_company_id'];	

			$max_number_of_observers=$row_retSessionInfo['max_number_of_observers'];

			$status_id = $row_retSessionInfo['status_id'];

			//Set Names
			$brand_project_name = $row_retSessionInfo['brand_project_name'];
			$client_company_name = $row_retSessionInfo['client_company_name'];
			$session_name = $row_retSessionInfo['name'];

			$brand_project_logo_url = $row_retSessionInfo['logo_url'];			
		}

		//Set variable that will allow display of brand project name and client company name
    if($client_company_id && $brand_project_id){
      $subtitle_found = true;
    }

    //Set session name as title
    if($session_name){
      $title = $session_name;
    }

		//retrieve the session moderator
		mysql_select_db($database_ifs, $ifs);
		$query_retSessionMod = "
		SELECT 
		  session_staff.user_id,
		  session_staff.session_id,
		  users.name_first,
		  users.name_last
		FROM
		  session_staff
		  INNER JOIN users ON (session_staff.user_id = users.id)
		 WHERE
		   session_staff.session_id=". $session_id ." 
		   AND session_staff.type_id = 2
		   AND session_staff.active = 1
		";
		$retSessionMod = mysql_query($query_retSessionMod, $ifs) or die(mysql_error());
		$totalRows_retSessionMod = 0;
		$row_retSessionMod = array();

		if($retSessionMod){
			$row_retSessionMod = mysql_fetch_assoc($retSessionMod);
			$totalRows_retSessionMod = mysql_num_rows($retSessionMod);
		}

		//retrieve the sessions users
		mysql_select_db($database_ifs, $ifs);
		$query_retSessionStaff = "
		SELECT 
		  sessions.name,
		  sessions.start_time,
		  sessions.end_time,
		  session_staff.id,
		  users.name_last,
		  users.name_first,
		  users.job_title,
		  users.mobile,
		  users.email,
		  users.id AS `user_id`,
		  client_user_types.name AS type_name
		FROM
		  sessions
		  INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
		  INNER JOIN users ON (session_staff.user_id = users.id)
		  INNER JOIN client_user_types ON (session_staff.type_id = client_user_types.id)
		WHERE
		   session_staff.type_id NOT IN (2,3) AND session_staff.session_id=$session_id
		   AND active = 1
		";

		$retSessionStaff = mysql_query($query_retSessionStaff, $ifs) or die(mysql_error());
		$totalRows_retSessionStaff = 0;

		if($retSessionStaff){
			$totalRows_retSessionStaff = mysql_num_rows($retSessionStaff);
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