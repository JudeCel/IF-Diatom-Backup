<?php
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/users_model.php');
	
	if((!isset($_SESSION['MM_UserTypeId']) || $_SESSION['MM_UserTypeId'] > 3) || (!isset($_GET['json']))){
		mysql_close($ifs);

		header('Location: /users.php');
		die();
	}

	$json = json_decode($_GET['json']); //get json

	//Go through each role dor each user and activate and deactivate roles
	foreach($json as $user_id=>$roles){
		foreach($roles as $role){
			$type_id = $role->type_id;
			$active_status = $role->active_status;

			//Set session id
			$sid = null;
			if(isset($role->sid)){
				$sid = $role->sid;
			}

			//Update active
			$active_update = null;
			if($type_id && $user_id && isset($active_status)){
				$active_update = set_client_user_active_deactivated($database_ifs, $ifs, $user_id, $type_id, $active_status, $sid);
			}
		}		
	}

	mysql_close($ifs);

	header("Location: users.php");
	die();