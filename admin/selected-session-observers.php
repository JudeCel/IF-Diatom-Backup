<?php
	/* Make sure user id is set */
	if(!isset($user_id)){
		$user_id = null;
	}

	/* Make sure session id is set */
	if(!isset($session_id)){
		$user_id = null;
	}

	/* Make sure type id is set */
	if(!isset($type_id)){
		$user_id = null;
	}

	/* Make sure user type id is set */
	if(!isset($user_type)){
		$user_type = null;
		if(isset($_SESSION['MM_UserTypeId'])){
			$user_type = $_SESSION['MM_UserTypeId'];
		}
	}

	/* Make sure uid is set */
	if(!isset($uid)){
		$uid = array();
	}

	/* Get last uid value */
	$last_uid = null;
	if(!empty($uid)){
		$last_uid_key = count($uid) - 1;
		$last_uid = $uid[$last_uid_key];
	}

	/* Make sure max observers is set */
	if(!isset($max_observers)){
		$max_observers = 0;
	}

	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3) && $user_id && $session_id && $type_id && $max_observers){

		//enter the observers
		$insert3SQL = sprintf("INSERT INTO session_staff (user_id, session_id, type_id, comments,created) VALUES ($user_id, $session_id, $type_id, 'Creating another observer for this session','$created')");
		mysql_select_db($database_ifs, $ifs);
		$update = mysql_query($insert3SQL, $ifs) or die(mysql_error());	

		if($update){
			//retrieve receiver details - could be observer		
			mysql_select_db($database_ifs, $ifs);
			$query_retReceiverDetails = "
			SELECT 
			  users.name_first,
			  users.name_last,
			  users.email,
			  users.id,
			  user_logins.username,
			  user_logins.id As user_logins_id
			FROM
			  users
			  INNER JOIN user_logins ON (users.user_login_id = user_logins.id)
			WHERE
			  users.id = ".$user_id."
			";
			$retReceiverDetails = mysql_query($query_retReceiverDetails, $ifs) or die(mysql_error());
			$totalRows_retReceiverDetails = 0;
			$row_retReceiverDetails = array();

			$receiver_name = '';
			$receiver_user_id = 0;
			$to = '';
			$username = '';
			$user_login_id = null;

			if($retReceiverDetails){
				$row_retReceiverDetails = mysql_fetch_assoc($retReceiverDetails);
				$totalRows_retReceiverDetails = mysql_num_rows($retReceiverDetails);

				$receiver_name = $row_retReceiverDetails['name_first'];
				$receiver_user_id = $row_retReceiverDetails['id'];
				$to = $row_retReceiverDetails['email'];
				$username = $row_retReceiverDetails['username'];

				$user_login_id = $row_retReceiverDetails['user_logins_id'];
				$password = 'Your Password';
			}

			//retrieve sender details which will be session moderator
			mysql_select_db($database_ifs, $ifs);
			$query_retSenderDetails = "
			SELECT 
			  users.name_first,
			  users.name_last,
			  users.email,
			  users.id,
			  users.mobile,
			  users.phone
			FROM
			  users
			  INNER JOIN session_staff ON (users.id = session_staff.user_id)
			WHERE
			  session_staff.type_id = 2 AND 
			  session_staff.session_id = ".$session_id."
			";
			$retSenderDetails = mysql_query($query_retSenderDetails, $ifs) or die(mysql_error());

			$sender_name = '';
			$sender_email = '';
			$sender_user_id = null;
			$sender_mobile = null;
			$sender_phone = null;

			if($retSenderDetails){
				$row_retSenderDetails = mysql_fetch_assoc($retSenderDetails);
				$totalRows_retSenderDetails = mysql_num_rows($retSenderDetails);

				$sender_name = $row_retSenderDetails['name_first'].' '.$row_retSenderDetails['name_last'];
				$sender_email = $row_retSenderDetails['email'];
				$sender_user_id = $row_retSenderDetails['id'];
				$sender_mobile = $row_retSenderDetails['mobile'];
				$sender_phone = $row_retSenderDetails['phone'];
			}

			$from = "donotreply@insiderfocus.com";
			$name = $receiver_name;				
		
			/// brand project details				
			mysql_select_db($database_ifs, $ifs);
			$query_retBrandProject = "
				SELECT 
				  brand_projects.name,
				  client_companies.name AS company_name
				FROM
				  sessions
				  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
				  INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
				  INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
				WHERE
				  sessions.id = ".$session_id."
			";
		
			$retBrandProject = mysql_query($query_retBrandProject, $ifs) or die(mysql_error());
			$totalRows_retBrandProject = 0;
			$row_retBrandProject = array();

			$brand_name = '';
			$session = '';
			$company_name = '';

			if($retBrandProject){
				$row_retBrandProject = mysql_fetch_assoc($retBrandProject);
				$totalRows_retBrandProject = mysql_num_rows($retBrandProject);

				$brand_name = $row_retBrandProject['name'];
				$session = $row_retSessionInfo['name'];
				$company_name = $row_retBrandProject['company_name'];
			}

			if($redirection){
				//Create New Password
				$password = create_unique_password();
				$pass_hashed = md5($password);

				$user_login_id = create_user_logins($database_ifs, $ifs, $to, $pass_hashed, $user_id);
			}

			$subject = 'Your Observer Ticket to (' . $brand_name . ') ' . $session . ' Session';

			/* Get content for message */
			$email_message = store_view_in_var(
				'view-email-template.php', 
				array(
					'admin_email_type' => 'observer_register',
					'session_id' => $session_id
				)
			);

			/* Set the tags available */
			$tags_available = array(
				'First Name', 
				'Last Name', 
				'Username', 
				'Start Date',
				'End Date',
				'Session Name',
				'Brand Name',
				'Facilitator Name',
				'Facilitator Email',
				'Facilitator Phone',
				'Facilitator Mobile',
				'Password'
			);

			/* Set the replacements for parsing */
			$replacements = array();
			$replacements['First Name'] = (isset($row_retReceiverDetails['name_first']) ? $row_retReceiverDetails['name_first'] : '');
			$replacements['Last Name'] = (isset($row_retReceiverDetails['name_last']) ? $row_retReceiverDetails['name_last'] : '');
			$replacements['Username'] = (isset($row_retReceiverDetails['username']) ? $row_retReceiverDetails['username'] : '');
			$replacements['Start Date'] = date('h:ia l j F Y', strtotime($row_retSessionInfo['start_time']));
			$replacements['End Date'] = date('h:ia l j F Y', strtotime($row_retSessionInfo['end_time']));
			$replacements['Session Name'] = $session;
			$replacements['Brand Name'] = $brand_name;
			$replacements['Facilitator Name'] = $sender_name;
			$replacements['Facilitator Email'] = $sender_email;
			$replacements['Facilitator Phone'] = $sender_phone;
			$replacements['Facilitator Mobile'] = $sender_mobile;
			$replacements['Password'] = $password;

			/* Parse Template for tags */
			$email_message = parse_tags_for_template($email_message, $replacements, $tags_available);

			$sent = sendMail($database_ifs, $ifs, $to, $name, $subject, $email_message, $from, $receiver_user_id);

			/* Redirect if needed */
			if($redirection && $sent && (!$last_uid || ($last_uid && $user_id == $last_uid))){
				mysql_close($ifs);

				header("Location: " . $redirect_path);
				die();
			} elseif(!$redirection && !$sent){
				$message->others[] = 'An e-mail could not be sent to the created observer';
			} elseif($sent) {
				$message->others[] = 'An e-mail was sent to the created observer';
			} else {
				$message->others[] = 'The observer could not be added';
			}
		} else {
			$message->others[] = 'The observer could not be created';
		}
	} else {
		$_SESSION['notification'] = 'The Observer could not be set. Please contact the administrator.';
		
		if(!$max_observers){
			$_SESSION['notification'] = 'You\'ve reached the observer limit, please clear some observers if you would like to add more';
		}
  
    if(!admin($database_ifs, $ifs)){
      $_SESSION['current_location'] = $form_action;
    }

		mysql_close($ifs);

		header('Location: index.php');
		die();
	}