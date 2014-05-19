<?php
	require_once("Connections/ifs.php");
	require('core.php');
	require_once('models/users_model.php');

	//Make sure that it is an ajax call and that the user has been set
	if(!isset($_GET['ajax']) || !isset($_GET['uid']) || !admin($database_ifs, $ifs)){
		return;
	}

	$user_info = array();

	$uid = strip_tags(mysql_real_escape_string($_GET['uid'])); //uid
	$user = retrieve_users($database_ifs, $ifs, false, null, $uid, false, null);

	//Make sure that the query was successful
	if(!$user || is_string($user)){
		$user_info['valid'] = false;
	} else {
		$user_info['user'] = mysql_fetch_assoc($user);
		$user_info['valid'] = true;
	}

	$user_json = json_encode($user_info); //encode as json

	echo $user_json; //display json for ajax to read

	mysql_close($ifs);