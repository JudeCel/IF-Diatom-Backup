<?php 
require_once('Connections/ifs.php');     
require_once('core.php'); 
require_once('getage.php');
require_once('models/participant-email-model.php');
require_once('models/users_model.php');
require_once('models/array_repository_model.php');

/* Get user type id */
$user_type = null;
if(isset($_SESSION['MM_UserTypeId'])){
	$user_type = $_SESSION['MM_UserTypeId'];
}

if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
	/* Get user id */
	$user_id = 0;
	if(isset($_SESSION['MM_UserId']['user_id'])){
		$user_id = strip_tags(mysql_real_escape_string($_SESSION['MM_UserId']));
	}

	$session_id = NULL;
	if(isset($_GET['session_id'])){
		$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
	}

	$ajax = false;
	if(isset($_GET['ajax'])){
		$ajax = true;
	}		

	//Page properties
	$main_script = 'session_close';
	$other_content = 'session_close';
	$grid = false;
	$validate = false;
	$inline_scripting = 'session_close_inline';
	
	//Only initialisation - if client company id is available, it is set further down
	$page = 'Sessions | Session Close';
	$title = 'Sessions';
	
	$sub_id = null;
	$sub_group = null;
	$sub_navigation = null;
	
	$footer = 'session_footer';

	//retrieve user types
	mysql_select_db($database_ifs, $ifs);
	$query_retUserTypes = "SELECT * FROM client_user_types WHERE id NOT IN (1,2)";
	
	$retUserTypes = mysql_query($query_retUserTypes, $ifs) or die(mysql_error());
	$totalRows_retUserTypes = 0;
	
	if($retUserTypes){
		//$row_retUserTypes = mysql_fetch_assoc($retUserTypes);
		$totalRows_retUserTypes = mysql_num_rows($retUserTypes);
	}		

	//retrieve the session staff info 
	mysql_select_db($database_ifs, $ifs);
	$query_retSessionInfo = "
	SELECT 
	  sessions.name,
	  sessions.status_id,
	  sessions.start_time,
	  sessions.end_time,
	  sessions.brand_project_id,
	  brand_projects.client_company_id,
	  client_companies.max_number_of_observers
	FROM
	  sessions
	  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
	  INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
	WHERE
	  sessions.id=$session_id
	";

	$retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error());
	
	$totalRows_retSessionInfo = 0;
	$row_retSessionInfo = array();
	$brand_project_id = null;
	$client_company_id = null;
	$max_number_of_observers = 0;

	/* If the query found something/was successful */
	if($retSessionInfo){
		$row_retSessionInfo = mysql_fetch_assoc($retSessionInfo);
		$totalRows_retSessionInfo = mysql_num_rows($retSessionInfo);

		$brand_project_id = $row_retSessionInfo['brand_project_id'];
		$client_company_id = $row_retSessionInfo['client_company_id'];	

		$max_number_of_observers = $row_retSessionInfo['max_number_of_observers'];
	}

	//retrieve the session moderator
	mysql_select_db($database_ifs, $ifs);
	$query_retSessionMod = "
	SELECT 
	  session_staff.user_id,
	  session_staff.session_id,
	  users.name_first,
	  users.name_last
	FROM
	  session_staff
	  INNER JOIN users ON (session_staff.user_id = users.id)
	 WHERE
	   session_staff.session_id=".$session_id." 
	   AND session_staff.type_id=2
	";
	$retSessionMod = mysql_query($query_retSessionMod, $ifs) or die(mysql_error());
	$row_retSessionMod = array();
	$totalRows_retSessionMod = 0;

	if($retSessionMod){
		$row_retSessionMod = mysql_fetch_assoc($retSessionMod);
		$totalRows_retSessionMod = mysql_num_rows($retSessionMod);
	}

	//retrieve the session participants
	mysql_select_db($database_ifs,$ifs);
	$query_retSessionParticipant=" 
	SELECT 
	  users.name_first,
	  users.name_last,
	  users.email,
	  users.phone,
	  users.fax,
	  users.mobile,
	  users.job_title,
	  users.Gender,
	  users.invites,
	  addresses.street,
	  addresses.post_code,
	  addresses.suburb,
	  addresses.state,
		participants.id AS `pid`,
	  participants.dob,
	  participants.ethnicity,
	  participants.occupation,
	  participants.brand_segment,
	  participant_lists.id,
	  participant_lists.participant_reply_id,
	  participant_lists.participant_rating_id,
	  participant_lists.comments,
	  participants.invite_again
	FROM
	  participants
	  INNER JOIN users ON (participants.user_id = users.id)
	  LEFT OUTER JOIN addresses ON (users.address_id = addresses.id)
	  INNER JOIN participant_lists ON (participants.id = participant_lists.participant_id)
	WHERE 
	  participant_lists.session_id=".$session_id."
	 ";

	$retSessionParticipant = mysql_query($query_retSessionParticipant,$ifs) or die(mysql_error());
	$totalRows_retSessionParticipant = 0;
	$participants = array();

	if($retSessionParticipant){
		$totalRows_retSessionParticipant = mysql_num_rows($retSessionParticipant);
	}

	//Add all particpants id to participants array
	if($totalRows_retSessionParticipant){
		while($row_retSessionParticipant = mysql_fetch_assoc($retSessionParticipant)){
			if(isset($row_retSessionParticipant['id'])){
				$participants[] = $row_retSessionParticipant['id'];
			}
		}

		mysql_data_seek($retSessionParticipant, 0);
	}


	//Set JSON for javascript
	$participants_json = json_encode(array());
	if(!empty($participants)){
		$participants_json = json_encode($participants);
	}

	/* Check if the session is open and set the new state */
	$status_id = 1;
	if(isset($row_retSessionInfo['status_id']) && $row_retSessionInfo['status_id'] == 1){
		$status_id = 2;
	}

	//The sub navigation for the content
	if($session_id){
		$sub_group = 'sessions';
		$sub_navigation = array(
			'Emails' => 'session-emails.php?session_id=' . $session_id,
			'Green Room Template' => 'session-greenroom.php?session_id=' . $session_id,
			'Participants' => 'session-participants-jqgrid.php?session_id=' . $session_id,
			'Observers' => 'session-edit.php?session_id=' . $session_id,
			'Topics' => 'newTopic.php?session_id=' . $session_id
		);
		$sub_id = $session_id;
	}

	$created = time();
		
	/* Update status */
	$updateSQL4 = sprintf("UPDATE sessions SET status_id=$status_id, updated='$created' WHERE id=$session_id");
	mysql_select_db($database_ifs, $ifs);
	$Result4 = mysql_query($updateSQL4, $ifs) or die(mysql_error());
	
	if($status_id == 2){
		/* Find out if a reply was sent and if so update number of invites  */
		$left_out_participants = retrieve_participant_list($database_ifs, $ifs, null, $session_id, true);
	
		if($left_out_participants && !is_string($left_out_participants)){
			if(mysql_num_rows($left_out_participants) > 0){
				$part_fo = prepare_foreach($left_out_participants); //prepare participants for looping
	
				/* Update number of invites */
				foreach($part_fo as $part){
					$user_info = retrieve_users($database_ifs, $ifs, null, null, $part['user_id'], true);

					/* Get user information */
					if($user_info && !is_string($user_info)){
						$invites = array();

						if(mysql_num_rows($user_info) > 0){
							$user_val = mysql_fetch_assoc($user_info);

							/* Set invite information */
							$invites['total'] = $user_val['invites'];
							$invites['no_reply'] = $user_val['invites_no_reply'];

							/* Increment number of invites for those who didn't reply */
							$invites_update = iterate_number_of_invites($database_ifs, $ifs, $part['user_id'], null, $invites);
						}
					}
				}
			}
		}
	} else {
		/* If ajax is available then, then set session as open */
		if($ajax){
			$json_return = new StdClass;
			$json_return->open = $updateSQL4; //tell if session was opened or not

			echo json_encode($json_return);
			return;
		} else {
			mysql_close($ifs);

			header("Location: session-emails.php?session_id=" . $session_id);
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

	header('Location: index.php');
	die();
}

mysql_close($ifs);
