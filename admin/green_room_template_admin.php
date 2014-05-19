<?php
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/green_room_model.php');

	/* Get session information */
	$session_id = NULL;
	if(isset($_GET['session_id'])){
		$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));	
	} else {
		mysql_close($ifs);

		header('Location: index.php'); //return to main page
		die();	
	}

	/* Get Green Room Information */
	$green_room_session = NULL;

	$green_room_details = retrieve_green_room_information($database_ifs, $ifs, $session_id);
	
	if($green_room_details && !is_string($green_room_details)){
		$green_room_session = mysql_fetch_assoc($green_room_details); //get green room session information
	}

	/* Set if updated */
	$update_value = '';
	$updated = FALSE;
	if(isset($_GET['update'])){
		$update_value = strip_tags(mysql_real_escape_string($_GET['update']));
		$updated = TRUE;
	}

	/* Update template if template is updated */
	if(((isset($_POST['btnSubmit_x']) && isset($_POST['btnSubmit_y'])) ||
		 (isset($_POST['btnPreview_x']) && isset($_POST['btnPreview_y'])))){

		$updated = update_green_room($database_ifs, $ifs, $session_id, ($green_room_session ? 1 : 0));

		if($updated && !is_string($updated)){
			$updateGoTo = $form_action;

			//Check if should preview			
			if(isset($_POST['btnPreview_x']) && isset($_POST['btnPreview_y'])){
				$root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

				//Check if root includes ifs-test
				if(preg_match('/^\/ifs-test/', $_SERVER['REQUEST_URI'])){
					$root .= '/ifs-test';
				}

				$updateGoTo = $root . '/IFS/index.php?session_id=' . $session_id . '&preview=1';
			}

			mysql_close($ifs);

			header(sprintf('Location: %s', $updateGoTo));
			die();
			
		} elseif(is_string($updated)) {
			$message = $updated; //display message
		}

	}	

	//Load in view
	require_once('views/green_room_template_admin.php');

	mysql_close($ifs);