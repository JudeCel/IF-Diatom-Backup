<?php	
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/users_model.php');

	$message = array();

	/* Type of User */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	if(admin($database_ifs, $ifs) && $user_type >= -1 && $user_type <= 5){
		$update_message = null; //if update_message is shown
		$fields = array();

		//Page properties
		$page = 'Profile';
		$title = $page;
		$main_script = null;
		$other_content = 'profile';

		$grid = false;
		$validate = true;
		$inline_scripting = false;

		$sub_navigation = false;
		$sub_id = null;
		$sub_group = null;
	
		/* User id */
		$user_id = null;
		if(isset($_SESSION['MM_UserId'])){
			$user_id = $_SESSION['MM_UserId'];	
		}

		/* Get user data */
		$retClientUser = null;		
		if($user_id && is_numeric($user_id)){
			$retClientUser = retrieve_users($database_ifs, $ifs, false, null, $user_id, false, null, true);
			$totalRows_retClientUser = 0;
			$row_retClientUser = array();
		}

		if(!$retClientUser || ($retClientUser && is_string($retClientUser))){
			$message[] = 'Your user data was not found'; //set message
			$_SESSION['notification'] = $message;

			mysql_close($ifs);

			header("Location: logout.php");
			die();
		}

		$row_retClientUser = mysql_fetch_assoc($retClientUser);
		$totalRows_retClientUser = mysql_num_rows($retClientUser);

		$user_login_id = $row_retClientUser['user_login_id'];

		/* The update was successful */ 
		if(isset($_SESSION['update']) && $_SESSION['update']){
			$update_message = TRUE;
			$message = '<p>The profile was updated successfully</p>';
			unset($_SESSION['update']);
		}

		//do the insert
		if(isset($_POST['btnSubmit']))
		{
				/* Prepare for notification */
				$update_message = TRUE;	

				$update = update_user_profile($database_ifs, $ifs, $user_id, $user_login_id);

				/* Update profile */
				if(is_array($update)){
					$message = $update['html'];
					$fields = $update['fields'];
				} else {
					$_SESSION['update'] = true;

					mysql_close($ifs);

					$updateGoTo = "profile.php";
					header(sprintf("Location: %s", $updateGoTo));	
					die();
				}			
		}

		require_once('views/home.php');
	} else {
		if(!$user_type){
			$_SESSION['notification'] = 'You are logged out, please login again.';
		} else {
			$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
		}

		mysql_close($ifs);

		header("Location: logout.php");
		die();
	}

	mysql_close($ifs);