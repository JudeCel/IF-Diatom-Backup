<?php 

require_once('Connections/ifs.php');     
require_once('core.php');
require_once('models/participant-email-model.php');

$filename = basename($_SERVER['PHP_SELF']);
$created = date('Y-m-d H:i:s');

// get participant id from URL
if(isset($_GET['participant_id']))
{
	$participant_id =  strip_tags(mysql_real_escape_string($_GET['participant_id']));
	
	// get the participant_reply_id from URL
	if(isset($_GET['participant_reply_id']))
	{
		//Set participant reply id and level of interest
		$participant_reply_id = 5;
		$interested =  strip_tags(mysql_real_escape_string($_GET['participant_reply_id']));

		//update the participant table
		$lid = build_participant_list($database_ifs, $ifs, $participant_id, true, $participant_reply_id);

		//Save the participant interest
		save_participant_interest($database_ifs, $ifs, $participant_id, $interested);
	}
}

//Page properties
$page = 'Invitation Reply';
$title = $page;
$main_script = false;
$other_content = 'invitation_reply';

$grid = false;
$validate = false;
$inline_scripting = false;

$sub_navigation = false;
$sub_nav_url = false;
$sub_id = null;
$sub_group = null;

$content = '<p>Thank you, your response has been recorded.</p>';

require_once('views/not_logged_in.php');

mysql_close($ifs);
