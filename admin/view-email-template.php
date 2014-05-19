<?php
require_once('Connections/ifs.php');
require_once('core.php');
require_once('models/participant-email-model.php');
require_once('models/users_model.php');
require_once('models/brand_model.php');

/* Check if logged in */
if ((!isset($_GET['session_id']) && !isset($_GET['admin_email_type'])) || (isset($_GET['session_id']) && !isset($_GET['admin_email_type']) && !isset($_GET['email_type_id']))) {
    return FALSE;
}

$session_id = NULL;
if (isset($_GET['session_id']))
    $session_id = $_GET['session_id'];

$email_type_id = NULL;
if (isset($_GET['email_type_id']))
    $email_type_id = $_GET['email_type_id'];

$list_id = NULL;
if (isset($_GET['list_id']))
    $list_id = $_GET['list_id'];

$admin_email_type = NULL;
if (isset($_GET['admin_email_type']))
    $admin_email_type = $_GET['admin_email_type'];

$preview = FALSE;
if (isset($_GET['preview'])) {
    $preview = TRUE;
}

/* Get email details */
if ($email_type_id) {
    $details = get_details_for_template($email_type_id);
    $details_num = 0;
    if (!empty($details)) {
        $details_num = count($details);
    }
}

/* Get Brand Information */
$brand_project = retrieve_brand_project($database_ifs, $ifs);
$row_retBrandProject = mysql_fetch_assoc($brand_project);
$totalRows_retBrandProject = mysql_num_rows($brand_project);

if ($session_id && $email_type_id) {
    $row_retSessionEmail = array();
    $totalRows_retSessionEmail = 0;

    /* Find specific email template */
    $retSessionEmail = retrieve_email_template($session_id, $email_type_id, $database_ifs, $ifs);

    //Ensure that the template has been found
    if ($retSessionEmail && !is_string($retSessionEmail)) {
        $row_retSessionEmail = mysql_fetch_assoc($retSessionEmail); //get email template
        $totalRows_retSessionEmail = mysql_num_rows($retSessionEmail); //number of rows available
    }

    $email_buttons = get_email_buttons_for_template($email_type_id, $list_id);
} elseif ($admin_email_type) {
    /* Set the message information */
    $row_retSessionEmail = array();
    $row_retSessionEmail['greeting'] = 'Dear [[First Name]] [[Last Name]]';
    $row_retSessionEmail['email_message_top'] = get_textual_information_for_admin_email($admin_email_type);
    $row_retSessionEmail['subject'] = 'Insiderfocus | ' . ucwords(str_replace('_', ' ', $admin_email_type));
}

if (isset($row_retBrandProject['Session_Name'])) {
    $brand_name = $row_retBrandProject['Session_Name'];
}

//Added to get correct root.
$root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

/* Get thumbanil path */
if (isset($row_retBrandProject['logo_url'])) {
    $thumbnail_path = $root . '/' . $row_retBrandProject['logo_url'];
} else {
    $thumbnail_path = $root .  "/images/logoDefaultInsiderfocus.jpg";
}

if ($admin_email_type) {
    /* Find the details for the admin email and if it is not empty, specify number of details */
    $details = get_details_for_template($admin_email_type);
    $details_num = 0;
    if (!empty($details)) {
        $details_num = count($details);
    }

    /* Set buttons for email */
    $email_buttons = get_email_buttons_for_template($admin_email_type);
}

if ($session_id && $email_type_id) {
    /* Get Facilitator Properties */
    $facilitator_firstname = '';
    $facilitator_lastname = '';
    $facilitator_phone = '';
    $facilitator_email = '';
    $brand_name = '';

    if (isset($row_retBrandProject['user_id'])) {
        /* Get Facilitator */
        $facilitator_result = retrieve_users($database_ifs, $ifs, true, true, $row_retBrandProject['user_id']);
        if (!empty($facilitator_result)) {
            $facilitator = mysql_fetch_assoc($facilitator_result);

            $facilitator_firstname = $facilitator['name_first'];
            $facilitator_lastname = $facilitator['name_last'];
            $facilitator_phone = ($facilitator['uses_landline'] ?  $facilitator['phone'] : $facilitator['mobile']);
            $facilitator_email = $facilitator['email'];
        }
    }
}

/* Load View */
require_once('views/email_template.php');

mysql_close($ifs);



	