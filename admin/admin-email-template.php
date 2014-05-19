<?php
require_once('Connections/ifs.php');
require_once('core.php');
require_once('models/participant-email-model.php');
require_once('models/users_model.php');

/* Get the email admin */
if (!isset($_GET['admin_email_type'])) {
    return FALSE;
} else {
    $email_type = $_GET['admin_email_type'];
}

/* Find the details for the admin email and if it is not empty, specify number of details */
$details = get_details_for_template($email_type);
$details_num = 0;
if (!empty($details)) {
    $details_num = count($details);
}

/* Set buttons for email */
$email_buttons = get_email_buttons_for_template($email_type);

/* Logo for Insiderfocus */
$thumbnail_path = $ADMIN_URL .  "images/logoDefaultInsiderfocus.jpg";

/* Set the message information */
$row_retSessionEmail = array();
$row_retSessionEmail['greeting'] = 'Dear [[First Name]] [[Last Name]]';
$row_retSessionEmail['email_message_top'] = get_textual_information_for_admin_email($email_type);
$row_retSessionEmail['subject'] = 'Insiderfocus | ' . ucwords(str_replace('_', ' ', $email_type));

/* Load View */
require_once('views/email_template.php');

mysql_close($ifs);
