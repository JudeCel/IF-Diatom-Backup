<?php
	require_once("Connections/ifs.php");
	require('core.php');
	require_once('models/brand_model.php');

	/* Get brand project id */
	$brand_project_id = null;
	if(isset($_GET['brand_project_id'])){
		$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));

		if(!is_numeric($brand_project_id)){
			$brand_project_id = NULL;
		}
	}

	/* Get client company id */
	$client_company_id = null;
	if(isset($_GET['client_company_id'])){
		$client_company_id = strip_tags(mysql_real_escape_string($_GET['client_company_id']));

		if(!is_numeric($client_company_id)){
			$client_company_id = NULL;
		}
	}

	//Check if user needs to return to the panel
	$panel = false;
	if(isset($_GET['panel'])){
		$panel = true;
	}

	//Check if javascript set an ajax value
	$ajax = false;
	if(isset($_GET['ajax'])){
		$ajax_str = $_GET['ajax'];

		//Check if ajax string is a stamptime
		if(is_numeric($ajax_str) && strlen($ajax_str) == 10){
			$ajax = true;
		} else {
			$_SESSION['notification'] = 'Please enable javascript';
		}
	}

	//Make sure the user is supposed to have access
	if((!admin($database_ifs, $ifs) && $_SESSION['MM_UserTypeId'] != -1) || !$brand_project_id || !$ajax){
		//Redirect
		mysql_close($ifs);

		if($client_company_id){
			if(!$panel){
				header("Location: newBrandProject.php?client_company_id=" . $client_company_id);
			} else {
				header("Location: brandProject.php"); //go to main panel
			}
		} else {
			header("Location: index.php");
		}

		die();
	}

	mysql_select_db($database_ifs, $ifs);

	//query to delete brand project id
	$delete_sql = sprintf(
		"DELETE	FROM
			brand_projects
		WHERE
			id = %d",
		$brand_project_id
	);

	$result = mysql_query($delete_sql, $ifs);

	if(!$result){
		$_SESSION['notification'] = 'The brand project could not be deleted';
	}

	mysql_close($ifs);

	//Redirect
	if($client_company_id){
		if(!$panel){
			header("Location: newBrandProject.php?client_company_id=" . $client_company_id);
		} else {
			header("Location: brandProject.php");
		}
	} else {
		header("Location: index.php");
	}