<?php
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/users_model.php');
	require_once('models/participant-email-model.php');

if(isset($_POST['btnHome']))
{
    header('Location: ' . $ADMIN_URL."index.php");
    die();
}

	$change = null;

	//Initialise the found users variables
	$found_users = process_user_accounts(null); //initialise user accounts

	//Set up the notification system
	$messages = new StdClass;
	$messages->other = array();
	$notifications = null;

	$reset_found = false;
	$username = '';
	$email_array = false;

	//Page properties
	$page = 'Forgot Password';
	$title = $page;
	$main_script = false;
	$other_content = 'forgot_password';
	
	$grid = false;
	$validate = false;
	$inline_scripting = false;

	$sub_navigation = false;
	$sub_nav_url = false;
	$sub_id = null;
	$sub_group = null;

	//Set usable user types
	$user_types = array(
		-1 => 'IFS Admin',
		1 => 'Global Admin',
		2 => 'Facilitator',
		3 => 'Facilitator',
		4 => 'Observer'
	);

	//Get username
	if(isset($_POST['email'])){
		$username = strip_tags(mysql_real_escape_string($_POST['email']));
	} elseif(isset($_GET['email'])){ //Get username by get
		$username = strip_tags(mysql_real_escape_string($_GET['email']));
		$_POST = array(); //ensure that POST values cannot be used		
	}

	if($username){
		if(!validate_user_email($username)){
			$username = null;
		}
	}	

	//Find the user accounts to reset
	if(isset($_POST['btnSubmit'])){
		if($username){
			$user = retrieve_user_by_email($database_ifs, $ifs, $username); //get user

			//Check if user was found
			if($user && !is_string($user)){
				$found_users = process_user_accounts($user); //understand the user's accounts

				$reset_found = true;
			} else {
				$messages->other[] = 'No user found that matches that username/email address.';
			}	
		} else {
			$messages->other[] = 'Please enter a valid e-mail address.';
		}
	}

	//Reset the user accounts
	if(isset($_POST['send_new'])){
		//Check if a staff member needs to be reset
		if(isset($_POST['staff_reset'])){
			$staff_reset = $_POST['staff_reset']; //user login id
			
			if($staff_reset != 'none'){
				//Request a change to the password
				if(request_change_to_password($database_ifs, $ifs, $username, $staff_reset)){
					$messages->other[] = 'An e-mail has been sent to confirm your request for a new password';
				} else {
					$messages->other[] = 'There was a problem sending your e-mail. Please contact the administartor';
				}
			}		
		}

		//Check if an observer needs to be reset
		if(isset($_POST['observer_reset'])){
			$observer_reset = $_POST['observer_reset'];

			//Request a change to the password
			if($observer_reset != 'none'){
				if(request_change_to_password($database_ifs, $ifs, $username, $observer_reset)){
					$messages->other[] = 'An e-mail has been sent to confirm your request for a new password';
				} else {
					$messages->other[] = 'There was a problem sending your e-mail. Please contact the administartor';
				}
			}
		}

		//Check if a participant needs to be reset
		if(isset($_POST['participant_reset'])){
			$participant_reset = strip_tags($_POST['participant_reset']);

			//Request a change to the password
			if($participant_reset != 'none'){
				if(request_change_to_password($database_ifs, $ifs, $username, $participant_reset)){
					$messages->other[] = 'An e-mail has been sent to confirm your request for a new password.';
				} else {
					$messages->other[] = 'There was a problem sending your e-mail. Please contact the administartor.';
				}
			}
		}
	}

	if(isset($_GET['key']) && $username){ //reset password using key
		$key = strip_tags(mysql_real_escape_string($_GET['key']));

		if($user_login_id = check_key_for_reset_password($database_ifs, $ifs, $key, $username)){

			$new_password = create_unique_password(); //password
			$reset = reset_password($database_ifs, $ifs, $username, $user_login_id, $new_password, $messages); //reset password and compile message

			//Set properties
			$messages = $reset->message;
			$user_id = $reset->user_id;
			$full_name = $reset->full_name;
			$replacements = $reset->replacements;

			//Send email
			if($reset->status){
				send_admin_email_to_user($database_ifs, $ifs, $username, $user_id, $full_name, 'Your Password for '.$replacements['Activity Name'].' has been Reset', 'forget_password', $replacements);
				//Set notification
				if(isset($messages->other)){
					$_SESSION['notification'] = $messages->other;
				}
			}

		} else
            $messages->other[] = 'This password has already been reset. Please check your inbox.';
	}

	$message = process_messages($messages); //prepare output for messages

	require_once('views/not_logged_in.php');

	mysql_close($ifs);