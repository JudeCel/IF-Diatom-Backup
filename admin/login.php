<?php
require_once('Connections/ifs.php');
require_once('core.php');
require_once('models/users_model.php');
require_once('talkToNode.php');


// *** Validate request to login to this site.
if (!isset($_SESSION)) {
    session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = strip_tags(mysql_real_escape_string($_GET['accesscheck']));
}

if (isset($_POST['username'])) {
  $login = true;

  $loginUsername = strip_tags(mysql_real_escape_string($_POST['username']));
  $password1= strip_tags(mysql_real_escape_string($_POST['password']));
  $password = md5($password1);
  $MM_fldUserAuthorization = "";

  //Set redirection for post-login
  if($current_location != $form_action && isset($_SESSION['current_location'])){
		$MM_redirectLoginSuccess = $current_location;

		unset($_SESSION['current_location']); //unset session variable
	} else {
		$MM_redirectLoginSuccess = $ADMIN_URL."index.php";
	}

  //URL for failed login
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;


    $params =new stdClass();
    $params->login=$loginUsername;
    $params->password=$password;
    talkToNode( "/getuserloginRS", $params, $LoginRS);

  //Make sure that the query was successful
  if($LoginRS){
  	$loginFoundUser = sizeof($LoginRS);

  	if($loginFoundUser){
	  	$row_retUser = json_decode($LoginRS,true);

			//If user id is available
		  $user_id_ifs = null;
		 	if(!is_null($row_retUser['id'])){
		 		$user_id_ifs = $row_retUser['id'];
		 	}

		  //If login found user matches a certain number, clear the login group
		  if($loginFoundUser == 1){
		     $loginStrGroup = "";
		  }

			//Refresh the session
			if(PHP_VERSION >= 5.1){
				session_regenerate_id(true);
			} else {
				session_regenerate_id();
			}

		  //declare two session variables and assign them
		  $_SESSION['MM_Username'] = $loginUsername;
		  $_SESSION['MM_UserGroup'] = $loginStrGroup;

			//Set user name
			$user_name = 'User';
			if(!is_null($row_retUser['name_first'])){
				$user_name = $row_retUser['name_first'];
			}
			$_SESSION['MM_FirstName'] = $user_name;




            talkToNode( "/getUserAdminity", $params, $globaladmin);

			$globaladmin_num_row = 0;

			//If query was successful, set the number of global admin
			if($globaladmin){
				$globaladmin_num_row = sizeof($globaladmin);
			}

        talkToNode( "/getUserModity", $params, $sessionMod);
        $sessionMod_row = 0;

			//If query was successful, set the number of facilitators
			if($sessionMod){
				$sessionMod_row = sizeof($sessionMod);
			}

			//check for observers

            talkToNode( "/getUserObservity", $params, $observer);
			$observer_row = 0;

			//If query was successful, set the number of observers
			if($observer){
				$observer_row = sizeof($observer);
			}

			//check for participants
            talkToNode( "/getUserParticipity", $params, $participant);
			$participant_row = 0;

			//If query was successful, set the number of participants
			if($participant){
				$participant_row = sizeof($participant);
			}

			$current_time = time();

			if($globaladmin_num_row > 0){ //global admin found
				$UserTypeId = 1; //user type id

				$globaladmin_info = json_decode($globaladmin,true);

				//Quickly find results for ids
				$CompanyId = $globaladmin_info['client_company_id'];
				$UserId = $globaladmin_info['user_id'];
				$start_date = strtotime($globaladmin_info['start_date']);
				$end_date = strtotime($globaladmin_info['end_date']);

				/* If the lease date hasn't been activated or has expired */
				if($current_time < $start_date || $current_time > $end_date){
					if($current_time < $start_date){
						/* Send notification that the client company's lease hasn't been activated */
						$_SESSION['notification'] = array(
							'Sorry, but your company\'s lease hasn\'t been activated yet! Please contact your administrator.'
						);
					} else {
						/* Send notification that he client company's lease has has expired */
						$_SESSION['notification'] = array(
							'Sorry, but your company\'s lease has expired! Please contact your administrator.'
						);
					}


					header("Location: logout.php");
					die();
				}

				//Set session information
				$_SESSION['MM_UserId'] = $UserId;
				$_SESSION['MM_UserTypeId'] = $UserTypeId;
				$_SESSION['MM_CompanyId'] = $CompanyId;

			} elseif($sessionMod_row > 0){ //facilitator found
				$UserTypeId = 3; //user type id

                $sessionmod_info  = json_decode($sessionMod,true);

				//Quickly find results for ids
				$CompanyId = $sessionmod_info['client_company_id'];
				$UserId = $sessionmod_info['user_id'];
				$start_date = strtotime($sessionmod_info['start_date']);
				$end_date = strtotime($sessionmod_info['end_date']);

				/* If the lease date hasn't been activated or has expired */
				if($current_time < $start_date || $current_time > $end_date){
					if($current_time < $start_date){
						/* Send notification that the client company's lease hasn't been activated */
						$_SESSION['notification'] = array(
							'Sorry, but your company\'s lease hasn\'t been activated yet! Please contact your administrator.'
						);
					} else {
						/* Send notification that he client company's lease has has expired */
						$_SESSION['notification'] = array(
							'Sorry, but your company\'s lease has expired! Please contact your administrator.'
						);
					}


					header("Location: logout.php");
					die();
				}

				//Set session information
				$_SESSION['MM_UserId'] = $UserId;
				$_SESSION['MM_UserTypeId'] = $UserTypeId;
				$_SESSION['MM_CompanyId'] = $CompanyId;

			} elseif($observer_row > 0){ //observer found
				$UserTypeId = 4; //user type id

                $observer_info  = json_decode($observer,true);

				$CompanyId = $observer_info['client_company_id'];
				$UserId = $observer_info['user_id'];

				//Logout user if the user login does not match
				if($observer_info['ul_id'] != $observer_info['user_login_id']){
					$_SESSION['notification'] = array(
						'Sorry, this Password is not valid for your current Session that you are registered for.
						Please use the password that was included in your invitation e-mail.'
					);


					header('Location: ' . $MM_redirectLoginFailed);
					die();
				} else {
					//Set session information
					$_SESSION['MM_UserId'] = $UserId;
					$_SESSION['MM_UserTypeId'] = $UserTypeId;
					$_SESSION['MM_CompanyId'] = $CompanyId;
					$_SESSION['MM_UserLoginId'] = $observer_info['user_login_id'];
				}
			} elseif($participant_row > 0){
				$participant_info = json_decode($participant,true); //get participant info

				$UserTypeId = 5; //user type id

				$CompanyId = $participant_info['client_company_id'];
				$UserId = $participant_info['user_id'];

				//Logout user if the user login does not match
				if($participant_info['ul_id'] != $participant_info['user_login_id']){
					$_SESSION['notification'] = array(
						'Sorry, this Password is not valid for your current Session that you are registered for.
						Please use the password that was included in your invitation e-mail.'
					);


					header('Location: ' . $MM_redirectLoginFailed);
					die();
				} else {
					//Set session information
					$_SESSION['MM_UserId'] = $UserId;
					$_SESSION['MM_UserTypeId'] = $UserTypeId;
					$_SESSION['MM_CompanyId'] = $CompanyId;
					$_SESSION['MM_UserLoginId'] = $participant_info['user_login_id'];
				}
			} elseif($row_retUser['ifs_admin']) {
				$UserTypeId = -1; //IFS admin
				$CompanyId = -1;

				//Set session information
				$_SESSION['MM_UserId'] = $user_id_ifs;
				$_SESSION['MM_UserTypeId'] = -1;
				$_SESSION['MM_CompanyId'] = $CompanyId;
			} else {
				$_SESSION['notification'] = array(
					'You do not have a valid login.'
				);


				header("Location: logout.php");
				die();
			}

			//Initialise values for database query
			$row_retClientCompanyLogo = array();
			$totalRows_retClientCompanyLogo = 0;

			if($CompanyId != -1){
				//retrieve client company logo details
                $params =new stdClass();
                $params->id=$CompanyId;
                talkToNode( "/getClientCompanyLogo", $params, $retClientCompanyLogo);


				//If the query was successful, set the appropriate number of records found
				if($retClientCompanyLogo){
					$row_retClientCompanyLogo = json_decode($retClientCompanyLogo,true);
					$totalRows_retClientCompanyLogo = sizeof($retClientCompanyLogo);
				}
			}

			$_SESSION['session_logo'] = $ADMIN_URL . "images/logoDefaultInsiderfocus.jpg";
			//If the client logo was found
			if($totalRows_retClientCompanyLogo) {
				if(! is_null($row_retClientCompanyLogo['client_company_logo_url'])){
					$_SESSION['session_logo'] = $row_retClientCompanyLogo['client_company_logo_url'];
				}
			}



			header('Location: ' . $MM_redirectLoginSuccess);
			die();
		}	else {
			$login = false;
		}
	} else {
		$login = false;
	}

	if(!$login){
		//Check if the otification session has been set
		if(!isset($_SESSION['notification'])){
			$_SESSION['notification'] = array();
		}

		$_SESSION['notification'][] = 'No matching user found';
	}

}

/* Get message information */
$message = null;
if (isset($_SESSION['notification'])) {
    $message_val = new StdClass;
    $message_val->other = $_SESSION['notification'];

    $message = process_messages($message_val);

    unset($_SESSION['notification']);
}

//Display view
require_once('views/login.php');
