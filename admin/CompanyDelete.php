<?php
	require_once("Connections/ifs.php");
	require('core.php');
	require_once('models/brand_model.php');

	/* Get client company id */
	$client_company_id = null;
	if(isset($_GET['client_company_id'])){
		$client_company_id = strip_tags(mysql_real_escape_string($_GET['client_company_id']));
	}

	//Check if javascript set an ajax value
	$ajax = false;
	if(isset($_GET['ajax'])){
		$ajax_str = strip_tags(mysql_real_escape_string($_GET['ajax']));

		//Check if ajax string is a stamptime
		if(is_numeric($ajax_str) && strlen($ajax_str) == 10){
			$ajax = true;
		} else {
			$_SESSION['notification'] = 'Please enable javascript';
		}
	}

	//Make sure the user is supposed to have access
	if((!admin($database_ifs, $ifs) && $_SESSION['MM_UserTypeId'] != -1) || !$client_company_id || !$ajax){
		mysql_close($ifs);

		header("Location: clientCompany.php");

		die();		
	}

	mysql_select_db($database_ifs, $ifs);

	//query to delete brand project id
	$delete_sql = sprintf(
		"DELETE	FROM
			client_companies
		WHERE
			id = %d",
		$client_company_id
	);

	$result = mysql_query($delete_sql, $ifs);

	if(!$result){
		$_SESSION['notification'] = 'The client company could not be deleted';
	}

	mysql_close($ifs);

	header("Location: clientCompany.php");