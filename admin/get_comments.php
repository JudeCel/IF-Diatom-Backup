<?php
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/users_model.php');

	if((!admin($database_ifs, $ifs) && $_SESSION['MM_UserTypeId'] > 3) || !isset($_GET['participant_list_id'])){
		$_SESSION['current_location'] = $current_location;
		
		mysql_close($ifs);

		header('Location: index.php');
		die();
	}

	$participant_list_id = strip_tags(mysql_real_escape_string($_GET['participant_list_id'])); //Participant Lists id

	//Find comments of Participant
	$comments_result = find_participant_comments($database_ifs, $ifs, $participant_list_id);

	//Get Comments
	$comments = null;
	if(!$comments_result || ($comments_result && is_string($comments_result))){
		$comments = (!$comments_result ? 'No Comments Found' : $comments_result);
	} else {
		$comments_row = mysql_fetch_assoc($comments_result);
		$comments = $comments_row['comments'];
	}

	echo $comments;

	mysql_close($ifs);
