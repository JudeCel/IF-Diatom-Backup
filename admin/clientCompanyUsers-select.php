<?php	
require_once('Connections/ifs.php');     
require_once('core.php');
require_once('models/users_model.php');
require_once('models/participant-email-model.php');

/* Type of User */
$user_type = null;
if(isset($_SESSION['MM_UserTypeId'])){
	$user_type = $_SESSION['MM_UserTypeId'];
}

$message_val = new stdClass;
$message_val->other = array();
$update = FALSE;
$redirection = FALSE;
$sent = null;

/* Ensure that the user has access to these pages */
if(!admin($database_ifs, $ifs) || ($user_type != -1 && $user_type != 1)){
	if(!$user_type){
		$_SESSION['notification'] = 'You are logged out, please login again.';
	} else {
		$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
	}

	mysql_close($ifs);

	header("Location: index.php");

	die();
}

//Page properties
$page = 'Add Global Admin';
$title = 'Add Global Admin';
$main_script = null;
$other_content = 'client_company_users_insert_select';
$validate = false;
$inline_scripting = false;

//retrieve user types
mysql_select_db($database_ifs, $ifs);
$query_retUserTypes = "SELECT * FROM client_user_types WHERE client_user_types.id IN (1)";

$retUserTypes = mysql_query($query_retUserTypes, $ifs) or die(mysql_error());

$totalRows_retUserTypes = 0;

//If query was successful
if($retUserTypes){
	$totalRows_retUserTypes = mysql_num_rows($retUserTypes);
}
	
//Set the client company id
$client_company_id = null;
if(isset($_GET['client_company_id'])){
	$client_company_id =	strip_tags(mysql_real_escape_string($_GET['client_company_id']));
}

//Set user id
$user_id = null;
if(isset($_GET['user_id'])){
	$user_id = strip_tags(mysql_real_escape_string($_GET['user_id']));

	$update = TRUE; //the user was passed along
	$redirection = TRUE;
}

//retrieve the user list
mysql_select_db($database_ifs, $ifs);
$query_retMods = "
SELECT 
  users.name_first,
  users.name_last,
  user_logins.id AS `user_login_id`,
  client_users.user_id
FROM
  users
  INNER JOIN client_users ON (users.id = client_users.user_id)
  INNER JOIN user_logins ON (users.user_login_id = user_logins.id)	  
WHERE
  client_users.client_company_id=$client_company_id
  AND client_users.active = 1
  AND (client_users.type_id != 4
  OR client_users.type_id IS NULL)
";

$retMods = mysql_query($query_retMods, $ifs) or die(mysql_error());
$totalRows_retMods = 0;

//If query was successful
if($retMods){
	$totalRows_retMods = mysql_num_rows($retMods);
}

//Variables for setting global admin
$type_id = 1;
$created = date('Y-m-d H:i:s'); //created

/* Redirect */
if(!$totalRows_retMods && !$redirection){
	mysql_close($ifs);

	header("Location: clientCompanyUsers-insert.php?client_company_id=" . $client_company_id . "&type_id=" . $type_id);
	exit();
}	

//do the insert
if(isset($_POST['btnSubmit'])){
	/* Prepare for notification */
	$update_message = TRUE;

	$user_id = strip_tags(mysql_real_escape_string($_POST['user_id']));

	$update = true;
}

/* If the global admin was processed */
if($update){
	$company_details = get_client_company_details($database_ifs, $ifs, $client_company_id);
	$user = retrieve_users($database_ifs, $ifs, false, false, $user_id);

	$password = 'Your Password';

	/* If company details are available */
	if($company_details && !is_string($company_details)){
		
		/* If the user details are available */
		if($user && !is_string($user)){
			$row_retcompany_name = mysql_fetch_assoc($company_details);
			$row_user = mysql_fetch_assoc($user);

			$full_name = $row_user['name_first'] . ' ' . $row_user['name_last'];
			$subject = $row_retcompany_name['company_name'] . ' Global Admin Registration';

			if($redirection){
				//Create New Password
				$password = create_unique_password();
				$pass_hashed = md5($password);

				$user_login_id = create_user_logins($database_ifs, $ifs, $row_user['email'], $pass_hashed, $user_id);				
			}

			/* Upsert client user */


            if(isset($_POST['btnSubmit'])&&($_POST['btnSubmit']=='Add'))
            {
                $SQLR =
                    "UPDATE client_users
                    SET active=1"
                    .($type_id ? ', type_id=' . $type_id : '')
                    .sprintf("
                    Where
                    client_company_id=%d
                    AND user_id=%d"
                    ,
                    $client_company_id,
                    $user_id);
            }
            else
            {
                $SQLR = sprintf(
                    "INSERT INTO
                        client_users
                        (client_company_id, user_id, created, active%s)
                    VALUES (%d, %d, '%s', 1%s)",
                    ($type_id ? ', type_id' : ''),
                    $client_company_id,
                    $user_id,
                    $created,
                    ($type_id ? ', ' . $type_id : '')
                );
            }

			$global_admin = mysql_query($SQLR, $ifs);

			if(!$global_admin){
				$message_val->other[] = 'The client information could not be saved';
				$update = false; //stop e-mail sending
			}

			if($update){
				/* Set replacemnt tags for sending e-mail */
				$replacements = array();
				$replacements['First Name'] = $row_user['name_first'];
				$replacements['Last Name'] = $row_user['name_last'];
				$replacements['Username'] = $row_user['email'];
				$replacements['Password'] = $password;
				$replacements['Company Name'] = $row_retcompany_name['company_name']; 
				$replacements['Company Street'] = $row_retcompany_name['street'];
				$replacements['Company Suburb'] = $row_retcompany_name['suburb'];
				$replacements['Company State'] = $row_retcompany_name['state'];
				$replacements['Company Postcode'] = $row_retcompany_name['post_code'];
				$replacements['Company Country'] = $row_retcompany_name['country_name'];
				$replacements['Start Date'] = date('h:ia l j F Y', strtotime($row_retcompany_name['start_date']));
				$replacements['End Date'] = date('h:ia l j F Y', strtotime($row_retcompany_name['end_date']));

				$sent = send_admin_email_to_user($database_ifs, $ifs, $row_user['email'], $user_id, $full_name, $subject, 'admin_register', $replacements);

				if($sent){
					if($redirection){
						mysql_close($ifs);

						header(sprintf("Location: clientCompanyUsers-insert.php?client_company_id=%d&update=1&type_id=1", $client_company_id));
						die();
					} else {
						$message_val->other[] = 'A Global Administrator was added successfully.';
					}
				} else {
					$message_val->other[] = 'The e-mail to the added Global Administrator could not be sent.';
				}
			}
		} else { //The user could not be found
			$message_val->other[] = 'The user information could not be found';
		}
	} else { //The company could not be found
		$message_val->other[] = 'The company information could not be found';
	}
}

/* Transform message into a string to be used by the view */
if(!empty($message_val->other)){
	if(!$sent){
		$message = process_messages($message_val);
	} else {
		//Session is not writing correctly, make sure the data is saved
		session_write_close();
		session_start();

		$_SESSION['notification'] = array();
		$_SESSION['notification'][] = process_messages($message_val);

		session_write_close();

		//close fancybox
		echo '<script type="text/javascript" src="js/fancybox_close.js" />';	
	}
}

mysql_close($ifs);

require_once('views/popup.php');