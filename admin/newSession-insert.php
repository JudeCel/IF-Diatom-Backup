<?php
	/* If this file is not being being imported */
	if(!isset($import) || (isset($import) && !$import)){		    
		require_once('Connections/ifs.php');
		require_once('core.php');
		require_once('models/participant-email-model.php');
		require_once('models/users_model.php');		

		/* Set user type */	
		$user_type = NULL;
		if($_SESSION['MM_UserTypeId']){
			$user_type = $_SESSION['MM_UserTypeId'];
		}		
		
		$fields = array();
		$notification = null; //dummy variable if the notiification variable is needed
		$import = null;					
	}

	$message = new stdClass;
	$message->other = array();
	$message->fields = array();
	$message->error_types = array();

	$update = FALSE;
	$redirection = FALSE;
	$sent = null;
	
	require_once('models/brand_model.php');

if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
	/* Set message */
	if(isset($_SESSION['notification'])){
		$message->other = $_SESSION['notification'];
		unset($_SESSION['notification']);
	}

	//Brand Project id
	$brand_project_id = NULL;
	if(isset($_GET['brand_project_id'])){
		$brand_project_id =	strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
	}

	//Set User id
	$potential_facilitator = FALSE;
	if(!$import){
		$user_id = NULL;
		if(isset($_GET['user_id'])){
			$user_id = strip_tags(mysql_real_escape_string($_GET['user_id']));

			$redirection = FALSE; /* DISABLE Redirection for now */
			$potential_facilitator = TRUE;

			$message->other['facil_select'] = 'A potential Facilitator has been registered. Please select name from Existing Admin.';
		}
	}

	//Set session id
	$session_id = NULL;
	if(isset($_GET['session_id'])){
		$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
	}

	//Page properties
	$page = 'Add Session';
	$title = $page;
	$main_script = 'session_insert';
	$other_content = 'session_insert';
	$validate = true;
	$inline_scripting = 'session_insert_inline';

	//retrieve the bp info
	mysql_select_db($database_ifs, $ifs);
	$query_retBPInfo = "
	SELECT 
	  brand_projects.max_sessions,
	  brand_projects.name,
	  brand_projects.id,
	  brand_projects.moderator_user_id,
	  brand_projects.end_date,
	  brand_projects.start_date,
	  brand_projects.client_company_id,
	  brand_projects.logo_thumbnail_url,
	  brand_projects.logo_url
	FROM
	  brand_projects
	WHERE
	  brand_projects.id=$brand_project_id  
	";

	$retBPInfo = mysql_query($query_retBPInfo, $ifs) or die(mysql_error());
	$totalRows_retBPInfo = 0;
	$row_retBPInfo = array();

	$max_sessions = 0;
	$bp_name = null;

	$min_date = time();
	$max_date = time();

	/* If query is successful */
	if($retBPInfo){
		
		$totalRows_retBPInfo = mysql_num_rows($retBPInfo);		
	}

	if($totalRows_retBPInfo){
		$row_retBPInfo = mysql_fetch_assoc($retBPInfo);

		$max_sessions = $row_retBPInfo['max_sessions'];
		$bp_name = $row_retBPInfo['name'];

		/* Set min and max date */
		$min_date = strtotime($row_retBPInfo['start_date']);
		$max_date = strtotime($row_retBPInfo['end_date']);
	}

	/* Get brand project logo thumbnail url */
 	$brand_project_thumbnail_url = NULL;
 	if(isset($row_retBPInfo['logo_thumbnail_url'])){
 		$brand_project_thumbnail_url = $row_retBPInfo['logo_thumbnail_url'];
 	}

 	/* Get brand project logo full image */
 	$brand_project_logo_url = NULL;
 	if(isset($row_retBPInfo['logo_url'])){
 		$brand_project_logo_url = $row_retBPInfo['logo_url'];
 	}

	$uploadText = 'Preview';

	/* Set upload text to Upload if no image is found */
	if(!$brand_project_thumbnail_url || !$brand_project_logo_url ){
		$uploadText = 'Upload';
	}

	$client_company_id = $row_retBPInfo['client_company_id'];
	$brand_project_name = $row_retBPInfo['name'];

	//retrieve the user list
	mysql_select_db($database_ifs, $ifs);
	$query_retMods = "
	SELECT 
	  users.name_first,
	  users.name_last,
	  client_users.user_id
	FROM
	  users
	  INNER JOIN client_users ON (users.id = client_users.user_id)	  
	WHERE
	   client_users.active = 1
	   AND client_users.client_company_id=$client_company_id
	   AND (client_users.type_id != 4
	   OR client_users.type_id IS NULL) 
	";
	$retMods = mysql_query($query_retMods, $ifs) or die(mysql_error());
	$totalRows_retMods = 0;

	//If query was successful
	if($retMods){
		$totalRows_retMods = mysql_num_rows($retMods);
	}

	//retrieve the barnd projects
	mysql_select_db($database_ifs, $ifs);
	$query_retSession = "
	SELECT 
	  sessions.name,
	  sessions.start_time,
	  sessions.end_time,
	  sessions.id
	FROM
	  sessions
	  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
	WHERE
	  brand_projects.id=$brand_project_id   
	";

	$retSession = mysql_query($query_retSession, $ifs) or die(mysql_error());
	$totalRows_retSession = 0;	

	//If query was successful
	if($retSession){
		$totalRows_retSession = mysql_num_rows($retSession);
	}

	/* If there were any results, find the brand project name */
	if($totalRows_retSession){
		$row_session_bp = mysql_fetch_assoc($retSession);

		mysql_data_seek($retSession, 0); //reset results
	}

	//do the inserts
	if(isset($_POST['btnSubmit'])){
		$update_success = TRUE;

		$created = date('Y-m-d H:i:s');

		if(isset($_POST['name']) && $_POST['name']){
			$session_name = $_POST['name'];
		} else {
			$update_success = FALSE;
			$message->fields['name'] = 'Session Name';
		}
		
		if(isset($_POST['start_date']) && $_POST['start_date']){
			$start_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));

			if(strtotime($start_date) < $min_date){
				$update_success = FALSE;
				$message->fields['start_date'] = 'Start Date';
				$message->error_types['start_date'] = 'less';
			} elseif(strtotime($start_date) > strtotime('+1 day', $max_date)){
				$update_success = FALSE;
				$message->fields['start_date'] = 'Start Date';
				$message->error_types['start_date'] = 'exceed';
			}
		} else {
			$update_success = FALSE;
			$message->fields['start_date'] = 'Start Date';
		}
		
		if(isset($_POST['end_date']) && $_POST['end_date']){
			$end_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));

			if(strtotime($end_date) < $min_date){
				$update_success = FALSE;
				$message->fields['end_date'] = 'End Date';
				$message->error_types['end_date'] = 'less';
			} elseif(strtotime($end_date) > strtotime('+1 day', $max_date)){
				$update_success = FALSE;
				$message->fields['end_date'] = 'End Date';
				$message->error_types['end_date'] = 'exceed';
			}
		} else {
			$update_success = FALSE;
			$message->fields['end_date'] = 'End Date';
		}

		if($update_success){
			$update = true;	
			if(!$import){		
				if(isset($_POST['moderator_user_id'])){
					$user_id = $_POST['moderator_user_id'];
				} else {
					$update = false;
					$message->other[] = 'Please add a new facilitator before registering a new session.';
				}			
			} elseif(is_array($user_id)){
				$update = false;
			}
		} else {
			$update = false;
		}							
	}

	/* The session was created  */
	if($update && ($totalRows_retSession < $max_sessions)){
		$type_id = 2;

        $session_name = str_replace("'", "\'",$session_name);
	
		//insert into sessions
		$insert2SQL = sprintf("INSERT INTO sessions (name, brand_project_id,start_time, end_time, created) VALUES ('$session_name', $brand_project_id,'$start_date', '$end_date','$created')");
		mysql_select_db($database_ifs, $ifs);
		$Result3 = mysql_query($insert2SQL, $ifs) or die(mysql_error());
		
		$session_id = mysql_insert_id($ifs);

		if($Result3){
			$message->other[] = sprintf('A new session, %s, has been created.', $session_name);
		} else {
			$message->other[] = 'A new session could not be created.';
		}		

		//if 1st session entry for a bp, set the session mod
		$insert3SQL = sprintf("INSERT INTO session_staff (user_id, session_id, type_id, comments,created) VALUES ($user_id, $session_id, $type_id, 'Creating a moderator entry for this session','$created')");
		mysql_select_db($database_ifs, $ifs);
		$update = mysql_query($insert3SQL, $ifs) or die(mysql_error());

		if($update){
			//retrieve receiver details		- Could be Facilitator
			mysql_select_db($database_ifs, $ifs);
			$query_retReceiverDetails = "
			SELECT 
			  users.id,
			  user_logins.username,
			  user_logins.id As login_id,
			  users.name_first,
			  users.name_last,
			  users.email
			FROM
			  user_logins
			  INNER JOIN users ON (user_logins.id = users.user_login_id)
			WHERE
				users.id = ".$user_id."
			";
			
			//Find facilitator
			$retReceiverDetails = retrieve_users($database_ifs, $ifs, true, true, $user_id);			

			//If query was successful
			if($retReceiverDetails && !is_string($retReceiverDetails) && $start_date){
				//Get data for facilitator
				$row_retReceiverDetails = mysql_fetch_assoc($retReceiverDetails);
				$totalRows_retReceiverDetails = mysql_num_rows($retReceiverDetails);
				
				$receiver_name = $row_retReceiverDetails['name_first']. ' '.$row_retReceiverDetails['name_last'];
				$receiver_user_id = $row_retReceiverDetails['id'];
				$to = $row_retReceiverDetails['email'];
				$username1 = $row_retReceiverDetails['username'];
				$user_login_id = $row_retReceiverDetails['user_login_id'];
			
				$from = "donotreply@insiderfocus.com";				
				
				$subject = sprintf('(%s) %s Facilitator Registration', $bp_name, $session_name);

				/* Get content for message */
				$email_message = store_view_in_var(
					'admin-email-template.php', 
					array(
						'admin_email_type' => 'facilitator_register'
					)
				);

				/* Set the tags available */
				$tags_available = array(
					'First Name', 
					'Last Name', 
					'Username', 
					'Start Date',
					'End Date',
					'Brand Name',
					'Session Name',
					'Password'
				);

				/* Set the replacements for parsing */
				$replacements = array();
				$replacements['First Name'] = (isset($row_retReceiverDetails['name_first']) ? $row_retReceiverDetails['name_first'] : '');
				$replacements['Last Name'] = (isset($row_retReceiverDetails['name_last']) ? $row_retReceiverDetails['name_last'] : '');
				$replacements['Username'] = (isset($row_retReceiverDetails['username']) ? $row_retReceiverDetails['username'] : '');
				$replacements['Start Date'] = date('h:ia l j F Y', strtotime($start_date));
				$replacements['End Date'] = date('h:ia l j F Y', strtotime($end_date));
				$replacements['Session Name'] = $session_name;
				$replacements['Brand Name'] = $bp_name;
				
				if((!$import || !isset($password['password'])) && !$potential_facilitator){
					$replacements['Password'] = 'Your Password';
				} else {
					if(isset($password['password'])){
						$replacements['Password'] = $password['password'];
					} else {
						//Create password
						$new_password = create_unique_password();
						$password = md5($new_password);

						//Perform update of password
						$pass_update = create_user_logins($database_ifs, $ifs, $to, $password, $user_id, $user_login_id);

						/* Set password replacement */
						if($pass_update){
							$replacements['Password'] = $new_password;
						} else {
							$replacements['Password'] = 'Your Password';
						}
					}
				}

				/* Parse Template for tags */
				$email_message = parse_tags_for_template($email_message, $replacements, $tags_available);

				$name = $receiver_name;
				
				$sent = sendMail($database_ifs, $ifs, $to,$name,$subject,$email_message,$from,$user_id);

				if($sent){
					$message->other[] = 'An email was sent to the new facilitator.';

					/* Redirect back to page */
					if($redirection || $import){
						mysql_close($ifs);

						//Set notification
						$_SESSION['notification'] = $message->other;

						if($redirection && !$import){
							header(sprintf("Location: clientCompanyUsers-insert.php?client_company_id=%d&type_id=2&update=1", $client_company_id));
						} else {
							header(sprintf("Location: newSession-insert.php?brand_project_id=%d&update=1", $brand_project_id));
						}

						die();
					}					
				} else {
					$message->other[] = 'A registration confirmation email has been sent to the new Facilitator.';
				}
			} else {
				$message->other[] = 'A registration confirmation email has been sent to the new Facilitator.';
			}
		} else {
			$message->other[] = 'A new Facilitator could not be created';
		}
	}

	/* If some fields are empty */
	if(!empty($fields)){
		$fields = array_merge(array_keys($message->fields), $fields);		
	} else {
		$fields = array_keys($message->fields);
	}

	//Set message to be processed later
	if(!empty($message) && $sent){
		//Do not display facilitator select message
		if(isset($message->other['facil_select'])){
			unset($message->other['facil_select']);
		}

		//Session is not writing correctly, make sure the data is saved
		session_write_close();
		session_start();

		$_SESSION['notification'] = array();
		$_SESSION['notification'][] = process_messages($message);

		session_write_close();

		//close fancybox
		echo '<script type="text/javascript" src="js/fancybox_close.js" />';	
	} elseif(!empty($message) || $notification){ //if creation was not successful
		
		if(!empty($message)){ //posible that both cases are true
			$message = ($notification ? $notification : '') . process_messages($message);
		} else {
			$message = $notification; //only notification available
		}

		if(isset($_GET['update']) && !$message){
			$message = '<p>A new session was created.</p>';
		}
	}

	/* If not importing file */
	if(!$import){
		include('views/popup.php');
	} else {
		include('views/import.php');
	}
}	else {
	mysql_close($ifs);

	header('Location: index.php');
	die();
}

if(!$import){
	mysql_close($ifs);
}