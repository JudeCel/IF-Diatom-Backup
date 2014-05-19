<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php'); 
	require_once('getCounts.php');
	require_once('models/users_model.php');

	/* Get message information */
	$message = new StdClass;
	$message->other = array();

	if(isset($_SESSION['notification'])){
		$message->other = $_SESSION['notification'];

		unset($_SESSION['notification']);
	}

	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	if(admin($database_ifs, $ifs) && $user_type == -1 || $user_type == 1){
		/* Get client company id */
		if(isset($_GET['client_company_id'])){
			$client_company_id = strip_tags(mysql_real_escape_string($_GET['client_company_id']));
		} else {
			//Set message
			$message->other[] = 'The Client Company is not set';
			$_SESSION['notification'] = $message->other;

			mysql_close($ifs);

			header("Location: signup.php");
			die();
		}

		/* Get user type id */
		$user_type = null;
		if(isset($_SESSION['MM_UserTypeId'])){
			$user_type = $_SESSION['MM_UserTypeId'];
		}

		if($client_company_id == $_SESSION['MM_CompanyId'] || $user_type == -1){
			//Page properties
			$main_script = 'brand_project_list';
			$other_content = 'brand_project_list';
			$grid = false;
			$validate = true;
			$inline_scripting = 'client_company_users_inline';
			$page_help = 'companies';
			
			//Only initialisation - if client company id is available, it is set further down
			$page = 'Brand Projects';
			$title = 'New Company';
			
			$sub_id = null;
			$sub_group = null;
			$sub_navigation = array();

			//retrieve the client company info
			mysql_select_db($database_ifs, $ifs);
			$query_retCompany = "
			SELECT 
			  client_companies.name,
			  client_companies.start_date,
			  client_companies.end_date,
			  client_companies.number_of_brands,
			  client_companies.max_sessions_brand,
			  client_companies.client_company_logo_thumbnail_url,
			  client_companies.client_company_logo_url
			FROM
			  client_companies
			WHERE
			  client_companies.id=$client_company_id  
			";
			
			$retCompany = mysql_query($query_retCompany, $ifs) or die(mysql_error());
			$totalRows_retCompany = 0;
			$row_retCompany = array();

			/* If client company was found */
			if($retCompany){
				$row_retCompany = mysql_fetch_assoc($retCompany);
				$totalRows_retCompany = mysql_num_rows($retCompany);
			} else {
				//Set message
				$message->other[] = 'The Client Company is not set';
				$_SESSION['notification'] = $message->other;

				mysql_close($ifs);

				header("Location: signup.php");
				die();
			}

			//If the client company name is set
			if(isset($row_retCompany['name'])){
				$client_company_name = $row_retCompany['name'];

				//Change the page properties using the client company name
				$page = 'Brand Projects | ' . $client_company_name;
				$title = $client_company_name;
			}

		 	$uploadText = 'Preview';

		 	/*Get company log full image */
		 	$client_company_logo_url = NULL;
		 	if(isset($row_retCompany['client_company_logo_url'])){
		 		$client_company_logo_url = $row_retCompany['client_company_logo_url'];
		 	}

			//retrieve the brand projects 
			mysql_select_db($database_ifs, $ifs);
			$query_retBPs = "
			SELECT 
			  brand_projects.max_sessions,
			  brand_projects.name,
			  brand_projects.id,
			  brand_projects.end_date,
			  brand_projects.start_date,
			  brand_projects.session_replay_date
			FROM
			  brand_projects
			WHERE
			   brand_projects.client_company_id=$client_company_id  
			";

			$retBPs = mysql_query($query_retBPs, $ifs) or die(mysql_error());
			$totalRows_retBPs = 0;

			/* If Brand Project found */
			if($retBPs){
				$totalRows_retBPs = mysql_num_rows($retBPs);
			}

			//The sub navigation for the content
			if($client_company_id){
				$sub_group = 'client_company';

				//Only allow registration tab for IFS Admin
				if($user_type == -1){
					$sub_navigation['Registration'] = 'signup.php?client_company_id=' . $client_company_id;
				}

				//Pages available to all eligible roles
				$pages_available = array(
					'Global Admin' => 'clientCompanyUsers.php?client_company_id=' . $client_company_id,
					'Brand Projects' => 'newBrandProject.php?client_company_id=' . $client_company_id
				);

				$sub_navigation += $pages_available;

				$sub_id = $client_company_id;
			}

			//Set message
			if(!empty($message->other)){
				$message = process_messages($message);
			}

			require_once('views/home.php');
		} else {
			$_SESSION['notification'] = 'You are logged out, please login again.';
	
			if(!admin($database_ifs, $ifs)){
				$_SESSION['current_location'] = $form_action;
			}

			mysql_close($ifs);

			header('Location: index.php');
			die();
		}
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