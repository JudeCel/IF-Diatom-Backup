<?php	
	include('Connections/ifs.php');

	require_once('core.php');
	require_once('models/users_model.php');
	require_once('models/participant-email-model.php');

	$message_val = new stdClass;
	$message_val->other = array();
	$message_val->fields = array();
	$fields = array();
	$notification = '';

	/* Type of User */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	/* Ensure that the user has access to these pages */
	if(!admin($database_ifs, $ifs) || ($user_type != -1 && $user_type != 1)){
		if(!$user_type){
			$_SESSION['notification'] = 'You are logged out, please login again.';
		} else {
			$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
		}

		mysql_close($ifs);

		header("Location: index.php");

		die();
	}

	/* Set the type id for setting the role */
	$type_id = null;
	if(isset($_GET['type_id'])){
		$type_id = strip_tags(mysql_real_escape_string($_GET['type_id']));
	}	

	//Page properties
	$page = 'Staff User Insert';
	$title = 'Add Staff User';
	$main_script = 'client_company_users_insert';
	$other_content = 'client_company_users_insert';
	$validate = true;
	$inline_scripting = false;
	
	$import = false;
	$import_page = null;
	$email_sent = true;

	/* Change Title of page if type id exists */
	if($type_id){
		switch($type_id){
			case 1:
				$title = 'Register New Global Admin';
			break;
			case 2:
				$title = 'Register New Facilitator';
				
				/* DISABLE IMPORT FOR NOW */
				$import = false; //use to bring in add session
				$import_page = 'newSession-insert.php';

				/* Legends */
				$user_legend = 'Facilitator Details';
				$session_legend = 'Session Details';
			break;
			case 4:
				$title = 'Register New Observer';				
			break;
		}
	}

	//retrieve user types
	mysql_select_db($database_ifs, $ifs);
	$query_retUserTypes = "SELECT * FROM client_user_types";

	$retUserTypes = mysql_query($query_retUserTypes, $ifs) or die(mysql_error());

	$totalRows_retUserTypes = 0;

	//If there are results and the query was successful
	if($retUserTypes){
		$totalRows_retUserTypes = mysql_num_rows($retUserTypes);
	}

	//Set the client company id
	$client_company_id = null;
	if(isset($_GET['client_company_id'])){
		$client_company_id =	strip_tags(mysql_real_escape_string($_GET['client_company_id']));
	}

	//set the brand project id
	$brand_project_id = null;
	if(isset($_GET['brand_project_id'])){
		$brand_project_id =	strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
	}

	//set the session id
	$session_id = null;
	if(isset($_GET['session_id'])){
		$session_id =	strip_tags(mysql_real_escape_string($_GET['session_id']));
	} elseif($type_id && $type_id == 4){
		$email_sent = false;
	}

	$update_message = null; //if update_message is shown
	$fields = array();

	/* The update was successful */ 
	if(isset($_GET['update']) && $_GET['update']){
		/* Change message if type id exists */
		if($type_id){
			switch($type_id){
				case 1:
					$message_val->other[] = 'A Global Administrator was successfully created';
				break;
				case 2:
					$message_val->other[] = 'A Potential Facilitator was successfully created';
					$email_sent = false;
				break;
				case 4:
					$message_val->other[] = 'A' . (!$session_id ? ' Potential' : 'n') . ' Observer was successfully created';
				break;
			}
		} else {
			$message_val->other[] .= 'A Staff User was successfully created';
		}

		//Set message
		$_SESSION['notification'] = array();
		$_SESSION['notification'][] = process_messages($message_val);

		//close fancybox
		echo '<script type="text/javascript" src="js/fancybox_close.js" />';
	}

	//Input Values
	/* get data	*/		
	$name_first = (isset($_POST['name_first']) ? htmlentities(mysql_real_escape_string($_POST['name_first'])) : ''); //First Name		
	$name_last = (isset($_POST['name_last']) ? htmlentities(mysql_real_escape_string($_POST['name_last'])) : ''); //Last Name		
	$gender = (isset($_POST['gender']) ? htmlentities(mysql_real_escape_string($_POST['gender'])) : ''); //Gender	
	$job_title = (isset($_POST['job_title']) ? htmlentities(mysql_real_escape_string($_POST['job_title'])) : ''); //Job Title
	$email = (isset($_POST['email']) ? htmlentities(mysql_real_escape_string($_POST['email'])) : ''); //Email
	$mobile = (isset($_POST['mobile']) ? htmlentities(mysql_real_escape_string($_POST['mobile'])) : ''); //Mobile
	$phone = (isset($_POST['phone']) ? htmlentities(mysql_real_escape_string($_POST['phone'])) : ''); //Phone
	$fax = (isset($_POST['fax']) ? htmlentities(mysql_real_escape_string($_POST['fax'])) : ''); //Fax
    $uses_landline = (isset($_POST['uses_landline']) ? htmlentities(mysql_real_escape_string($_POST['uses_landline'])) : ''); //Fax

	//do the insert for 
	if(isset($_POST['btnSubmit']) && $client_company_id){
		/* Prepare for notification */
		$update_message = TRUE;		

		//Set password
		$new_password = create_unique_password();
		$password = array(
			'password' => $new_password,
			'hashed' => md5($new_password)
		);

		/* Include type_id if adding observers */
		if(($type_id && $type_id != 4) || !$type_id){
			$update = update_user_profile($database_ifs, $ifs, null, null, ($type_id == 1 ? null : $client_company_id), $password);
		} else {
			$update = update_user_profile($database_ifs, $ifs, null, null, $client_company_id, $password, null, $type_id, $brand_project_id);
		}		

		$user_id = $update;

		if(!$import){
			$updateGoTo = $form_action . "&update=1"; //redirected url
	
			/* If a type id was defined */
			if($type_id && is_numeric($update)){
				switch($type_id){
					case 1:
						$updateGoTo = sprintf('clientCompanyUsers-select.php?client_company_id=%d&user_id=%d', $client_company_id, $update);
					break;
					case 2:
						if($brand_project_id){
							$updateGoTo = sprintf('newSession-insert.php?brand_project_id=%d&user_id=%d', $brand_project_id, $update);
						}
					break;
					case 4:
						if($session_id && !$brand_project_id){
							$updateGoTo = sprintf('session-edit-insert.php?session_id=%d&user_id=%d', $session_id, $update);
						}
					break;
				}
			}			
		}		
		

		/* Set messages or redirect */
		if(is_array($update)){
			/* Set message */
			$message = $update['html'];

			if($import){
				$notification = $message;
				$message = '';
			}

			//If there are items witin the field array
			$fields = $update['fields'];
		} elseif(!$import) {
			if($ifs){
				mysql_close($ifs);
			}

			header(sprintf("Location: %s", $updateGoTo));
			die();	
		}	
	}

	mysql_close($ifs);

	require_once('views/popup.php');