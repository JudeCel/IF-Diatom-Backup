<?php	
require_once('Connections/ifs.php');     
require_once('core.php');
require_once('models/participant-email-model.php');
require_once('models/users_model.php');


/* Set user type */
$user_type = NULL;
if($_SESSION['MM_UserTypeId']){
	$user_type = $_SESSION['MM_UserTypeId'];
}

$message = new stdClass;
$message->other = array();
$message->fields = array();
$message->error_types = array();
$fields = array();
$update = FALSE;
$redirection = FALSE;

//Set session id
$session_id = NULL;
if(isset($_GET['session_id'])){
	$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
}

if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3) && $session_id){
	/* The session was successflly updated */
	if(isset($_GET['update'])){
		$message->other[] = 'The Session was successfully updated';

		unset($_GET['update']);
	}

	//Page properties
	$page = 'Edit Session';
	$title = $page;
	$main_script = 'session_edit';
	$other_content = 'session_edit';
	$validate = true;
	$inline_scripting = 'session_edit_inline';


	//retrieve the brand project's details 
	mysql_select_db($database_ifs, $ifs);
	$query_retSessionInfo = "
	SELECT 
	  sessions.brand_project_id,
	  sessions.name,
	  sessions.start_time,
	  sessions.end_time,
	  sessions.incentive_details,
	  users.name_first,
	  users.name_last,
	  client_users.client_company_id,
	  brand_projects.end_date,
	  brand_projects.start_date,
	  brand_projects.name AS `brand_project_name`
	FROM
	  sessions
	  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
	  INNER JOIN client_users ON (brand_projects.client_company_id = client_users.client_company_id)
	  INNER JOIN users ON (client_users.user_id = users.id)
	WHERE
	  sessions.id = $session_id  
	GROUP BY
		sessions.id";

	$retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error() . ' T8');
	
	$row_retSessionInfo = array();
	$totalRows_retSessionInfo = 0;

	$brand_project_id = null;
	$client_comapny_id = null;
	$brand_name = 'New Brand Project';

	$start_date = time();
	$end_date = time();
	$min_date = time();
	$max_date = time();

	//If the query was successful
	if($retSessionInfo){
		$row_retSessionInfo = mysql_fetch_assoc($retSessionInfo);
		$totalRows_retSessionInfo = mysql_num_rows($retSessionInfo);
	}

	//If the results were positive
	if($totalRows_retSessionInfo){
		$brand_project_id = $row_retSessionInfo['brand_project_id'];
		$brand_name = $row_retSessionInfo['brand_project_name'];
		$client_company_id = $row_retSessionInfo['client_company_id'];

		/* Set start date and end date */
		$start_date = strtotime($row_retSessionInfo['start_time']);
		$end_date = strtotime($row_retSessionInfo['end_time']);
		$min_date = strtotime($row_retSessionInfo['start_date']);
		$max_date = strtotime($row_retSessionInfo['end_date']);
	}

	//retrieve the session moderator
	mysql_select_db($database_ifs, $ifs);
	$query_retSessionMod = "
	SELECT 
	  session_staff.user_id
	FROM
	  session_staff
	 WHERE
	   session_staff.session_id=$session_id 
	   AND session_staff.type_id=2
	";

	$retSessionMod = mysql_query($query_retSessionMod, $ifs) or die(mysql_error() . ' T9');
	$row_retSessionMod = array();
	$totalRows_retSessionMod = 0;

	//If the query was successful
	if($retSessionMod){
		$row_retSessionMod = mysql_fetch_assoc($retSessionMod);
		$totalRows_retSessionMod = mysql_num_rows($retSessionMod);
	}

	//retrieve the company users
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
	   client_users.client_company_id=$client_company_id
	   AND client_users.deleted is NULL 
	   AND client_users.user_id NOT IN 
	   (
	   		SELECT 
			  session_staff.user_id
			FROM
			  session_staff
	   		WHERE
			  session_staff.session_id = $session_id 
			  AND session_staff.type_id=4
	   )
	";
	$retMods = mysql_query($query_retMods, $ifs) or die(mysql_error() . ' T10');
	$totalRows_retMods = 0;

	//If the query was successful
	if($retMods){
		$totalRows_retMods = mysql_num_rows($retMods);
	}

	//do the insert
	if(isset($_POST['btnSubmit'])){
		$moderator_user_id = $_POST['moderator_user_id'];

		if(!isset($_POST['name']) || (isset($_POST['name']) && !$_POST['name'])){
			$message->fields['name'] = 'Session Name';
		}


		//Check if start time is set
		if(isset($_POST['start_time']) && $_POST['start_time']){
			$start_time = date('Y-m-d H:i:s', strtotime($_POST['start_time']));

			if(strtotime($start_time) < $min_date){
				$message->fields['start_time'] = 'Start Date';
				$message->error_types['start_time'] = 'less';
			} elseif(strtotime($start_time) > strtotime('+1 day', $max_date)){
				$message->fields['start_time'] = 'Start Date';
				$message->error_types['start_time'] = 'exceed';
			}
		} else {
			$message->fields['start_time'] = 'Start Date';
		}

		//Check if end time is set
		if(isset($_POST['end_time']) && $_POST['end_time']){
			$end_time = date('Y-m-d H:i:s', strtotime($_POST['end_time']));

			if(strtotime($end_time) < $min_date){
				$message->fields['end_time'] = 'End Date';
				$message->error_types['end_time'] = 'less';
			} elseif(strtotime($end_time) > strtotime('+1 day', $max_date)){
				$message->fields['end_time'] = 'End Date';
				$message->error_types['end_time'] = 'exceed';
			}
		} else {
			$message->fields['end_time'] = 'End Date';
		}

		if(empty($message->fields)){
			$session_name = $_POST['name'];

			$created = date('Y-m-d H:i:s'); 

			$moderator_type_id = 2;

			$comoderator_type_id = 3;
			
			$created = date('Y-m-d H:i:s');
			
			//update sessions
            $session_name = str_replace("'", "\'",$session_name);

			$insert3SQL = sprintf("UPDATE sessions SET name='$session_name',start_time='$start_time', end_time='$end_time',updated='$created' WHERE id=$session_id");
			mysql_select_db($database_ifs, $ifs);
			$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error() . ' T1');			
			
			//do the moderator add/update
			if($totalRows_retSessionMod > 0){
				//update session_staff
				$insert3SQL = sprintf("UPDATE  session_staff SET user_id=$moderator_user_id,  type_id=$moderator_type_id, updated='$created' WHERE session_id=$session_id AND type_id=$moderator_type_id");
				mysql_select_db($database_ifs, $ifs);
				$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error() . ' T2');
			} else 	{
				//if 1st session entry for a bp, set the default bp mod as session mod
				$insert3SQL = sprintf("INSERT INTO session_staff (user_id, session_id, type_id, comments,created) VALUES ($moderator_user_id, $session_id, $moderator_type_id, 'Creating a moderator entry for this session','$created')");
				mysql_select_db($database_ifs, $ifs);
				$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error() . ' T4');			
			}

			//this to send an email to the new mod
			if($row_retSessionMod['user_id'] != $moderator_user_id){
				//send an email to the new moderator
				
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
					users.id = ".$moderator_user_id."
				";
				
				$retReceiverDetails = mysql_query($query_retReceiverDetails, $ifs) or die(mysql_error() . ' T6');
				
				$row_retReceiverDetails = array();
				$totalRows_retReceiverDetails = 0;

				$receiver_name = '';
				$receiver_user_id = null;
				$to = '';
				$username1 = '';
				$user_login_id = null;

				//If the query was successful
				if($retReceiverDetails){					
					$totalRows_retReceiverDetails = mysql_num_rows($retReceiverDetails);				
				}

				//If results were available
				if($totalRows_retReceiverDetails){
					$row_retReceiverDetails = mysql_fetch_assoc($retReceiverDetails);

					$receiver_name =$row_retReceiverDetails['name_first']. ' '.$row_retReceiverDetails['name_last'];
					$receiver_user_id =$row_retReceiverDetails['id'];
					$to = $row_retReceiverDetails['email'];
					$username1 = $row_retReceiverDetails['username'];
					$user_login_id = $row_retReceiverDetails['login_id'];

					$from = "donotreply@insiderfocus.com";
						
					$subject = sprintf('%s %s Facilitator - Registration Confirmation', $brand_name, $session_name);

					$email_message = store_view_in_var(
						'admin-email-template.php', 
						array(
							'admin_email_type' => 'facilitator_register'
						)
					);

					//Create password
					$new_password = create_unique_password();
					$password = md5($new_password);

					//Perform update of password
					$pass_update = create_user_logins($database_ifs, $ifs, $to, $password, $moderator_user_id, $user_login_id);

					/* Set the tags available */
					$tags_available = array(
						'First Name', 
						'Last Name', 
						'Username', 
						'Start Date',
						'End Date',
						'Session Name',
						'Password',
						'Brand Name'
					);

					/* Set the replacements for parsing */
					$replacements = array();
					$replacements['First Name'] = (isset($row_retReceiverDetails['name_first']) ? $row_retReceiverDetails['name_first'] : '');
					$replacements['Last Name'] = (isset($row_retReceiverDetails['name_last']) ? $row_retReceiverDetails['name_last'] : '');
					$replacements['Username'] = (isset($row_retReceiverDetails['username']) ? $row_retReceiverDetails['username'] : '');
					$replacements['Start Date'] = date('h:ia l j F Y', $start_date);
					$replacements['End Date'] = date('h:ia l j F Y', $end_date);
					$replacements['Session Name'] = $session_name;
					$replacements['Password'] = $new_password;
					$replacements['Brand Name'] = $brand_name;

					/* Parse Template for tags */
					$email_message = parse_tags_for_template($email_message, $replacements, $tags_available);

					$user_id = $receiver_user_id;
					$name = $receiver_name;

					$sent = sendMail($database_ifs, $ifs, $to,$name,$subject,$email_message,$from,$user_id);			
			
					if($sent){
						mysql_close($ifs);

						$updateGoTo = $form_action . '&update=1';
						header(sprintf("Location: %s", $updateGoTo));
						die();
					} else {
						$message->other[] = 'An email could not be sent to the selected facilitator.';
					}
				} else {
					$message->other[] = 'An email could not be sent to the selected facilitator.';
				}						
			} else {
				$updateGoTo = $form_action . '&update=1';
				header(sprintf("Location: %s", $updateGoTo));
				die();
			}
		}
	}

	if(!empty($message->other) || !empty($message->fields)){
		if(!empty($message->fields)){
			$fields = array_keys($message->fields);
		}

		$message = process_messages($message);
	}

	require_once('views/popup.php');
} else {
	if(!$user_type){
		$_SESSION['notification'] = 'You are logged out, please login again.';
	} elseif(!$session_id) {
		$_SESSION['notification'] = 'Please select an appropriate session.';
	} else {
		$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
	}

	mysql_close($ifs);

	if($session_id){
		header('Location: brandProject.php');
	} else {
		header('Location: brandProject.php');
	}
	die();
}

mysql_close($ifs);
