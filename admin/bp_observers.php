<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/participant-email-model.php');
	require_once('models/users_model.php');

	/* Get user id */
	$user_id = 0;
	if(isset($_SESSION['MM_UserId']['user_id'])){
		$user_id = $_SESSION['MM_UserId'];
	}

	/* Get user type id */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	/* Get message information */
	$message = new StdClass;
	$message->other = array();

	if(isset($_SESSION['notification'])){
		$message->other = $_SESSION['notification'];

		unset($_SESSION['notification']);
	}

	//Brand Project id
	$brand_project_id = NULL;
	if(isset($_GET['brand_project_id'])){
		$brand_project_id =	strip_tags(mysql_real_escape_string($_GET['brand_project_id']));

		if(!is_numeric($brand_project_id)){
			$brand_project_id = NULL;			
		}		
	}

	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3) && $brand_project_id){		//Page properties
		$main_script = 'bp_observers';
		$other_content = 'bp_observers';
		$grid = true;
		$validate = false;
		$inline_scripting = 'brand_project_list_inline';
		$page_help = 'brand_projects';
		
		//Only initialisation - if client company id is available, it is set further down
		$page = 'Brand Projects | Brand Project Observers';
		$title = 'Brand Project';
		
		$sub_id = null;
		$sub_group = null;
		$sub_navigation = null;

		$footer = false;

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
		  client_companies.name AS `client_company_name`
		FROM
		  brand_projects
		  INNER JOIN client_companies ON(brand_projects.client_company_id = client_companies.id)
		WHERE
		  brand_projects.id=$brand_project_id  
		";

		$retBPInfo = mysql_query($query_retBPInfo, $ifs) or die(mysql_error());
		$row_retBPInfo = array();
		$totalRows_retBPInfo = 0;

		$brand_project_logo_url = '';

		if($retBPInfo){
			$row_retBPInfo = mysql_fetch_assoc($retBPInfo);
			$totalRows_retBPInfo = mysql_num_rows($retBPInfo);

			$title = $row_retBPInfo['name'];
			$client_company_id = $row_retBPInfo['client_company_id'];
			$client_company_name = $row_retBPInfo['client_company_name'];
			$brand_project_logo_url = $row_retBPInfo['logo_url'];

			/* Should use the client company subtitle */
			if($client_company_id && $brand_project_id){
	 			$subtitle_found = true;
	 		}
		}

		///The sub navigation for the content
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