<?php 
require_once('Connections/ifs.php');     
require_once('core.php');
require_once('models/users_model.php');

/* Set user type */
$user_type = NULL;
if($_SESSION['MM_UserTypeId']){
	$user_type = $_SESSION['MM_UserTypeId'];
}

$message = new stdClass;
$message->other = array();

if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
	// get the user_login_id from URL
	if(isset($_GET['session_id']))
	{
		$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));	
	}
	else
	{
		$session_id = -1;
	}

	//Page properties
	$page = 'Send SMS';
	$title = $page;
	$main_script = 'participant_sms';
	$other_content = 'participant_sms';
	$validate = true;
	$inline_scripting = 'participant_sms_inline';

	//Send selected participants to send SMS
	$participant_results = null;
	if(isset($_GET['part_ids'])){
		$part_ids = strip_tags(mysql_real_escape_string($_GET['part_ids'])); //get the sent participant ids
		$decoded_part = json_decode($part_ids); //decode the json so the ids are usable

		//Get users, using decoded participant ids
		$participant_results = find_participant($database_ifs, $ifs, $decoded_part, true, false, null);	 
	}

	//retrieve the session staff info 
	mysql_select_db($database_ifs, $ifs);
	$query_retSessionInfo = "
	SELECT 
	  session_staff.id AS session_staff_id,
	  sessions.brand_project_id,
	  sessions.name,
	  sessions.start_time,
	  sessions.end_time,
	  sessions.incentive_details,
	  session_staff.session_id,
	  users.name_first,
	  users.name_last,
	  session_staff.user_id,
	  sessions.id,
	   sessions.incentive_details,
	  brand_projects.client_company_id
	FROM
	  sessions
	  INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
	  INNER JOIN users ON (session_staff.user_id = users.id)
	  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
	WHERE
	  session_staff.type_id=2 AND sessions.id=$session_id
	  
	";
	$retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error());
	$row_retSessionInfo = mysql_fetch_assoc($retSessionInfo);
	$totalRows_retSessionInfo = mysql_num_rows($retSessionInfo);

	$brand_project_id=$row_retSessionInfo['brand_project_id'];
	$client_company_id=$row_retSessionInfo['client_company_id'];	

	$retSessionParticipant = NULL;
	if(!$participant_results || ($participant_results && is_string($participant_results))){
		//retrieve the session participants
		mysql_select_db($database_ifs,$ifs);
		$query_retSessionParticipant=" 
		SELECT DISTINCT 
		  users.name_first,
		  users.name_last,
		  users.email,
		  users.phone,
		  users.fax,
		  users.mobile,
		  users.job_title,
		  users.Gender,
		  participants.id AS `part_id`,
		  participants.dob,
		  participants.ethnicity,
		  participants.occupation,
		  participants.brand_segment,
		  participant_lists.id,
		  participant_lists.participant_id,
		  participant_reply_lookup.reply_name,
		  participant_lists.participant_reply_id
		FROM
		  participants
		  INNER JOIN users ON (participants.user_id = users.id)
		  INNER JOIN participant_lists ON (participants.id = participant_lists.participant_id)
		  LEFT OUTER JOIN participant_reply_lookup ON (participant_lists.participant_reply_id = participant_reply_lookup.id)
		WHERE 
		  participant_lists.session_id=".$session_id."
		 ";
		$retSessionParticipant =mysql_query($query_retSessionParticipant,$ifs) or die(mysql_error());
		$totalRows_retSessionParticipant = mysql_num_rows($retSessionParticipant);
	}

	$participants_found = array();

	if($retSessionParticipant){
		//Prepare participants to display information
		if(mysql_num_rows($retSessionParticipant) > 0){
			while($row = mysql_fetch_assoc($retSessionParticipant)){
				$participants_found[$row['part_id']] = $row;
			}
		}
	}

	//Combine with participants that have not yet been invited
	if($participant_results && !is_string($participant_results)){
		while($row = mysql_fetch_assoc($participant_results)){
			$participants_found[$row['part_id']] = $row;
		}
	}

	//Sort by participants by keys
	if(!empty($participants_found)){
		ksort($participants_found);
	}

	if(isset($_POST['btnSubmit']))
	{	
		//Code to send message through Direct SMS
		$connectURL="http://api.directsms.com.au/s3/http/send_message?";
		$username="judeifs";
		$password="1c72cbc2";
		$mobileName = 'YourVoice';

		$text = null;
		if(isset($_POST["message"]) && $_POST["message"]){
			$text = urlencode(htmlentities(mysql_real_escape_string($_POST["message"])));
		} else {
			$message->other[] = "Please write a message before sending a SMS.";
		}

		$participants = array();
		if(isset($_POST['participants'])){
			$participants = $_POST['participants'];
		}	else {
			$message->other[] = "Please select Participants to send a SMS.";
		}

		if(!empty($participants) && $text){
			$participants=$_POST['participants'];
			 
			if(sizeof($participants) > 0)
			{
				for($i=0; $i<sizeof($participants);$i++)
				{
					if($participants[$i])
					{
						if(is_numeric($participants[$i])){
							$number = $participants[$i];
							$length = strlen($number);							
							
							switch($length){
								case 9:
									$number = '61' . $number;
								break;
								case 10:
									$number = '61' . ltrim($number, '0');
								break;
								case 12:
									$number = preg_replace("/[^0-9,.]/", "", $number);
								break;	
							}
							
							if($length < 9 || $length > 12){
								//Let the user know that the telephone number was not correct
								$message->other[] = sprintf("Telephone number %s was not in an appropriate format", $number);
								continue;
							}

							$temp[]=$number;
						}
					}
				}
			}

			if(!empty($temp)){
				$to= implode(',', $temp);	
				// auth call
				$url=$connectURL."username=".$username."&password=".$password."&message=".$text."&senderid=".$mobileName."&to=".$to."&type=1-way";

				//Start cURL
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				//Output of cURL
				$ret = curl_exec($ch);

				curl_close($ch);

				//Make sure that the response contains MessageID
				if($ret && is_string($ret) && strpos($ret, 'id') !== false){
					$message->other[] = "Message has been sent";
				} else {
					$message->other[] = "Message could not be sent";
				}
			}
		}	
		
		//put the code to send the sms
		/* +-----------------------------------------------------------------------+
		 * | AussieSMS PHP HTTP API                                                |
		 * +-----------------------------------------------------------------------+
		 * | Copyright (c) 2008 Tafadzwa Brandon Tapera - AussieSMS Chief Engineer |
		 * +-----------------------------------------------------------------------+
		 * | Authors: Tafadzwa Brandon Tapera <support.aussiesms.com.au>           |
		 * +-----------------------------------------------------------------------+
		 */
		
		//the aussie sms account details
		
		/*$mobileID = "61403290248";
		$mobileName = 'YourVoice';
		$password = "v3f8M5td";
		
		$baseurl ="http://api.aussiesms.com.au/";
		
		$text = null;
		if(isset($_POST["message"]) && $_POST["message"]){
			$text = urlencode(htmlentities(mysql_real_escape_string($_POST["message"])));
		} else {
			$message->other[] = "Please write a message before sending a SMS.";
		}
		
		$participants = array();
		if(isset($_POST['participants'])){
			$participants = $_POST['participants'];
		}	else {
			$message->other[] = "Please select Participants to send a SMS.";
		}
		
		//testing multiple numbers
		//now it will populated based on post variables
		
		
		if(!empty($participants) && $text){
			$participants=$_POST['participants'];
			 
			if(sizeof($participants) > 0)
			{
				for($i=0; $i<sizeof($participants);$i++)
				{
					if($participants[$i])
					{
						if(is_numeric($participants[$i])){
							$number = $participants[$i];
							$length = strlen($number);							
							
							switch($length){
								case 9:
									$number = '61' . $number;
								break;
								case 10:
									$number = '61' . ltrim($number, '0');
								break;
								case 12:
									$number = preg_replace("/[^0-9,.]/", "", $number);
								break;	
							}
							
							if($length < 9 || $length > 12){
								//Let the user know that the telephone number was not correct
								$message->other[] = sprintf("Telephone number %s was not in an appropriate format", $number);
								continue;
							}

							$temp[]=$number;
						}
					}
				}
			}

			if(!empty($temp)){
				$to= implode(',', $temp);	
				
				// auth call
				$url = "$baseurl?sendsms&mobileID=$mobileID&password=$password&to=$to&text=$text&from=$mobileName&msg_type=SMS_TEXT";
				// do auth call
				//$ret = file($url);

				//Start cURL
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				//Output of cURL
				$ret = curl_exec($ch);

				curl_close($ch);
	
				//Make sure that the response contains MessageID
				if($ret && is_string($ret) && strpos($ret, 'MessageID') !== false){
					$message->other[] = "Message has been sent";
				} else {
					$message->other[] = "Message could not be sent";
				}
			}
		}*/		
	}

	if(!empty($message->other)){
		$message = process_messages($message);
	}

	require_once('views/popup.php');			
} else {
	if(!$user_type){
		$_SESSION['notification'] = 'You are logged out, please login again.';
	} else {
		$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
	}

	mysql_close($ifs);

	header('Location: index.php');
	die();
}

mysql_close($ifs);

