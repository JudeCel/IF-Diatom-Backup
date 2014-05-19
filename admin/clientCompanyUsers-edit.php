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

/* Get message information */
$message_val = new StdClass;
$message_val->other = array();

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
$page = 'Client Company Edit';
$title = 'Edit User';
$main_script = 'client_company_users_insert';
$other_content = 'client_company_users_insert';
$validate = true;
$inline_scripting = false;
$import = false;
$import_page = null;

//retrieve user types
mysql_select_db($database_ifs, $ifs);
$query_retUserTypes = "SELECT * FROM client_user_types WHERE client_user_types.id IN (1)";

$retUserTypes = mysql_query($query_retUserTypes, $ifs) or die(mysql_error());

$totalRows_retUserTypes = 0;

//If query was successful
if($retUserTypes){
	$totalRows_retUserTypes = mysql_num_rows($retUserTypes);
}
	
if(isset($_GET['user_id'])){
	$user_id = strip_tags(mysql_real_escape_string($_GET['user_id']));	

	if(!is_numeric($user_id)){
		$user_id = NULL;
	}
} else {
	$user_id = NULL;
}

$staff = false;
if(isset($_GET['staff'])){
	$staff = true;
}

//Ensure that user_id is set
if(!$user_id){
	mysql_close($ifs);
	header("Location: index.php");
	die();
}

// retrieve the users
mysql_select_db($database_ifs,$ifs);
$query_retClientUser="
SELECT 
  users.name_first,
  users.name_last,
  users.email,
  users.phone,
  users.fax,
  users.mobile,
  users.job_title,
  users.avatar_resource_id,
  users.Gender,
  users.uses_landline,
  client_users.user_id,
  client_users.type_id,
  user_logins.id AS user_login_id,
  client_users.id AS client_user_id,
  users.id AS user_id,
  sess_staff.session_type_id
FROM
  users
  INNER JOIN user_logins ON (users.user_login_id = user_logins.id)
  INNER JOIN client_users ON (users.id = client_users.user_id)
  LEFT JOIN (SELECT user_id AS `uid`, type_id AS `session_type_id` FROM session_staff) AS `sess_staff` ON(users.id = sess_staff.uid)
WHERE
   client_users.user_id=".$user_id."
";
	
$retClientUser = mysql_query($query_retClientUser,$ifs) or die(mysql_error());
$row_retClientUser = array();
$totalRows_retClientUser = 0;

$user_login_id = null;
$type_id = null;

/* If query was successful */
if($retClientUser){	
	$totalRows_retClientUser = mysql_num_rows($retClientUser); 
}

/* If rows were found */
if($totalRows_retClientUser){
	$row_retClientUser = mysql_fetch_assoc($retClientUser);

	$user_login_id = $row_retClientUser['user_login_id'];
	$type_id = $row_retClientUser['type_id'];

	//If type id is only set with session staff
	if(!$type_id && isset($row_retClientUser['session_type_id'])){
		$type_id = $row_retClientUser['session_type_id'];
	}
}

$user_type = 'User';
switch($type_id){
	case 1:		
		$user_type = 'Global Admin';
	break;
	case 2:
		$user_type = 'Facilitator';
	break;
	case 4:
		$user_type = 'Observer';
	break;
}

//Set title for user type
if($type_id == 1 || $type_id == 2 || $type_id == 4){
	$title = 'Edit ' . $user_type;
}

$fields = array();

//Input Values
/* get data	*/		
$name_first = (isset($_POST['name_first']) ? htmlentities(mysql_real_escape_string($_POST['name_first'])) : ''); //First Name		
$name_last = (isset($_POST['name_last']) ? htmlentities(mysql_real_escape_string($_POST['name_last'])) : ''); //Last Name		
$gender = (isset($_POST['gender']) ? htmlentities(mysql_real_escape_string($_POST['gender'])) : ''); //Gender	
$job_title = (isset($_POST['job_title']) ? htmlentities(mysql_real_escape_string($_POST['job_title'])) : ''); //Job Title
$email = (isset($_POST['email']) ? htmlentities(mysql_real_escape_string($_POST['email'])) : ''); //Email
$mobile = (isset($_POST['mobile']) ? htmlentities(mysql_real_escape_string($_POST['mobile'])) : ''); //Mobile
$phone = (isset($_POST['phone']) ? htmlentities(mysql_real_escape_string($_POST['phone'])) : ''); //Phone
$fax = (isset($_POST['fax']) ? htmlentities(mysql_real_escape_string($_POST['fax'])) : ''); //Fax
$uses_landline = (isset($_POST['uses_landline']) ? htmlentities(mysql_real_escape_string($_POST['uses_landline'])) : ''); //Fax

//Use Database values
if(!isset($_POST['name_first']) && isset($row_retClientUser['name_first'])){
	$name_first = $row_retClientUser['name_first'];
}
if(!isset($_POST['name_last']) && isset($row_retClientUser['name_last'])){
	$name_last = $row_retClientUser['name_last'];
}
if(!isset($_POST['gender']) && isset($row_retClientUser['Gender'])){
	$gender = $row_retClientUser['Gender'];
}
if(!isset($_POST['job_title']) && isset($row_retClientUser['job_title'])){
	$job_title = $row_retClientUser['job_title'];
}
if(!isset($_POST['email']) && isset($row_retClientUser['email'])){
	$email = $row_retClientUser['email'];
}
if(!isset($_POST['mobile']) && isset($row_retClientUser['mobile'])){
	$mobile = $row_retClientUser['mobile'];
}
if(!isset($_POST['phone']) && isset($row_retClientUser['phone'])){
	$phone = $row_retClientUser['phone'];
}
if(!isset($_POST['fax']) && isset($row_retClientUser['fax'])){
	$fax = $row_retClientUser['fax'];
}
if(!isset($_POST['uses_landline']) && isset($row_retClientUser['uses_landline'])){
    $uses_landline = $row_retClientUser['uses_landline'];
}

//do the insert
if(isset($_POST['btnSubmit'])){
	/* Prepare for notification */
	$update_message = TRUE;

	$update = update_user_profile($database_ifs, $ifs, $user_id, $user_login_id);

	/* Update profile */
	if(is_array($update)){
		$message = $update['html'];
		$fields = $update['fields'];
	} else {
		$message_val->other[] = $user_type . '\'s profile updated.';

		$_SESSION['notification'] = array();
		$_SESSION['notification'][] = process_messages($message_val);

		//close fancybox
		echo '<script type="text/javascript" src="js/fancybox_close.js" />';
	}
}

mysql_close($ifs);

require_once('views/popup.php');