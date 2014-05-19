<?php 
require_once('Connections/ifs.php');     
require_once('core.php');
require_once('models/users_model.php');
require_once('models/participant-email-model.php');


if(isset($_GET['user_login_id_array'])){	
	$user_login_ids = strip_tags(mysql_real_escape_string($_GET['user_login_id_array']));
	$user_login_id_array = explode(',', $user_login_ids);

	$email_array = array();

	foreach($user_login_id_array as $ulid){
		$user_info = get_user_id_and_name($database_ifs, $ifs, $ulid); //get user info

		//Get user info
		if($user_info && !is_string($user_info)){ //find e-mail
			$user_info_row = mysql_fetch_assoc($user_info);

			$email = $user_info_row['email']; //set e-mail
			
			//request email change
			request_change_to_password($database_ifs, $ifs, $email, $ulid);
		}
	}			
}

mysql_close($ifs);

$updateGoTo = $_SERVER['HTTP_REFERER'];
header(sprintf("Location: %s", $updateGoTo));