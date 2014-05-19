<?php 
	require_once('Connections/ifs.php');
 	require_once('core.php');
 	require_once('models/users_model.php');

 	$message = '';
 	$message_val = new StdClass;
 	$message_val->other = array();
 	$message_val->fields = array();
 	$fields = array();

 	if(isset($_SESSION['notification'])){
		$message_val->other = $_SESSION['notification'];

		unset($_SESSION['notification']);
	}

 	/* Get user type id */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}
 
 	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
		/* Get user id */
		$user_id = 0;
		if(isset($_SESSION['MM_UserId']['user_id'])){
			$user_id = $_SESSION['MM_UserId'];
		}
		
		if(isset($_GET['session_id'])){
			$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
		} else {
			mysql_close($ifs);

			header('Location: index.php'); //return to main page
			die();		
		}	

		$page = 'Add Topic';
		$title = $page;
		$main_script = 'session_topic_insert';
		$other_content = 'session_topic_insert';
		$validate = true;
		$inline_scripting = false;

		//retrieve the session staff info 
		mysql_select_db($database_ifs, $ifs);
		$query_retSessionInfo = "
		SELECT 
		  session_staff.id AS session_staff_id,
		  sessions.brand_project_id,
		  sessions.name,
		  sessions.start_time,
		  sessions.end_time,
		  sessions.active_topic_id,
		  sessions.status_id,
		  session_staff.session_id,
		  users.name_first,
		  users.name_last,
		  session_staff.user_id,
		  brand_projects.client_company_id,
		  brand_projects.name AS `brand_project_name`,
		  client_companies.name AS `client_company_name`
		FROM
		  sessions
		  INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
		  INNER JOIN users ON (session_staff.user_id = users.id)
		  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
		  INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
		WHERE
		  session_staff.type_id=2 AND sessions.id=$session_id		  
		";

		$retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error());
		$totalRows_retSessionInfo = 0;
		$row_retSessionInfo = array();

		$brand_project_id = NULL;
		$totalRows_retSessionInfo = 0;
		$row_retSessionInfo = array();

		$brand_project_id = NULL;
		$client_company_id = NULL;
		$status_id = 2;

		$brand_project_name = 'New Brand Project';
		$client_company_name = 'New Client Company';

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

			//Set variable that will allow display of brand project name and client company name
      if($client_company_id && $brand_project_id){
        $subtitle_found = true;
      }
		}

		mysql_select_db($database_ifs, $ifs);
		$query_retTopic = "
		SELECT 
		  topics.*
		FROM
		  topics
		WHERE
		   topics.session_id=$session_id
		ORDER BY
		  topics.topic_order_id    
		";

		$retTopic = mysql_query($query_retTopic, $ifs) or die(mysql_error());
		$totalRows_retTopic = 0;

		if($retTopic){
			$totalRows_retTopic = mysql_num_rows($retTopic);
		}

		$make_active = 0;
		if($totalRows_retTopic == 0){
			$make_active = 1;
		}

		if(isset($_POST['btnSubmit'])){
			$update_success = TRUE;

			if(isset($_POST['topic_name']) && $_POST['topic_name']){
				$topic_name = $_POST['topic_name'];
			} else {
				$update_success = FALSE;
				$message_val->fields['topic_name'] = 'Topic Name';
			}

			$created = date('Y-m-d H:i:s');			
			
			if($update_success){
				//insert the new topics

                $topic_name = str_replace("'", "\'",$topic_name);

				$insertSQL = sprintf("INSERT INTO topics (session_id, name,created) VALUES ($session_id,'$topic_name', '".$created."')");
				mysql_select_db($database_ifs, $ifs);
				$Result1 = mysql_query($insertSQL, $ifs) or die(mysql_error());
				$topic_id = mysql_insert_id($ifs); 
				
				if($make_active == 1){
					//make the 1st topic active by default
					$updateSQL4 = sprintf("UPDATE sessions SET  active_topic_id='$topic_id', updated='$created' WHERE id= $session_id" );
					mysql_select_db($database_ifs, $ifs);
					$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());				

					//add the topic entry in the active logs table
					$insertSQL4 = sprintf("INSERT INTO topic_activity_logs  (session_id, topic_id, created) VALUES ($session_id, $topic_id, '$created') ");
					mysql_select_db($database_ifs, $ifs);
					$Result4 = mysql_query($insertSQL4, $ifs) or die(mysql_error());	
				}
				
				$message_val->other[] = 'A topic was successfully created.';

				$_SESSION['notification'] = array();
				$_SESSION['notification'][] = process_messages($message_val);

				mysql_close($ifs);

				header("Location: " . $form_action);
				die();				
			}	else {
				$fields = array_keys($message_val->fields);
				$message = process_messages($message_val);
			}
		}

		//Set message if not set yet
		if(!$message && !empty($message_val->other)){
			$message = process_messages($message_val);
		}

		mysql_close($ifs);

		require_once('views/popup.php');
	}	else {
		if(!$user_type){
			$_SESSION['notification'] = 'You are logged out, please login again.';
		} else {
			$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
		}

		mysql_close($ifs);

		header('Location: index.php');
		die();
	}
