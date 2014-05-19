<?php
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/participant-email-model.php');
	require_once('models/users_model.php');
	require_once('models/brand_model.php');
	require_once('models/array_repository_model.php');

	//Initialise values for testing and notification
	$valid = TRUE;
	$notification = array();
	$updateGoTo = "session-participants-jqgrid.php?session_id=".$session_id;

	/* Make sure that session_id and email_type-id is set */
	if(!$session_id || !$email_type_id){
		$valid = FALSE;

		/* Session was not set */
		if(!$session_id){
			$notification[] = 'The session was not specified.';
		}

		if(!$email_type_id){
			$notification[] = 'The e-mail type was not set.';
		}
	}

	/* The email sending could not continue */
	if(!$valid){
		prepare_messages_and_revert_to_previous($notification, $updateGoTo);
	}

	/* Get particpant id */
	$participant_id = NULL;
	$part_num = 0;
	if(isset($_GET['participant_id']) && $email_type_id != 5){
		$participant_id_raw = strip_tags(mysql_real_escape_string($_GET['participant_id']));	
		$participant_id = explode(",",$participant_id_raw);
		$part_num = count($participant_id); //get number of participants
	}

	/* If closing session*/
	$created=date('Y-m-d H:i:s'); 
	$retSessionParticipants = NULL;

	
	if($email_type_id == 5){
		mysql_select_db($database_ifs, $ifs);
		$query_retSessionParticipants = "
			SELECT 
			  participant_lists.participant_id,
			  participant_lists.id,
			  participant_lists.participant_reply_id
			FROM
			  participant_lists
			  
			WHERE
			  participant_lists.session_id = ".$session_id."
			  AND participant_lists.participant_reply_id=1
		";
		
		$retSessionParticipants = mysql_query($query_retSessionParticipants, $ifs) or die(mysql_error());
		
		/* Check if query was successful */
		if(!$retSessionParticipants || ($retSessionParticipants && is_string($retSessionParticipants))){
			$valid = FALSE;
			$notification[] = 'The participants could not be found';
		} else {
			$totalRows_retSessionParticipants = mysql_num_rows($retSessionParticipants);
		}		
	}

	/* The email sending could not continue */
	if(!$valid){
		prepare_messages_and_revert_to_previous($notification, $updateGoTo);
	}

	/* Find specific email template */
	$retSessionEmail = retrieve_email_template($session_id, $email_type_id, $database_ifs, $ifs);

	$row_retSessionEmail = array();
	$totalRows_retSessionEmail = 0;
	
	if(!$retSessionEmail || ($retSessionEmail && is_string($retSessionEmail))){
		// $valid = FALSE;
		// $notification[] = 'The email information could not be found';		
	} else {
		$row_retSessionEmail = mysql_fetch_assoc($retSessionEmail); //get email template
		$totalRows_retSessionEmail = mysql_num_rows($retSessionEmail); //number of rows available

	}

	/* The email sending could not continue */
	if(!$valid){
		prepare_messages_and_revert_to_previous($notification, $updateGoTo);
	}	

	/* Initialise values */
	$brand_name = NULL;
	$start_date = NULL;
	$end_date = NULL;
	$incentive_details = NULL;

	$brand_project = retrieve_brand_project($database_ifs, $ifs);

	if(!$brand_project || ($brand_project && is_string($brand_project))){
		$valid = FALSE;
		$notification[] = 'The brand information could not be found';
	}

	/* The email sending could not continue */
	if(!$valid){
		prepare_messages_and_revert_to_previous($notification, $updateGoTo);
	}
	
	/* Get Brand Information */
	$row_retBrandProject = mysql_fetch_assoc($brand_project);
	$totalRows_retBrandProject = mysql_num_rows($brand_project);

	/* Set session name */
	$session_name = null;
	if(isset($row_retBrandProject['Session_Name'])){
		$session_name = $row_retBrandProject['Session_Name'];
	}

	/* Get Brand Properties */
	if(isset($row_retBrandProject['name'])){
		$brand_name = $row_retBrandProject['name'];
	}
	if(isset($row_retBrandProject['start_time'])){
		$start_date = strtotime($row_retBrandProject['start_time']);
		$start_date = date('h:ia l j F Y', $start_date);
	}
	if(isset($row_retBrandProject['end_time'])){
		$end_date = strtotime($row_retBrandProject['end_time']);
		$end_date = date('h:ia l j F Y', $end_date);
	}
	if(isset($row_retBrandProject['incentive_details'])){
		$incentive_details = $row_retBrandProject['incentive_details'];
	}

	/* Iterate through particpants and built lists */
	if($part_num > 0 && !$retSessionParticipants){
		foreach($participant_id as $key=>$id){
			$replacements = array();

			if($email_type_id != 6){
				/* Built particpant list and get new id */
				$list_id = build_participant_list($database_ifs, $ifs, $id);

				if(!$list_id){
					$notification[] = 'The participant list could not be build correctly';
				}
			} else {
				$list_id = $id;
			}

			/* Check if should filter out participants that is not on the participant lists */
			$search_lists = true;
			if($email_type_id == 6){
				$search_lists = false;				
			}

			$participant = find_participant($database_ifs, $ifs, $id, true, $search_lists);			

			if($participant && !is_string($participant)){
				$row_retReceiverDetails = mysql_fetch_assoc($participant);
				$totalRows_retReceiverDetails = mysql_num_rows($participant);			

				if($totalRows_retReceiverDetails){
					/* Get receiver first name */
					$receiver_first_name = $row_retReceiverDetails['name_first'];			
					$replacements['First Name'] = $receiver_first_name;

					/* Get receiver last name  */
					$receiver_last_name = $row_retReceiverDetails['name_last'];			
					$replacements['Last Name'] = $receiver_last_name;

					/* Get receiver email  */
					$receiver_email = $row_retReceiverDetails['email'];			
					$replacements['Email'] = $receiver_email;

					$receiver_user_id = $row_retReceiverDetails['user_id']; //set id
					
					if($email_type_id != 6){
						/* Set as the last invited session */
						set_last_session_invited_name($database_ifs, $ifs, $session_name, $receiver_user_id);
					}

					/* Set start date, end date and brand for parsing */
					$replacements['Start Date'] = $start_date;
					$replacements['End Date'] = $end_date;
					$replacements['Brand'] = $brand_name;

					$from = "yourvoice@insiderfocus.com"; //yourvoice@insiderfocus.com
					$name = $receiver_first_name . '  ' . $receiver_last_name;
					$to = $row_retReceiverDetails['email'];

					$subject = '';
					if(isset($row_retSessionEmail['subject'])){
						$subject = $row_retSessionEmail['subject'];
					}

					/* Get content for message */
					$message = store_view_in_var(
						'view-email-template.php', 
						array(
							'session_id' => $session_id, 
							'email_type_id' => $email_type_id,
							'list_id' => $list_id
						)
					);

					/* Parse Template for tags */
					$message = parse_tags_for_template($message, $replacements);

					/* Send email */
					$sent_email = sendMail($database_ifs, $ifs, $to, $name, $subject, $message, $from, $receiver_user_id);
					
					//Let the administrators know that the e-mail was not sent correctly.
					if(!$sent_email && $email_type_id != 6){
						build_participant_list($database_ifs, $ifs, $row_retReceiverDetails['list_id'], true, 4, $session_id); //Inform system taht e-mail is not sent
					}
				}
			} else{
				$notification[] = 'The participant with participant ID ' . $id . ' could not be found';
			}
		}

		if(!empty($notification)){
			$_SESSION['notification'] = $notification;
		}

		mysql_close($ifs);

		/* Go to session */
		header(sprintf("Location: %s", $updateGoTo));

		die();

	} elseif($retSessionParticipants){ /* Close Session and send out e-mails */

		$filename=basename($_SERVER['PHP_SELF']);
		$participants = prepare_foreach($retSessionParticipants);
		$from="yourvoice@insiderfocus.com";

		foreach($participants as $part){
			$list_id = $part['participant_id'];
	
			$retReceiverDetails = find_participant($database_ifs, $ifs, $list_id);

			/* The receiver could not be found */
			if(!$retReceiverDetails){
				$notification[] = 'The participant with participant ID ' . $list_id . ' could not be found';
			} else {
				/**
				* Set information and send email
				**/
				$row_retReceiverDetails = mysql_fetch_assoc($retReceiverDetails);

				$totalRows_retReceiverDetails = mysql_num_rows($retReceiverDetails);
			  
				$name = $row_retReceiverDetails['name_first'] . ' ' . $row_retReceiverDetails['name_last'];
				$receiver_user_id = $row_retReceiverDetails['id'];

				$replacements = array();

				$receiver_first_name = $row_retReceiverDetails['name_first'];			
				$replacements['First Name'] = $receiver_first_name;

				/* Get receiver first name */
				$receiver_username = $row_retReceiverDetails['username'];			
				$replacements['Username'] = $receiver_username;

				/* Get receiver last name  */
				$receiver_last_name = $row_retReceiverDetails['name_last'];			
				$replacements['Last Name'] = $receiver_last_name;

				/* Get receiver email  */
				$receiver_email = $row_retReceiverDetails['email'];			
				$replacements['Email'] = $receiver_email;

				/* Set start date, end date and brand for parsing */
				$replacements['Start Date'] = $start_date;
				$replacements['End Date'] = $end_date;
				$replacements['Brand'] = $brand_name;

				$to = $row_retReceiverDetails['email'];
				$subject = $row_retSessionEmail['subject'];

				/* Get content for message */
				$message = store_view_in_var(
					'view-email-template.php', 
					array(
						'session_id' => $session_id, 
						'email_type_id' => $email_type_id,
						'list_id' => $list_id
					)
				);

				if($message){
					/* Parse Template for tags */
					$message = parse_tags_for_template($message, $replacements);

					/* Send email */
					if(!sendMail($database_ifs, $ifs, $to, $name, $subject, $message, $from, $receiver_user_id)){
						build_participant_list($database_ifs, $ifs, $part['id'], true, 4, $session_id); //Inform system taht e-mail is not sent
					}			
				} else {
					continue;
				}
			}
		}

		if(!empty($notification)){
			$_SESSION['notification'] = $notification;
		}

		mysql_close($ifs);

		header(sprintf("Location: %s", $updateGoTo));

		die();	
	}

	mysql_close($ifs);



	






	

