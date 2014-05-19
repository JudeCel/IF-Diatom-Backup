<?php	
require_once('Connections/ifs.php');     
require_once('core.php');
require_once('getCounts.php');
require_once('models/participant-email-model.php');
require_once('models/users_model.php');
require_once('models/array_repository_model.php');

/* Set user type */
$user_type = null;
if(isset($_SESSION['MM_UserTypeId'] )){
	$user_type = $_SESSION['MM_UserTypeId'];
}

$message = new StdClass;
$message->other = array();

/* Get message information */
if(isset($_SESSION['notification'])){
	$message->other = $_SESSION['notification'];

	unset($_SESSION['notification']);
}

if(admin($database_ifs, $ifs) && $user_type == -1 || $user_type == 1){
	//Page properties
	$main_script = 'client_users';
	$other_content = 'client_company_users';
	$grid = false;
	$validate = false;
	$inline_scripting = 'client_company_users_inline';
	$page_help = 'companies';
	
	//Only initialisation - if client company id is available, it is set further down
	$page = 'Global Admin';
	$title = 'New Company';
	
	$sub_id = null;
	$sub_group = null;
	$sub_navigation = array();

	//retrieve user types
	mysql_select_db($database_ifs, $ifs);
	$query_retUserTypes = "SELECT * FROM client_user_types WHERE id IN (1)";
	$retUserTypes = mysql_query($query_retUserTypes, $ifs) or die(mysql_error());
	$totalRows_retUserTypes = 0;

	//If the client user types are found
	if($retUserTypes){
		$totalRows_retUserTypes = mysql_num_rows($retUserTypes);
	}
	
	//Find client company id
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

	//retrieve company name
	mysql_select_db($database_ifs, $ifs);
	$query_retcompany_name = "
	SELECT 
	  client_companies.name AS company_name,
	  client_companies.start_date,
	  client_companies.end_date,
	  addresses.post_code,
	  addresses.suburb,
	  addresses.state,
	  addresses.street,
	  country_lookup.country_name
	FROM
	  client_companies
	  INNER JOIN addresses ON (client_companies.address_id = addresses.id)
	  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id)
	WHERE
	  client_companies.id=$client_company_id";

	//Get results for client companies
	$retcompany_name = mysql_query($query_retcompany_name, $ifs) or die(mysql_error());
	$totalRows_retcompany_name = 0;
	$row_retcompany_name = array();

	/* If client company is found */
	if($retcompany_name){
		$row_retcompany_name = mysql_fetch_assoc($retcompany_name);
		$totalRows_retcompany_name = mysql_num_rows($retcompany_name);
	} else {
		//Set message
		$message[] = 'The Client Company is not set';
		$_SESSION['notification'] = $message;

		mysql_close($ifs);

		header("Location: signup.php");
		die();
	}

	//Client company name
	$client_company_name = (isset($row_retcompany_name['company_name']) ? $row_retcompany_name['company_name'] : '');

	//If the client company name is set
	if($client_company_name){
		//Change the page properties using the client company name
		$page = 'Global Admin | ' . $client_company_name;
		$title = $client_company_name;
	}

	if($client_company_id == $_SESSION['MM_CompanyId'] || $user_type == -1){
		// retrieve the users
		mysql_select_db($database_ifs,$ifs);
		$query_retClientUser="
		SELECT 
		  users.id,
		  users.name_first,
		  users.name_last,
		  users.email,
		  users.phone,
		  users.fax,
		  users.mobile,
		  users.job_title,
		  users.avatar_resource_id,
		  client_user_types.name
		FROM
		  users
		  INNER JOIN user_logins ON (users.user_login_id = user_logins.id)
		  INNER JOIN client_users ON (users.id = client_users.user_id)
		  INNER JOIN client_user_types ON (client_users.type_id = client_user_types.id)
		WHERE
		   client_users.client_company_id=".$client_company_id." 
		   AND client_users.type_id = 1
		   AND client_users.active = 1
		";

		$retClientUser =mysql_query($query_retClientUser,$ifs) or die(mysql_error());

		/* Save the client users to check if users are not already admin */
		$users_set = array();
		$row_retClientUser = NULL;
		$totalRows_retClientUser = 0;

		if($retClientUser){	
			$totalRows_retClientUser = mysql_num_rows($retClientUser); //total

			//Check that the result is not empty
			if($totalRows_retClientUser){
				$client_users = prepare_foreach($retClientUser);
				mysql_data_seek($retClientUser, 0); //reset retClientUser

				/* Save in array */
				foreach($client_users as $user){
					$users_set[] = $user['id'];
				}
			}	
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