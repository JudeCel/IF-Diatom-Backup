<?php
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/participant-email-model.php');
	require_once('models/users_model.php');
	require_once('models/brand_model.php');
	require_once('classes/image_upload_model.php');
	
	$message = NULL;
	
	if(isset($_SESSION['notification'])){
		$message = $_SESSION['notification'];
		unset($_SESSION['notification']);	
	}
	
	$session_id = NULL;
	if(isset($_GET['session_id'])){
		$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));	
	}
	
	$email_type_id = NULL;
	if(isset($_GET['email_type_id'])){
		$email_type_id = strip_tags(mysql_real_escape_string($_GET['email_type_id']));	
	}	

	/* Set if updated */
	$update_value = '';
	$updated = FALSE;
	if(isset($_GET['update'])){
		$update_value = strip_tags(mysql_real_escape_string($_GET['update']));
		$updated = TRUE;
	}

	/* Get email details */
	$details = get_details_for_template($email_type_id);
	$details_num = 0;
	if(!empty($details)){
		$details_num = count($details);
	}
	
	/* Find specific email template */
	$row_retSessionEmail = NULL;
	$retSessionEmail = retrieve_email_template($session_id, $email_type_id, $database_ifs, $ifs);

	
	$totalRows_retSessionEmail = 0;
	if($retSessionEmail){
		$row_retSessionEmail = mysql_fetch_assoc($retSessionEmail); //get email template
		$totalRows_retSessionEmail = mysql_num_rows($retSessionEmail); //number of rows available
	}
	

	/* Get Brand Information */
	$brand_project = retrieve_brand_project($database_ifs, $ifs);
	$row_retBrandProject = NULL;
	if($brand_project){
		$row_retBrandProject = mysql_fetch_assoc($brand_project);
		$totalRows_retBrandProject = mysql_num_rows($brand_project);
	}

	/* Get Facilitator Properties */
	$facilitator_firstname = '';
	$facilitator_lastname = '';
	$facilitator_phone = '';
	$facilitator_email = '';
	$brand_name = '';

	if(isset($row_retBrandProject['user_id'])){
		/* Get Facilitator */
		$facilitator_result = retrieve_users($database_ifs, $ifs, true, true, $row_retBrandProject['user_id']);
		if(!empty($facilitator_result)){
			$facilitator = mysql_fetch_assoc($facilitator_result);

			$facilitator_firstname = $facilitator['name_first'];
			$facilitator_lastname = $facilitator['name_last'];
            $facilitator_phone = ($facilitator['uses_landline'] ?  $facilitator['phone'] : $facilitator['mobile']);
			$facilitator_email = $facilitator['email'];
		}
	}

	if(isset($row_retBrandProject['Session_Name'])){
		$brand_name = $row_retBrandProject['Session_Name'];
	}

	$submit = FALSE;
	/* If uploading image */
	if(isset($_POST['btnImage_x'])){
		$updated = update_email_image($row_retSessionEmail['id'], 'email_image', $database_ifs, $ifs);
				
		/*  Goto after uplaod */
		if(!is_string($updated) && $updated){
			goto_after_upload($updated, $session_id, $email_type_id, '2');
		} else {
			$message = $updated;
		}
	}

	/* If embedding video */
	if(isset($_POST['btnVideo_x'])){
		/* If processed through ajax */
		if(isset($_GET['ajax'])){
			$updated = update_email_video($row_retSessionEmail['id'], $database_ifs, $ifs, $retSessionEmail);
			$json = new StdCLass();
			
			if(!is_string($updated) && $updated){
				$json->success = TRUE;
				$json->message = 'The Youtube video details have been saved and will appear as a link inside the e-mail';
			} else {
				$json->success = FALSE;
				$json->message = 'The error is the following, please contact your administrator: ' . $updated;
			}

			echo json_encode($json);

			die();
		} else {
			$submit = TRUE;	
		}
	}

	/* Update template if template is updated */
	if(((isset($_POST['btnSubmit_x']) && isset($_POST['btnSubmit_y'])) || 
		 ($submit) ||
		 (isset($_POST['btnPreview_x']) && isset($_POST['btnPreview_y'])))){

		$updated = update_email_template($row_retSessionEmail['id'], $database_ifs, $ifs, $retSessionEmail);
			
		/*  Goto after uplaod */
		if(!is_string($updated) && $updated){
			goto_after_upload($updated, $session_id, $email_type_id);
		} else {
			$message = $updated;
		}
	}
	
	/*Display message*/
	if(isset($_SESSION['notification'])){
		if(!$message){
			$message = '';	
		}
		
		$message .= "\n" . $_SESSION['notification'];
	}	

	//Load in view
	require_once('views/email_template_admin.php');

	mysql_close($ifs);
