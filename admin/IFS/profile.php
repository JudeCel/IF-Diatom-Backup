<?php
	require_once("../Connections/ifs.php");
	require('../core.php');
	require_once('../models/users_model.php');
	require_once('../models/participant-email-model.php');
	require_once('../models/brand_model.php');

	if(!admin($database_ifs, $ifs) || !$session_id){
		$_SESSION['notification'] = array();

		/* Make sure taht the user is not sent through a loop */
		if($session_id){
			$_SESSION['current_location'] = $current_location;			
		} else {
			$_SESSION['notification'][] = 'The session is not available at the moment. Please try again later.';
		}

		$_SESSION['notification'][] = 'Please enter your Username and Password.';

		header("Location: /login.php");
	}

	//Specify if you want to go straight to the chatroom
	$bypass = FALSE;
	if(isset($_GET['bypass'])){
		$bypass = TRUE;
	}
	$green_room_updated = FALSE;

	//available options: production, dev, ua
	$environment = 'production';
	if($environment == 'dev'){
		$environment = FALSE;
	}	

	/**
	* Get the appropriate information
	**/

	$full_name = '';
	$user_type = $_SESSION['MM_UserTypeId']; //user type

	/* Get Brand Information */
	$brand_project = retrieve_brand_project($database_ifs, $ifs);
	$row_retBrandProject = NULL;
	if($brand_project){
		$row_retBrandProject = mysql_fetch_assoc($brand_project);
		$totalRows_retBrandProject = mysql_num_rows($brand_project);
	}

	/* Get Brand Properties */
	$session_name = '';
	if(isset($row_retBrandProject['Session_Name'])){
		$session_name = $row_retBrandProject['Session_Name'];
	}
	
	//Check whether session has been closed or not
	$closed = false;
	if(isset($row_retBrandProject['status_id']) && $row_retBrandProject['status_id'] == 2){
		$closed = true; //session is closed	
	}

	/* Logo URL */
	$brand_project_logo_url = NULL;

	//Set Start Date
	$start_date = '';	
	if(isset($row_retBrandProject['start_time'])){
		$start_date_unix = strtotime($row_retBrandProject['start_time']);
		$start_date = date('h:i a l d F Y', $start_date_unix);
	}

	//Set End Date
	$end_date = '';
	if(isset($row_retBrandProject['end_time'])){
		$end_date_unix = strtotime($row_retBrandProject['end_time']);
		$end_date = date('h:i a l d F Y', $end_date_unix);
	}
	
	//Set User Id
	$user_id = NULL;
	if(isset($_SESSION['MM_UserId'])){
		$user_id = $_SESSION['MM_UserId'];
	}	
	
	/* Get Facilitator Properties */
	$facilitator_name = '';
	$facilitator_email = '';
	$facilitator_phone = '';

	//If Brand Project Details are available
	if(isset($row_retBrandProject['user_id'])){
		$facil_user_id = $row_retBrandProject['user_id'];

		/* Get Facilitator */
		$facilitator_result = retrieve_users($database_ifs, $ifs, true, true, $facil_user_id);
		if(!empty($facilitator_result)){
			$facilitator = mysql_fetch_assoc($facilitator_result);

			$facilitator_name = $facilitator['name_first'];
			$facilitator_last_name = $facilitator['name_last'];
			$facilitator_email = $facilitator['email'];
			$facilitator_mobile = (isset($facilitator['mobile']) ? $facilitator['mobile'] : $facilitator['phone']);

			/* if the facilitator is the user */
			if($facil_user_id == $user_id){
				if(!$facilitator['green_room_visit']){ //Check if the facilitator has visited the green room before
					if(!check_if_avatar_default_state($facilitator, $user_id)){
						update_green_room_visit_status($database_ifs, $ifs, $user_id);
						$green_room_updated = TRUE;
					}
				} elseif(!$green_room_updated && $bypass) {
					$chat_url = $BASE_URL.'?id=' . $user_id . '&sid=' . $session_id;

					header(sprintf("Location: %s", $chat_url));
				}
			} elseif($user_type == 2){
				/* Send notification that they do not have permission to access this area */
				$_SESSION['notification'] = array(
					'You do not have permission to access this green room. Please contact the administrator.'
				);

				header("Location: " . $x_close_url);
			}
		} else { //A facilitator has not been set yet.
			$_SESSION['notification'] = array(
					'A facilitator has not been set. Please try again later'
			);

			header("Location: " . $x_close_url);
		}
	} else { //A facilitator has not been set yet.
		$_SESSION['notification'] = array(
				'A facilitator has not been set. Please try again later'
		);

		header("Location: " . $x_close_url);
	}

	//Close Green Room
	$x_close_url = '../index.php';
	if($user_type > 3){
		$x_close_url = '../logout.php';
	}	

	/* Check if start time is correct and if the session hasn't ended */
	if($user_type > 1 && $facil_user_id != $user_id){
		if(!$start_date || !$end_date || $closed){
			/* Send notification that neither a start or end date has been set */
			$_SESSION['notification'] = array(
				'Neither a start date or end date has been associated to this session. Please contact the administrator.'
			);

			header("Location: " . $x_close_url);
		} else {
			$current_time = time();
			if($current_time < $start_date_unix || $current_time > $end_date_unix){
				if($current_time < $start_date_unix){
					/* Send notification that the session hasn't started yet */
					$_SESSION['notification'] = array(
						'Sorry, but your session has not yet started yet! Please come back at the Date & Time on your email. Contact the Facilitator with any questions'			
					);
				} else {
					/* Send notification that the session has ended */
					$_SESSION['notification'] = array(
						'The session has unfortunately ended, please consider attending any future sessions that you are invited to'
					);
				}

				header("Location: " . $x_close_url);
			}
		}
	}	

	/* Get Participany Name */
	$participant_name = '';
	if(isset($_SESSION['MM_FirstName'])){
		$participant_name = $_SESSION['MM_FirstName'];
	}

	$participant_last_name = '';	

	/**
	* Make sure the user have to the green room
	**/
	if(!$user_id){
		/* Send notification that no user id was found */
		$_SESSION['notification'] = array(
			'A valid user was not found.'
		);

		header("Location: " . $x_close_url);
	} else {
		//Find out whether the user has visited the green room before
		$green_room_result = find_participant($database_ifs, $ifs, null, true, true, $user_id);

		if(!is_string($green_room_result)){
			/* If query is valid */
			if($green_room_result){
				$green_room_row = mysql_fetch_assoc($green_room_result); //green room row

				//Set participant first name
				if(isset($green_room_row['name_first'])){
					$participant_first_name = $green_room_row['name_first'];
				}

				//Set participant last name
				if(isset($green_room_row['name_last'])){
					$participant_last_name = $green_room_row['name_last'];
				}				

				/* Show to chat room */
				if($green_room_row['green_room_visit'] && $user_type == 5 && $bypass){
					$chat_url = $BASE_URL.'?id=' . $user_id . '&sid=' . $session_id;

					header(sprintf("Location: %s", $chat_url));
				} elseif($user_type == 5){ //if participant, but have not visited befor
					if(!check_if_avatar_default_state($green_room_row, $user_id)){ //check if avatar is at default state
						update_green_room_visit_status($database_ifs, $ifs, $user_id);
						$green_room_updated = TRUE;
					}
				}
			} elseif($user_type == 5){
				/* Send notification that they do not have permission to access this area */
				$_SESSION['notification'] = array(
					'You do not have permission to access this green room. Please contact the administrator.'
				);

				header("Location: " . $x_close_url);
			}
		} elseif($user_type == 5){
			/* Send notification that they do not have permission to access this area */
			$_SESSION['notification'] = array(
				'You do not have permission to access this green room. Please contact the administrator.'
			);

			header("Location: " . $x_close_url);
		}
	}
	
	//If the user is a facilitator, set as participant first name
	if($facilitator_name == $participant_name){
		$participant_name = $facilitator_name;
	}
	
	//If the user is a facilitator, set as participant last name
	if($facilitator_last_name == $participant_last_name){
		$participant_last_name = $facilitator_last_name;
	}		

	//Check if the user is part of the staff and if they've visted the green room before
	$staff_result = retrieve_users($database_ifs, $ifs, true, null, $user_id, false, false);

	//Make sure that the staff has visted the green room before
	if($staff_result && !is_string($staff_result)){
		$staff_user = mysql_fetch_assoc($staff_result);

		//Set green room row as staff
		if(!isset($green_room_row)){
			$green_room_row = $staff_user;
			$full_name = $green_room_row['name_first'] . ' ' . $green_room_row['name_last']; //set full name
		}

		if(!$staff_user['green_room_visit']){ //Check if the facilitator has visited the green room before
			if(!check_if_avatar_default_state($staff_user, $user_id)){
				update_green_room_visit_status($database_ifs, $ifs, $user_id);
				$green_room_updated = TRUE;
			}
		} elseif(!$green_room_updated && $bypass){
			$chat_url = $BASE_URL.'?id=' . $user_id . '&sid=' . $session_id;

			header(sprintf("Location: %s", $chat_url));
		}
	}

	$full_name = $green_room_row['name_first'] . ' ' . $green_room_row['name_last']; //set full name

	/**
	* Updating the Profile
	**/
	$update_message = null; //if update_message is shown
	$fields = array();	

	/* The update was successful */ 
	if(isset($_GET['update']) && $_GET['update']){
		$update_message = TRUE;
		$message = '<div id="notification"><p>The profile was updated successfully</p></div>';
	}

	//do the insert
	if(isset($_POST['btnSubmit'])){
			/* Prepare for notification */
			$update_message = TRUE;

			$update = update_user_profile($database_ifs, $ifs, $user_id, $green_room_row['user_login_id']);

			/* Update profile */
			if(is_array($update)){
				$message = $update['html'];
				$fields = $update['fields'];
			} else {
				$updateGoTo = str_replace('&update=1', '', $form_action) . '&update=1';
				header(sprintf("Location: %s", $updateGoTo));	
			}
	}

	//Load in Profile
	require_once("../views/green_room_profile.php");
	
