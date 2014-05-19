<?php
	require_once("Connections/ifs.php");
	require_once('core.php');
	require_once("models/participant-email-model.php");
	require_once("models/users_model.php");

	if(admin($database_ifs, $ifs)) {
		/* If current_location doesn't macth the url */
		if($current_location != $form_action){
			mysql_close($ifs);

			header("Location: " . $current_location);
			die();
		}


		$user_type = $_SESSION['MM_UserTypeId']; //get user Type

		//Page properties
		$page = 'Home';
		$title = 'Overview';
		$main_script = 'home';
		$other_content = false;

		$grid = true;
		$validate = false;
		$inline_scripting = 'brand_project_inline';

		$sub_navigation = false;
		$sub_nav_url = false;
		$sub_id = null;
		$sub_group = null;


		$client_company_id = null;
		if(isset($_SESSION['MM_CompanyId'])){
			$client_company_id = $_SESSION['MM_CompanyId'];
		}

		if($user_type != 4 && $user_type != 5 && $user_type !== 0) {
			//Global Admin or Brand Facilitator
			$_SESSION['Home_URL'] = "home";

			/* Get message information */
			$message = null;
			if(isset($_SESSION['notification'])){
				$message_val = new StdClass;
				$message_val->other = $_SESSION['notification'];

				$message = process_messages($message_val);

				unset($_SESSION['notification']);
			}

			// echo "-----------------<BR/>user_type: ";
			// print_r($user_type);
			// echo "<BR/>";
			// echo "-----------------<BR/>database_ifs: ";
			// print_r($database_ifs);
			// echo "<BR/>";
			// echo "-----------------<BR/>ifs: ";
			// print_r($ifs);
			// echo "<BR/>";
			// echo "-----------------<BR/>user_login_id: ";
			// print_r($user_login_id);
			// echo "<BR/>";
			// echo "-----------------<BR/>staff: ";
			// print_r($staff);
			// echo "<BR/>";

			// exit();

			include 'views/home.php';

		} elseif($user_type == 4 || $user_type == 5) {	//Observer or Particiapnt
			$user_login_id = $_SESSION['MM_UserLoginId']; //user login id
			$user_id = $_SESSION['MM_UserId'];

			//Set staff information
			$staff = false;
			if($user_type == 4) { //the user is an observer
				$staff = true;
			}

			//Session information
			/*
			echo "-----------------<BR/>user_type: ";
			print_r($user_type);
			echo "<BR/>";
			echo "-----------------<BR/>database_ifs: ";
			print_r($database_ifs);
			echo "<BR/>";
			echo "-----------------<BR/>ifs: ";
			print_r($ifs);
			echo "<BR/>";
			echo "-----------------<BR/>user_login_id: ";
			print_r($user_login_id);
			echo "<BR/>";
			echo "-----------------<BR/>staff: ";
			print_r($staff);
			echo "<BR/>";
			*/

			$session_info = get_session_id_by_user($database_ifs, $ifs, $user_login_id, $staff);

			//If the session was found
			if($session_info && !is_string($session_info)) {
				$session_row = mysql_fetch_assoc($session_info);
				$session_id = $session_row['session_id'];

				//Set green room url
				$green_room_url = $ADMIN_URL."IFS/index.php?session_id=".$session_id;

				if ($staff){
					$green_room_url = $BASE_URL.'?id=' . $user_id . '&sid=' . $session_id;
				}

				mysql_close($ifs);

				header("Location: " . $green_room_url);
				die();
			} else {
				mysql_close($ifs);

				$_SESSION['notification'] = array(
					'Sorry, this Session has not yet started, or has now closed. Please check the dates &amp; times, or contact your Facilitator.'
				);

				header("Location: logout.php");
				die();
			}
		}
	} else {
		mysql_close($ifs);

		header("Location: login.php");

		die();
	}

	mysql_close($ifs);
