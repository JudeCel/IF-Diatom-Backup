<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('userfileupload.php');
	require_once('models/users_model.php'); 

	/* Set user type */
	$user_type = NULL;
	if($_SESSION['MM_UserTypeId']){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	/* Get message information */
	$message = new StdClass;
	$message->other = array();

	if(isset($_SESSION['notification'])){
		$message->other = $_SESSION['notification'];

		unset($_SESSION['notification']);
	}


if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
	//Brand Project id
	$brand_project_id = NULL;
	if(isset($_GET['brand_project_id'])){
		$brand_project_id =	strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
	}

	if(isset($_POST['submit'])){
		$URL="upload/userfiles/";
		
		uploadCsv($URL, $brand_project_id);
		
		$updateGoTo = "participantPanel.php?" . $_SERVER['QUERY_STRING'];
	}

	//Page properties
	$main_script = 'participant_panel';
	$other_content = 'participant_panel';
	$grid = true;
	$validate = false;
	$inline_scripting = 'brand_project_list_inline';
	$page_help = 'brand_projects';
	
	//Only initialisation - if client company id is available, it is set further down
	$page = 'Brand Projects | Participant Panel';
	$title = 'New Brand Project';
	
	$sub_id = null;
	$sub_group = null;
	$sub_navigation = null;

	// retrieve the participant information	
	mysql_select_db($database_ifs,$ifs);
	$query_retParticipant=" 
	SELECT 
	  users.name_first,
	  users.name_last,
	  users.email,
	  users.phone,
	  users.fax,
	  users.mobile,
	  users.job_title,
	  users.Gender,
	  addresses.street,
	  addresses.post_code,
	  addresses.suburb,
	  addresses.state,
	  participants.dob,
	  participants.ethnicity,
	  participants.occupation,
	  participants.brand_segment
	FROM
	  client_users
	  INNER JOIN brand_projects ON (client_users.client_company_id = brand_projects.client_company_id)
	  INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
	  INNER JOIN users ON (participants.user_id = users.id)
	  INNER JOIN addresses ON (users.address_id = addresses.id)
	  
	WHERE 
	  brand_projects.id=" . $brand_project_id . "
	GROUP BY
		participants.id";

	$retParticipant = mysql_query($query_retParticipant,$ifs) or die(mysql_error());
	$totalRows_retParticipant = 0;
	$client_company_id = 0;
	$client_company_name = 'New Company';

	$subtitle_found = false;
	
	if($retParticipant){
		$totalRows_retParticipant = mysql_num_rows($retParticipant);
	}

	if($totalRows_retParticipant){
		$participant_row = mysql_fetch_assoc($retParticipant);	

		mysql_data_seek($retParticipant, 0);

		/* Should use the client company subtitle */
		if($client_company_id && $brand_project_id){
 			$subtitle_found = true;
 		}
 	}

	//retrieve the bp info
	mysql_select_db($database_ifs, $ifs);
	$query_retBPInfo = "
	SELECT 
	  brand_projects.max_sessions,
	  brand_projects.name,
	  brand_projects.id,
	  brand_projects.end_date,
	  brand_projects.start_date,
	  brand_projects.client_company_id,
	  brand_projects.logo_url,
	  client_companies.name AS `client_company_name`,
	  client_companies.id AS `client_company_id`
	FROM
	  brand_projects
	  INNER JOIN client_companies ON(brand_projects.client_company_id = client_companies.id)
	WHERE
	  brand_projects.id=$brand_project_id  
	";

	$retBPInfo = mysql_query($query_retBPInfo, $ifs) or die(mysql_error());
	$row_retBPInfo = array();
	$totalRows_retBPInfo = 0;

	$brand_project_name = '';
	$brand_project_logo_url = '';

	if($retBPInfo){
		$row_retBPInfo = mysql_fetch_assoc($retBPInfo);
		$totalRows_retBPInfo = mysql_num_rows($retBPInfo);

		$title = $row_retBPInfo['name'];
		$brand_project_logo_url = $row_retBPInfo['logo_url'];
		$brand_project_name = $row_retBPInfo['name'];

		$client_company_id = $row_retBPInfo['client_company_id'];
		$client_company_name = $row_retBPInfo['client_company_name'];
	}

	//The sub navigation for the content
	if($brand_project_id){
		$sub_group = 'brand_projects';
		$sub_navigation = array(
			'Sessions' => 'newSession.php?brand_project_id=' . $brand_project_id,
			'Participant Panel' => 'participantPanel.php?brand_project_id=' . $brand_project_id								
		);

		if($user_type < 2){
			$sub_navigation['Panel Analysis'] = 'participantPanel-reports.php?brand_project_id=' . $brand_project_id;
		}

		//Add Observer List
		$sub_navigation['Observer List'] = 'bp_observers.php?brand_project_id=' . $brand_project_id;

		$sub_id = $brand_project_id;
	}

	//Set message
	if(!empty($message->other)){
		$message = process_messages($message);
	}

	require_once('views/home.php');
} else {
	if(!$user_type){
		$_SESSION['notification'] = 'You are logged out, please login again.';
	} else {
		$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
	}

	if(!admin($database_ifs, $ifs)){
		$_SESSION['current_location'] = $form_action;
	}

	mysql_close($ifs);

	header('Location: index.php');
	die();		
}

mysql_close($ifs);
