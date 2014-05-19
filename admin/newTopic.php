<?php 
	require_once('Connections/ifs.php');
 	require_once('core.php');
 	require_once('models/users_model.php');

 	/* Get user type id */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	/* Get message information */
	$message = new StdClass;
	$message->other = array();

	if(isset($_SESSION['notification'])){
		$message->other = $_SESSION['notification'];

		unset($_SESSION['notification']);
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
			$_SESSION['notification'] = 'An appropriate session hasn\'t been set';

			mysql_close($ifs);

    	header('Location: session.php'); //return to main page
    	die();		
		}	

		//Page properties
		$main_script = 'session_topics';
		$other_content = 'session_topics';
		$grid = false;
		$validate = false;
		$inline_scripting = 'session_topic_inline';
		$page_help = 'sessions';
		
		//Only initialisation - if client company id is available, it is set further down
		$page = 'Sessions | Session Topics';
		$title = 'Sessions';
		
		$sub_id = null;
		$sub_group = null;
		$sub_navigation = null;

		$footer = 'session_footer';

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
		  brand_projects.logo_url,
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
		$client_company_id = NULL;
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

		mysql_select_db($database_ifs, $ifs);
		$query_retTopic = "
		SELECT 
		  topics.*
		FROM
		  topics
		WHERE
		   topics.session_id=$session_id
		ORDER BY
		  topic_order_id
		";

		$retTopic = mysql_query($query_retTopic, $ifs) or die(mysql_error());
		$totalRows_retTopic = 0;
		$topics_json = json_encode(array());

		if($retTopic){
			$totalRows_retTopic = mysql_num_rows($retTopic);

			//Add together topics for javascript
			$topics = array();
			while($row_retTopic = mysql_fetch_assoc($retTopic)){
				$topics[] = $row_retTopic;
			}

			if(!empty($topics)){
				mysql_data_seek($retTopic, 0); //reset results
				
				$topics_json = json_encode($topics); //prepare topics for javascript
			}
		}

		if($totalRows_retTopic == 0){
			$make_active=1;
		}

		if(isset($_POST['btnSubmit'])){
			$topic_name = $_POST['topic_name'];
			$created = date('Y-m-d H:i:s');			
			
			//insert the new topics
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

			mysql_close($ifs);
			
			$updateGoTo = "newTopic.php?".$_SERVER['QUERY_STRING'];
			header(sprintf("Location: %s", $updateGoTo));
			die();	
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

		//Set message
		if(!empty($message->other)){
			$message = process_messages($message);
		}

		require_once('views/home.php');
	}	else {
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
