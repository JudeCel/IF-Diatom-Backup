<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/participant-email-model.php');
	require_once('models/users_model.php');

	$message = new stdClass;
	$message->other = array();
	$update = FALSE;
	$redirection = FALSE;

	/* Get user id */
	$user_id = 0;
	$loop = false;	

	if(isset($_SESSION['MM_UserId']['user_id']) && !isset($_GET['user_id'])){
		$user_id = $_SESSION['MM_UserId'];
	} elseif(isset($_GET['user_id'])){
		$user_id = strip_tags(mysql_real_escape_string($_GET['user_id']));
		$user_id = explode(',', $user_id);

		/* Check if update should be looped */		
		if(is_array($user_id)){
			$loop = true;

			/* Ensure that same variable (user_id) can still be used */
			$uid = $user_id;
			$user_id = 0;
		}

		$update = TRUE; //the user was passed along
		$redirection = TRUE;	
	}

	/* Get user type id */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	if(isset($_GET['session_id']) && admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
		$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));

		$redirect_path = sprintf('session-edit.php?session_id=%d', $session_id);	

		$page = 'Add Observer';
		$title = $page;
		$main_script = 'session_observers_insert';
		$other_content = 'session_observers_insert';
		$validate = false;
		$inline_scripting = 'session_observers_insert_inline';

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

			//Set variable that will allow display of brand project name and client company name
      if($client_company_id && $brand_project_id){
        $subtitle_found = true;
      }
		}

		/* Redirect to true if it isn't specified that it should return to parent page */
		if($redirection && !isset($_GET['return'])){
			$redirect_path = sprintf("clientCompanyUsers-insert.php?client_company_id=%d&update=1&type_id=4", $client_company_id);			
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

		//retrieve remaining users as observers  
		mysql_select_db($database_ifs, $ifs);
		$query_retObservers = "SELECT 
		  client_users.user_id,	
		  users.name_first,
		  users.name_last
		FROM
		  client_users
		  INNER JOIN users ON (client_users.user_id = users.id)
		WHERE
		   client_users.client_company_id=$client_company_id
		   AND type_id=4 AND bpid=$brand_project_id";

		$retObservers = mysql_query($query_retObservers, $ifs) or die(mysql_error());
		$totalRows_retObservers = 0;

		if($retObservers){
			$totalRows_retObservers = mysql_num_rows($retObservers);
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

		//Set Max Observers
		$max_observers = $max_number_of_observers - $totalRows_retSessionStaff;

		$type_id = 4;
		$created = date('Y-m-d H:i:s'); 

		//do the inserts
		if(isset($_POST['btnSubmit'])){			
			$user_id = strip_tags(mysql_real_escape_string($_POST['observers']));			
			$update = true;		
		}

		/* Perform the update */
		if($update){
			if(!$loop){
				include('selected-session-observers.php');
			} else {
				/* Allow different users to be processed */
				foreach($uid as $user_id){
					include('selected-session-observers.php');
				}
			}
			
		}

		/* Transform message into a string to be used by the view */
		if(!empty($message->other)){
			$message = process_messages($message);
		}

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

		mysql_close($ifs);

		header('Location: index.php');
		die();
	}

	mysql_close($ifs);