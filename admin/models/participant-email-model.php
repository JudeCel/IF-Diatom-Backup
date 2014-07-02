<?php
$tags_available = array('First Name', 'Last Name', 'Email', 'Start Date', 'End Date');

$session_id = NULL;
if (isset($_GET['session_id'])) {
    $session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
}

$email_type_id = NULL;
if (isset($_GET['email_type_id'])) {
    $email_type_id = strip_tags(mysql_real_escape_string($_GET['email_type_id']));
}

/**
 * Find Email Template
 **/
function retrieve_email_template($sid, $type, $database, $ifs)
{
    $table = 'session_emails';

    //retrieve session's email if it exists
    mysql_select_db($database, $ifs);
    $query_retSessionEmail =
        "SELECT
 				session_emails.*
  			FROM
 				session_emails
 		  	INNER JOIN
 		  		sessions ON (sessions.id = session_emails.session_id)
 		  	INNER JOIN
 		  		brand_project_preferences ON (sessions.brand_project_id = brand_project_preferences.brand_project_id)
  			WHERE
 				session_emails.session_id = $sid AND
 				session_emails.email_type_id = $type";

    /* Run Query */
    $retSessionEmail = mysql_query($query_retSessionEmail, $ifs) or die(mysql_error());

    /* Check if result was returned */
    if (mysql_num_rows($retSessionEmail) > 0) {
        return $retSessionEmail;
    } else {
        mysql_select_db($database, $ifs);
        $query_retSessionEmail =
            "SELECT
 					*
 				FROM
 					session_emails
 				WHERE
 					session_emails.session_id = $sid AND
 					session_emails.email_type_id = $type";
        /* Run Query */
        $retSessionEmail = mysql_query($query_retSessionEmail, $ifs) or die(mysql_error());

        if (mysql_num_rows($retSessionEmail) > 0) {
            return $retSessionEmail;
        } else {
            return FALSE;
        }
    }
}

/**
 * Get Session Name
 **/
function get_session_name($database, $ifs, $session_id)
{
    if ($session_id) {
        mysql_select_db($database, $ifs); //select database

        //Find conpany name for a specific company id
        $session_name_sql = sprintf(
            "SELECT
                name
            FROM
                sessions
            WHERE
                id = %d",
            $session_id
        );

        $result_session = mysql_query($session_name_sql, $ifs);

        //Check if company was found
        if ($result_session) {
            if (mysql_num_rows($result_session) > 0) {
                return $result_session;
            } else {
                return FALSE;
            }
        } else {
            //If there was an error, return it
            $error = mysql_error();

            return $error;
        }
    } else { //No company id set
        return FALSE;
    }
}

/**
 * Get session id used by a particular user
 **/
function get_session_id_by_user($database, $ifs, $user_login_id, $staff = false)
{
    //If no user login id is found
    if (!$user_login_id) {
        return FALSE;
    }

    //The mysql query used to retrieve the session id
    if (!$staff) {
        $session_sql = sprintf(
            "SELECT
                pl.session_id
            FROM
                participant_lists AS `pl`
            WHERE
                pl.ul_id = %d",
            $user_login_id
        );
    } else {
        $session_sql = sprintf(
            "SELECT
                ss.session_id
            FROM
                session_staff AS `ss`
                INNER JOIN user_logins AS `ul` ON (ss.user_id = ul.user_id)
            WHERE
                ul.id = %d
            GROUP BY
                ss.id",
            $user_login_id
        );
    }

    mysql_select_db($database, $ifs);

    $results = mysql_query($session_sql, $ifs); //run the mysql query

    if ($results) {
        if (mysql_num_rows($results)) { //if rows found, return result
            return $results;
        } else {
            return FALSE;
        }
    } else {
        $mysql_error = mysql_error();
        if ($mysql_error) { //if a mysql error was detected
            return $mysql_error;
        } else {
            return FALSE;
        }
    }
}

/**
 * Get Details Information
 **/
function get_details_for_template($type)
{
    $details = array();

    switch ($type) {
        case 1:
            $details = array(
                'This session runs from [[Start Date]] to [[End Date]].',
                'To confirm your participation between these times, please click on Accept Invitation.',
                "If you're not available this time, but interested in the future, please click on Not This Time.",
                'If not convenient for you at any time, please click on Not Interested.'
            );
            break;
        case 2:
            $login_details = '';
            $login_details .= '[b[Check-In]] from the start time, with your ...' . "\n";
            $login_details .= 'Username: [[Username]]' . "\n";
            $login_details .= 'Password: [[Password]]';

            $details = array(
                'This session runs from [b[Start Date]] to [b[End Date]].',
                'Please click the two buttons below to get you started.',
                '[b[Read T&Cs]] for the Terms & Conditions of your participation, and assurance of your Privacy Protection.',
                $login_details,
                'Checking in to the session means you understand and accept the T&Cs.'
            );
            break;
        case 5:
            $details = array(
                'If you would like to participate in another session in the future, please click on Interested.',
                'if you no longer wish  to participate, please click on Not Interested'
            );
            break;
        case 'admin_register':
            /* Login Details for user */
            $login_details = '';
            $login_details .= 'Username: [[Username]]' . "\n";
            $login_details .= 'Password: [[Password]]' . "\n";

            /* If company details are needed some time or later */
            $company_details = '';
            $company_details .= 'Your company details are the following...' . "\n";
            $company_details .= 'Name: [[Company Name]]' . "\n";
            $company_details .= 'Street: [[Company Street]]' . "\n";
            $company_details .= 'Suburb: [[Company Suburb]]' . "\n";
            $company_details .= 'State: [[Company State]]' . "\n";
            $company_details .= 'Post Code: [[Company Postcode]]' . "\n";
            $company_details .= 'Country: [[Company Country]]' . "\n";

            $details = array(
                $login_details
            );
            break;
        case 'facilitator_register':
            /* Login Details for user */
            $login_details = '';
            $login_details .= 'Username: [[Username]]' . "\n";
            $login_details .= 'Password: [[Password]]' . "\n";

            $details = array(
                $login_details
            );
            break;
        case 'observer_register':
            /* Login Details for user */
            $login_details = '';
            $login_details .= '[b[Login]] from the start time to observe the Session' . "\n";
            $login_details .= 'Username: [[Username]]' . "\n";
            $login_details .= 'Password: [[Password]]' . "\n";

            /* Observer Details */
            $observer_details = '';
            $observer_details .= 'For any follow-up questions or feedback, please contact the Facilitator [b[Facilitator Name]]';
            $observer_details .= ' at [[Facilitator Email]] or call/text on [b[Facilitator Mobile]].';

            /* ObserverRole */
            $observer_role = '';
            $observer_role .= 'During the Session you can only view the conversation, not interact with the Participants.';
            $observer_role .= 'They will not know that you personally are viewing, only that there will be Company Observers.' . "\n";

            $details = array(
                $login_details,
                $observer_role,
                'For Privacy T&amp;C requirements you will not be able to identify the Participants personally, other than by first name.',
                $observer_details
            );
            break;
        case 'request_password_change':
            $details = array(
                'Click on [b[Change Password]] to receive a new password'
            );
            break;
        case 'forget_password':
            $details = array(
                '[b[Login]] to start using your new password',
                'Your new password is: [b[Password]]'
            );
            break;
    }

    return $details;
}

/**
 * Get Textual Information for Admin Email Template
 **/
function get_textual_information_for_admin_email($email_type)
{
    $text = '';

    /* Set message */
    switch ($email_type) {
        case 'admin_register':
            $text = 'You are now registered as Global Administrator for the InsiderFocus agreement with
								 [[Company Name]] from [[Start Date]] to [[End Date]].' . "\n";
            break;
        case 'facilitator_register':
            $text = 'You are now registered as Facilitator for the ([[Brand Name]]) [[Session Name]] Session, from [[Start Date]] to [[End Date]].';
            break;
        case 'observer_register':
            $text = 'This is your Observer Ticket for the ([[Brand Name]]) [[Session Name]] Session, from [[Start Date]] to [[End Date]].';
            break;
        case 'request_password_change':
            $text = 'A request was made to change your password for [[Activity Name]].';
            break;
        case 'forget_password':
            $text = 'Your password for [[Activity Name]]  has been reset. Please notice your new password in the details below.' . "\n";
            $text .= '[[Staff]]';
            break;
    }

    return $text;
}

/**
 * Get Email Buttons for Template
 **/
function get_email_buttons_for_template($type, $list_id = null)
{
    $email_buttons = array();

    $root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

    //Check if root includes ifs-test
    if (preg_match('/^\/ifs-test/', $_SERVER['REQUEST_URI'])) {
        $root .= '/ifs-test';
    }

    /* Create Button according to Array */
    switch ($type) {
        case 1:
            $email_reply_id_1 = ($list_id ? $root . '/participantEmailReply.php?participant_lists_id=' . $list_id . '&participant_reply_id=1' : '');
            $email_reply_id_2 = ($list_id ? $root . '/participantEmailReply.php?participant_lists_id=' . $list_id . '&participant_reply_id=3' : '');
            $email_reply_id_3 = ($list_id ? $root . '/participantEmailReply.php?participant_lists_id=' . $list_id . '&participant_reply_id=2' : '');

            $email_buttons = array(
                create_button_for_array('/images/new_layout/accept.png', $email_reply_id_1, 'Accept Invitation'),
                create_button_for_array('/images/new_layout/interested.png', $email_reply_id_2, 'Not This Time'),
                create_button_for_array('/images/new_layout/reject.png', $email_reply_id_3, 'Refuse Invitation'),
            );
            break;
        case 2:
            $pdf_link = $root . '/Terms_and_Conditions.pdf';
            $email_reply_id_1 = '[[Login Link]]';

            $email_buttons = array(
                create_button_for_array('/images/new_layout/terms.png', $pdf_link, 'Terms and Conditions'),
                create_button_for_array('/images/new_layout/check_in.png', $email_reply_id_1, 'Check-in')
            );
            break;
        case 5:
            $email_reply_id_1 = ($list_id ? $root . '/participantEmailReply-end.php?participant_id=' . $list_id . '&participant_reply_id=1' : '');
            $email_reply_id_2 = ($list_id ? $root . '/participantEmailReply-end.php?participant_id=' . $list_id . '&participant_reply_id=2' : '');

            $email_buttons = array(
                create_button_for_array('/images/new_layout/int_1.png', $email_reply_id_1, 'Interested'),
                create_button_for_array('/images/new_layout/int_2.png', $email_reply_id_2, 'Not Interested')
            );
            break;
        case 'admin_register':
            $login_link = $root;

            $email_buttons = array(
                create_button_for_array('/images/new_layout/email_login.png', $login_link, 'Login')
            );
            break;
        case 'facilitator_register':
            $login_link = $root;

            $email_buttons = array(
                create_button_for_array('/images/new_layout/email_login.png', $login_link, 'Login')
            );
            break;
        case 'observer_register':
            $login_link = $root;
            $pdf_link = $login_link . '/Terms_and_Conditions.pdf';

            $email_buttons = array(
                create_button_for_array('/images/new_layout/email_login.png', $login_link, 'Login')
            );
            break;
        case 'request_password_change':
            $email_buttons = array(
                create_button_for_array('/images/new_layout/change_password.png', '[[Link]]', 'Change Password')
            );
            break;
        case 'forget_password':
            $login_link = $root;

            $email_buttons = array(
                create_button_for_array('/images/new_layout/email_login.png', $login_link, 'Login')
            );
            break;
        default:
            /* Nothing */
    }

    return $email_buttons;
}

/**
 * Create Button for Array
 **/
function create_button_for_array($src = null, $href = null, $alt = null, $title = null)
{
    if ($src && $alt) {
        /* Create StdClass */
        $button = new StdClass;
        $button->src = $src;
        $button->alt = $alt;
        $button->title = ($title ? $title : $alt);
        $button->href = $href;

        return $button;
    } else {
        return FALSE;
    }
}

/**
 * Create Unique Password
 **/
function create_unique_password()
{
    $pretrimmedrandom = md5(uniqid(mt_rand(), true));
    $password = substr($pretrimmedrandom, 0, 7);

    return $password;
}

/**
 * Send admin email to user
 **/
function send_admin_email_to_user($database, $ifs, $email, $user_id, $name, $subject, $email_type, $replacements)
{
    $from = 'donotreply@insiderfocus.com';

    /* Get content for message */
    $message = store_view_in_var(
        'admin-email-template.php',
        array(
            'admin_email_type' => $email_type
        )
    );

    $tags_available = get_tags_for_admin_emails($email_type);

    /* Parse Template for tags */
    $message = parse_tags_for_template($message, $replacements, $tags_available);

    $sent = sendMail($database, $ifs, $email, $name, $subject, $message, $from, $user_id);

    return $sent;
}

/**
 * Get Tags avaialable for admin emails
 **/
function get_tags_for_admin_emails($email_type)
{
    $tags = array();

    switch ($email_type) {
        case 'admin_register':
            $tags = array(
                'First Name',
                'Last Name',
                'Username',
                'Password',
                'Company Name',
                'Company Street',
                'Company Suburb',
                'Company State',
                'Company Postcode',
                'Company Country',
                'Start Date',
                'End Date'
            );
            break;
        case 'request_password_change':
            $tags = array('First Name', 'Last Name','Activity Name', 'Link');
            break;
        case 'forget_password':
            $tags = array('First Name', 'Last Name', 'Staff','Activity Name',  'Password');
            break;
    }

    return $tags;
}

/**
 * Get Content of View and store it in var
 **/
function store_view_in_var($url = null, $arguments = array())
{
    if ($url) {
        $root = $_SERVER['HTTP_HOST'];

        //Tag
        $pos = strpos($_SERVER['HTTP_HOST'], '/');
        if ($pos === false) {
            $header = array("Host:" . $root); //set header
        } else {
            $header = array("Host:" . substr($_SERVER['HTTP_HOST'], 0, $pos));
        }

        $ip = gethostbyname($root); //get ip address

        //Check if root includes ifs-test
        if (preg_match('/^\/ifs-test/', $_SERVER['REQUEST_URI'])) {
            $ip .= '/ifs-test';
        }

        if(strpos($url, '/')!==0)
            $url='/'.$url;
        $final_url = $ip . $url; //the url to use with the curl

        /* Build arguments */
        if (!empty($arguments)) {
            //$arg_values = array();
            foreach ($arguments as $key => $value) {
                $arg_values[] = $key . '=' . $value;
            }

            $final_url .= '?' . implode('&', $arg_values);
        }

        session_write_close();

        /* Set cURL */
        $c = curl_init();
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_HTTPHEADER, $header);
        curl_setopt($c, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $final_url);
        curl_setopt($c, CURLOPT_COOKIE, session_name() . "=" . session_id() . ";");
        $contents = curl_exec($c);

        curl_close($c);

        /* Return $contents */
        if ($contents) {
            return $contents;
        } else {
            return FALSE;
        }
    }
}

/**
 * Parse Tags to work with email template
 **/
function parse_tags_for_template($content, $replacements = array(), $tags_available = null)
{
    /* If no tags are set, set default tags */
    if (!$tags_available) {
        $tags_available = array('First Name', 'Last Name', 'Email', 'Start Date', 'End Date', 'Login Link', 'Username', 'Password', 'Brand');
    }

    $replacements_keys = array_keys($replacements);

    foreach ($tags_available as $tag) {
        /* Check if replacemnt exists */
        if (in_array($tag, $replacements_keys)) {
            $replace = $replacements[$tag];

            /* Replace tag in content in general */
             $content = preg_replace("/\[\[" . $tag . "\]\]/", $replace, $content);
            $content = preg_replace("/\[b\[" . $tag . "\]\]/", '<strong>' . $replace . '</strong>', $content);
            $content = preg_replace("/\[i\[" . $tag . "\]\]/", '<em>' . $replace . '</em>', $content);
        }
    }

    /* Replace content if bold */
    $content = preg_replace("/\[b\[(.*)\]\]/", '<strong>' . "$1" . '</strong>', $content);
    /* Replace content if italic */
    $content = preg_replace("/\[i\[(.*)\]\]/", '<em>' . "$1" . '</em>', $content);

    return $content;
}

function isValidURL($url)
{
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

/**
 * Sent user to specific location after update
 **/
function goto_after_upload($updated, $session_id, $email_type_id, $code = '1')
{
    /* Check if previewed */
    if (isset($_POST['btnPreview_x']) && isset($_POST['btnPreview_y']) && $updated) {
        $root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

        //Check if root includes ifs-test
        if (preg_match('/^\/ifs-test/', $_SERVER['REQUEST_URI'])) {
            $root .= '/ifs-test';
        }

        $updateGoTo = $root . '/view-email-template.php?session_id=' . $session_id . '&preview=1&email_type_id=' . $email_type_id;
    } else {
        /* If successful, go to */
        $updateGoTo = "participant-email-template-admin.php?" . $_SERVER['QUERY_STRING'];
        $update_get = '0';
        if ($updated === TRUE) {
            $update_get = $code;
        }
        $updateGoTo .= '&update=' . $update_get;
    }

    header(sprintf("Location: %s", $updateGoTo));
}

/**
 * Either Update or Insert a new email tenplate
 **/
function update_email_template($sid, $database, $ifs, $template)
{
    /* Check if submit was pressed */
    $created = date('Y-m-d H:i:s');

    $update_array = array();

    if (isset($_GET['session_id'])) {
        $update_array['session_id'] = strip_tags(mysql_real_escape_string($_GET['session_id']));
    }

    if (isset($_GET['email_type_id'])) {
        $update_array['email_type_id'] = strip_tags(mysql_real_escape_string($_GET['email_type_id']));
    }

    /* Subject and Messages */
    if (isset($_POST['subject'])) {
        $update_array['subject'] = "'" . strip_tags(mysql_real_escape_string($_POST['subject'])) . "'";
    }

    if (isset($_POST['subject'])) {
        $update_array['email_message_top'] = "'" . strip_tags(mysql_real_escape_string($_POST['email_message_top'])) . "'";
    }

    if (isset($_POST['email_message_bottom'])) {
        $update_array['email_message_bottom'] = "'" . strip_tags(mysql_real_escape_string($_POST['email_message_bottom'])) . "'";
    }

    if (isset($_POST['greeting'])) {
        $update_array['greeting'] = "'" . strip_tags(mysql_real_escape_string($_POST['greeting'])) . "'";
    }
    if (isset($_POST['detail_1'])) {
        $update_array['detail_1'] = "'" . strip_tags(mysql_real_escape_string($_POST['detail_1'])) . "'";
    }
    if (isset($_POST['detail_2'])) {
        $update_array['detail_2'] = "'" . strip_tags(mysql_real_escape_string($_POST['detail_2'])) . "'";
    }
    if (isset($_POST['detail_3'])) {
        $update_array['detail_3'] = "'" . strip_tags(mysql_real_escape_string($_POST['detail_3'])) . "'";
    }
    if (isset($_POST['detail_4'])) {
        $update_array['detail_4'] = "'" . strip_tags(mysql_real_escape_string($_POST['detail_4'])) . "'";
    }
    if (isset($_POST['detail_5'])) {
        $update_array['detail_5'] = "'" . strip_tags(mysql_real_escape_string($_POST['detail_5'])) . "'";
    }
    if (isset($_POST['detail_6'])) {
        $update_array['detail_6'] = "'" . strip_tags(mysql_real_escape_string($_POST['detail_6'])) . "'";
    }

    if (isset($_POST['email_video'])) {
        if (!isValidURL($_POST['email_video']) && $_POST['email_video']) {
            return false;
        }

        $update_array['email_video'] = "'" . strip_tags(mysql_real_escape_string($_POST['email_video'])) . "'";
    }

    $update_array['created'] = "'" . $created . "'";

    if (($template && !mysql_num_rows($template)) || !$template) {
        /* Insert Email Template */
        $sql = "INSERT INTO session_emails (%s)" . "\n";
        $sql .= " VALUES (%s)";

        $update_keys = array_keys($update_array);
        $update_values = array_values($update_array);

        $insert2SQL = sprintf($sql, implode(', ', $update_keys), implode(', ', $update_values));

        mysql_select_db($database, $ifs);
        $Result3 = mysql_query($insert2SQL, $ifs);

        //retrieve sid
        if ($Result3) {
            $sid = mysql_insert_id();
        }
    } else {
        $sql = "UPDATE session_emails" . "\n";
        $sql .= " SET %s" . "\n";
        $sql .= " WHERE session_emails.id = %d";

        $update_values = array();
        foreach ($update_array as $key => $value) {
            $update_values[] = $key . " = " . $value;
        }

        $insert2SQL = sprintf($sql, implode(', ', $update_values), $sid);

        mysql_select_db($database, $ifs);
        $Result3 = mysql_query($insert2SQL, $ifs);
    }

    if (isset($_FILES['email_image']['error']) && !$_FILES['email_image']['error'] && $sid) {
        $file_upload = update_email_image($sid, 'email_image', $database, $ifs);

        if (is_string($file_upload)) {
            $_SESSION['notification'] = $file_upload;
        }
    }

    /* Check if insert is successful */
    if (!$Result3) {
        return mysql_error();
    } else {
        return TRUE;
    }
}

/**
 * Update Email Image
 **/
function update_email_image($sid, $field, $database, $ifs, $sql = FALSE)
{
    if ((isset($_FILES[$field]['error'])) && (!$_FILES[$field]['error'])) {
        $filename = $_FILES[$field]['name'];
        $filetype = strtolower($_FILES[$field]['type']);

        /* Detect if appropriate file type */
        $allowed_filetypes = array(
            'image/jpg' => 'JPG',
            'image/jpeg' => 'JPEG',
            'image/png' => 'PNG',
            'image/gif' => 'GIF'
        );


        /* If not an appropriate file the return message */
        if (!in_array($filetype, array_keys($allowed_filetypes))) {
            return $filetype . ' is not an allowed filetype. Please try a different image';
        }


        $path = $_SERVER['DOCUMENT_ROOT'] . '/upload/email/' . $filename;

        /* Delete file if exits */
        if (file_exists($path)) {
            unlink($path);
        }

        $upload = image_upload('upload/email/', $field, 516, 153); //upload file

        $update_value = 'email_image = ';

        if ($upload) {
            $update_value .= "'" . strip_tags(mysql_real_escape_string($upload)) . "'";

            /* Image Description */
            if (isset($_POST['email_image_desc'])) {
                $update_value .= ', email_image_desc = ';
                $update_value .= "'" . strip_tags(mysql_real_escape_string($_POST['email_image_desc'])) . "'";
            }

            /* Send array if being used from another update function */

            if ($sql) {
                $update_array = array();

                $update_array_str = explode(', ', $update_value);

                /* Set keys and values */
                foreach ($update_array_str as $value) {
                    $db_values = explode(' = ', $value); //explode string

                    $update_array[$db_values[0]] = $db_values[1]; //set key and values for update array
                }

                return $update_array;
            } else {
                /* SQL Query */
                $sql = "UPDATE session_emails" . "\n";
                $sql .= " SET %s" . "\n";
                $sql .= " WHERE session_emails.id = %d";

                $insert2SQL = sprintf($sql, $update_value, $sid); //insert values in insert query

                mysql_select_db($database, $ifs);
                $Result3 = mysql_query($insert2SQL, $ifs);

                /* Check if insert is successful */
                if (!$Result3) {
                    return mysql_error();
                } else {
                    return TRUE;
                }
            }

        } else {
            if ((isset($_FILES[$field]['error'])) && ($_FILES[$field]['error'] != 0)) {
                return $_FILES[$field]['error'];
            } else {
                return 'The image could not be uploaded. Please make sure you\'ve selected an appropriate file'; //could not upload correctly
            }
        }
    }
}

/**
 * Update Email Video
 **/
function update_email_video($sid, $database, $ifs, $template)
{
    if ((($template && !mysql_num_rows($template)) || !$template) && !$sql) { //check if record exists or not
        return FALSE;
    } else {
        $sql = "UPDATE session_emails" . "\n";
        $sql .= " SET %s" . "\n";
        $sql .= " WHERE session_emails.id = %d";

        $update_value = 'email_video = ';

        /* Facilitator */
        if (isset($_POST['email_video'])) {
            if (!isValidURL($_POST['email_video']) && $_POST['email_video']) {
                return false;
            }

            $update_value .= "'" . cleanFromEditor($_POST['email_video']) . "'";
        }

        $insert2SQL = sprintf($sql, $update_value, $sid);

        mysql_select_db($database, $ifs);
        $Result3 = mysql_query($insert2SQL, $ifs);

        /* Check if insert is successful */
        if (!$Result3) {
            return mysql_error();
        } else {
            return TRUE;
        }
    }
}

/**
 * Retrive participant list
 **/
function retrieve_participant_list($database, $ifs, $participant_lists_id = null, $session_id = null, $left_out = false)
{
    $filename = basename($_SERVER['PHP_SELF']);
    mysql_select_db($database, $ifs);

    if (!$participant_lists_id && !$session_id) {
        return FALSE;
    }

    $query_retEmailReply = sprintf(
        "SELECT
          participants.id As participant_id,
          users.id As user_id,
          users.email,
          participant_lists.participant_reply_id,
          participant_lists.session_id,
          users.name_first,
          users.name_last,
          users.invites,
          users.invites_accepted,
          users.invites_not_now,
          users.invites_not_interested,
          users.invites_no_reply
        FROM
          participant_lists
          INNER JOIN participants ON (participant_lists.participant_id = participants.id)
          INNER JOIN users ON (participants.user_id = users.id)
        WHERE
            %s = %d%s",
        ($participant_lists_id ? 'participant_lists.id' : 'participant_lists.session_id'),
        ($participant_lists_id ? $participant_lists_id : $session_id),
        ($left_out ? " AND participant_lists.participant_reply_id IS NULL" : "")
    );

    logToFile($filename, $query_retEmailReply, true);

    $retEmailReply = mysql_query($query_retEmailReply, $ifs);

    if ($retEmailReply && mysql_num_rows($retEmailReply) > 0) {
        return $retEmailReply;
    } else {
        return mysql_error();
    }

}

/**
 * Get total number of participants
 **/
function get_total_participants($database, $ifs, $session_id = NULL)
{
    if (isset($_GET['session_id'])) {
        $session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
    }

    //get the total participants already there in the session
    mysql_select_db($database, $ifs);
    $query_retTotalParticipants = sprintf(
        "SELECT *
        FROM
          participant_lists
        WHERE
         participant_lists.participant_reply_id = 1
         AND participant_lists.session_id = %d", $session_id);

    $retTotalParticipants = mysql_query($query_retTotalParticipants, $ifs);

    if ($retTotalParticipants && mysql_num_rows($retTotalParticipants) > 0) {
        return mysql_num_rows($retTotalParticipants);
    } else {
        return FALSE;
    }
}

/**
 * Build participant list
 **/
function build_participant_list($database, $ifs, $participant_id = null, $update = false, $participant_reply_id = null, $session_id = null)
{
    if (!$session_id && isset($_GET['session_id'])) {
        $session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
    }

    $created = date('Y-m-d H:i:s');

    /* Check if participant_id and session_id is available */
    if ($participant_id && ($session_id || $update)) {
        mysql_select_db($database, $ifs);

        if (!$update) {
            /* Write query */
            $insert_participant_id = sprintf(
                "INSERT INTO
                    participant_lists (session_id, participant_id, created)
                VALUES (%d, %d, '%s')", $session_id, $participant_id, $created);
        } elseif ($participant_reply_id && $participant_id) {
            $participant_list_id = $participant_id; //show that this id is actually the participant_list_id

            $insert_participant_id = sprintf(
                "UPDATE
                    participant_lists
                SET
                    participant_reply_id = %d,
                    updated ='%s'
                WHERE id = %d", $participant_reply_id, $created, $participant_list_id);

        } else {
            return FALSE;
        }

        /* Check if insert was successful */
        $result = mysql_query($insert_participant_id, $ifs);
        if ($result) {
            if (!$update) {
                $participant_lists_id = mysql_insert_id($ifs);

                return $participant_lists_id;
            } else {
                return TRUE;
            }
        }
    } else {
        return FALSE;
    }
}

/**
 * Save that the participant is interested
 **/
function save_participant_interest($database, $ifs, $participant_id, $interest)
{
    mysql_select_db($database, $ifs); //select database

    //Update the interest id
    $interest_sql = sprintf(
        "UPDATE
            participants
        SET
            interested = %d
        WHERE
            id = %d",
        $interest,
        $participant_id
    );

    $results = mysql_query($interest_sql);

    //Return results
    return $results;
}

/**
 * Iterate Number of Invites and include reply
 **/
function iterate_number_of_invites($database, $ifs, $user_id = null, $participant_reply_id = null, $invites = array())
{
    /* Make sure the participant_reply_id and user_id is set */
    if (!$participant_reply_id && !$user_id) {
        return FALSE;
    }

    mysql_select_db($database, $ifs);

    /* Find which field to update, alongside with invites */
    $field = null;
    switch ($participant_reply_id) {
        case 1:
            $field = 'accepted';
            break;
        case 2:
            $field = 'not_interested';
            break;
        case 3:
            $field = 'not_now';
            break;
        default:
            $field = 'no_reply';
    }

    /* Make sure the total is available */
    if (!isset($invites['total']) || !isset($invites[$field])) {
        return FALSE;
    }
    $total = $invites['total']; //total invites
    $field_value = $invites[$field]; //field_value

    $invites_query = sprintf(
        "UPDATE
            users
        SET
            invites = %d,
            %s = %d
        WHERE
            id = %d",
        ($total + 1),
        'invites_' . $field,
        ($field_value + 1),
        $user_id
    );

    $result = mysql_query($invites_query, $ifs); //run query

    //return true or mysql query
    if ($result) {
        return TRUE;
    } else {
        return mysql_error();
    }
}

/**
 * Check if the avatar is at a default state
 **/
function check_if_avatar_default_state($user, $user_id)
{
    //Ensure taht valid values has been set
    if (!$user || !$user_id) {
        return FALSE;
    }

    //Check if avatar info is set
    if (!isset($user['avatar_info'])) {
        return FALSE;
    }

    $gender = strtolower($user['Gender']);

    //Detect the default avatar info
    switch ($gender) {
        case 'male':
            $avatar_info = '0:4:0:0:0:0';
            break;
        case 'female':
            $avatar_info = '0:4:6:6:5:0';
            break;
    }

    //Update that the facilitator has vited the green room
    if ($user['avatar_info'] == $avatar_info) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/* Set Last Session Invited */
function set_last_session_invited_name($database, $ifs, $session_name, $user_id)
{
    /* Check if session name was set */
    if (!$session_name && !$user_id) {
        return FALSE;
    }

    mysql_select_db($database, $ifs); //select database

    /* Update the users last session */
    $last_session_query = sprintf(
        "UPDATE
            users
        SET
            last_invite_name = '%s'
        WHERE
            id = %d",
        $session_name,
        $user_id
    );

    $result = mysql_query($last_session_query, $ifs);

    //return true or mysql query
    if ($result) {
        return TRUE;
    } else {
        return mysql_error();
    }
}

/**
 * Get participant colour when partcipant accepts
 **/
function get_participant_colour($database, $ifs, $colours_used)
{
    $filename = basename($_SERVER['PHP_SELF']);

    //Encode colours used as json
    if (is_string($colours_used)) {
        if ($colours_used) {
            $colours_used = (array)json_decode($colours_used);
        } else {
            $colours_used = array();
        }
    }

    //Make sure there are no empty values
    if (!empty($colours_used)) {
        foreach ($colours_used as $key => $colour) {
            if (!$colour) {
                unset($colours_used[$key]);
            }
        }

        //explode the array in preparation for query
        $colours_query = implode(', ', $colours_used);
    }

    //get the color if participant accepts
    mysql_select_db($database, $ifs);
    $query_retColour = sprintf(
        "SELECT
          participant_colour_lookup.id
        FROM
          participant_colour_lookup
        %s",
        (!empty($colours_used) && is_array($colours_used) ? 'WHERE id NOT IN(' . $colours_query . ')' : '')
    );

    $retColour = mysql_query($query_retColour, $ifs);

    if ($retColour && mysql_num_rows($retColour) > 0) {
        $colours_avail = array();

        //loop through results to prepare for random selection
        while ($row = mysql_fetch_assoc($retColour)) {
            $colours_avail[] = $row['id'];
        }

        $r_num = mt_rand(0, (count($colours_avail)) - 1); //random number

        $participant_colour = $colours_avail[$r_num]; //Get a specific colours

        return $participant_colour;
    } else {
        return mysql_error();
    }
}

function upload_csv($URL, $brand_project_id, $database, $ifs)
{
    $messages = array();
    $passes = true;

    //Autodetect line endings
    ini_set('auto_detect_line_endings', true);

    //Get CSV file
    $file = NULL;
    if (isset($_FILES['csvFile'])) {
        $file = $_FILES['csvFile'];
    } else {
        $messages[] = 'No CSV file is available. Please try uploading the file again';
    }

    //Discover any errors
    $error = NULL;
    if (isset($file['error']))
        $error = $file['error'];

    //File Type
    $type = NULL;
    if (isset($file['type']))
        $type = $file['type'];

    //Size of file
    $size = NULL;
    if (isset($file['size']))
        $size = $file['size'];

    //Filename
    $fileName = '';
    if (isset($file['name'])) {
        $fileName = $file['name'];
    } else {
        $messages[] = 'The name of the CSV file could not be determined. Please try uploading the file again';
    }

    //Find actual file
    $actualFile = NULL;
    if (isset($file['tmp_name']))
        $actualFile = $file['tmp_name'];

    //Check if URL or messages has been set
    if (!$URL || !$fileName) {
        return $messages;
    }

    $fileURL = $URL . $fileName; //set file URL

    $extention = strtolower(substr($fileName, strpos($fileName, '.') + 1)); //find extension

    //Set initial variables
    $country_id = NULL;
    $countryID = array();

    if ($extention == 'csv') {
        //  uplodat the file to the server
        if (move_uploaded_file($actualFile, $fileURL)) {
            //File successfully moved
        } else {
            $messages[] = 'The CSV file could not be processed during its file transfer. Please try uploading the file again';
        }

        // read the csv data from local disk
        $handle = fopen($fileURL, "r");

        if ($handle) {
            $counter1 = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $addresses = array();
                $users = array();
                $participants = array();

                if (!$counter1) {
                    //First pass
                } else {
                    if (isset($data[0]) && $data[0]) {
                        $users['name_first'] = "'" . mysql_real_escape_string($data[0]) . "'";
                    } else {
                        $messages[] = 'The first name data is missing on line ' . $counter1;
                        $passes = false;
                    }

                    if (isset($data[1]) && $data[1]) {
                        $users['name_last'] = "'" . mysql_real_escape_string($data[1]) . "'";
                    } else {
                        $messages[] = 'The last name data is missing on line ' . $counter1;
                        $passes = false;
                    }

                    $gender = NULL;
                    if (isset($data[2]) && $data[2])
                        $gender = strtolower($data[2]);

                    if ($gender) {
                        /* Check gender and set the appropriate variable */
                        switch ($gender) {
                            case 'male':
                                $users['avatar_info'] = "'" . '0:4:0:0:0:0' . "'";
                                break;
                            case 'female':
                                $users['avatar_info'] = "'" . '0:4:6:6:5:0' . "'";
                                break;
                        }

                        $users['Gender'] = "'" . mysql_real_escape_string(ucwords($gender)) . "'";
                    }

                    if (isset($data[3]) && $data[3]) {
                        $email = mysql_real_escape_string($data[3]);
                        $email_validate = filter_var($email, FILTER_VALIDATE_EMAIL);

                        if ($email_validate) {
                            $users['email'] = "'" . $email . "'";
                        } else {
                            $messages[] = 'The email data is not valid on line ' . $counter1;
                            $passes = false;
                        }
                    } else {
                        $messages[] = 'The email data is missing on line ' . $counter1;
                        $passes = false;
                    }

                    $created = "'" . date('Y-m-d H:i:s') . "'";

                    if (isset($data[4]) && $data[4]) {
                        $users['mobile'] = "'" . mysql_real_escape_string($data[4]) . "'";

                        $mobile_count = strlen($data[4]);
                        if ($mobile_count < 9 || $mobile_count > 12) {
                            $messages[] = 'The mobile data does not have the appropriate number of characters (9-12) on line ' . $counter1 . '. Please format it correctly in your spreadsheet application.';
                            $passes = false;
                        }
                    }

                    if (isset($data[5]) && $data[5]) {
                        $users['phone'] = "'" . mysql_real_escape_string($data[5]) . "'";

                        $phone_count = strlen($data[5]);
                        if ($phone_count < 9 || $phone_count > 12) {
                            $messages[] = 'The phone data does not have the appropriate number of characters (9-12) on line ' . $counter1 . '. Please format it correctly in your spreadsheet application.';
                            $passes = false;
                        }
                    }

                    if (isset($data[6]) && $data[6])
                        $addresses['street'] = "'" . mysql_real_escape_string($data[6]) . "'";

                    if (isset($data[7]) && $data[7])
                        $addresses['suburb'] = "'" . mysql_real_escape_string($data[7]) . "'";

                    if (isset($data[8]) && $data[8])
                        $addresses['postcode'] = "'" . mysql_real_escape_string($data[8]) . "'";

                    if (isset($data[9]) && $data[9])
                        $addresses['state'] = "'" . mysql_real_escape_string($data[9]) . "'";

                    if (isset($data[10]) && $data[10]) {
                        // country validation
                        mysql_select_db($database, $ifs);
                        $query_retCountry = "
							SELECT 
							country_lookup.country_name,
							country_lookup.id
							FROM
							country_lookup
							WHERE 
							country_lookup.country_name='$data[10]'
							ORDER BY
							country_lookup.country_name
							";
                        $retCountry = mysql_query($query_retCountry, $ifs);

                        $totalRows_retCountry = 0;
                        $row_retCountry = array();

                        if ($retCountry) {
                            $totalRows_retCountry = mysql_num_rows($retCountry);

                            if ($totalRows_retCountry)
                                $row_retCountry = mysql_fetch_assoc($retCountry);
                        }

                        if ($totalRows_retCountry > 0) {
                            // get the country id
                            $addresses['country_id'] = $row_retCountry['id'];
                        } else {
                            $messages[] = 'An invalid country name was set on line ' . $counter1;
                            $passes = false;
                        }
                    }

                    //Only go through loop to deliver messages
                    if (!$passes) {
                        continue;
                    }

                    $invite_again = NULL;
                    if (isset($data[11]) && $data[11])
                        $invite_again = $data[11];

                    /* Make sure invite has the correct value */
                    if ($invite_again) {
                        $invite_again_value = strtolower($invite_again);
                        if ($invite_again_value != 'no' && $invite_again_value != 'yes') {
                            $invite_again_value = 'yes';
                        }

                        $participants['invite_again'] = ucwords($invite_again_value);
                    } else {
                        $participants['invite_again'] = 'Yes'; //set invite agin as default
                    }

                    if ($invite_again == 'No') {
                        $messages[] = 'The user on line ' . $counter1 . ' has asked not to be invited again';
                        continue;
                    }

                    if (isset($data[12]) && $data[12])
                        $participants['dob'] = "'" . mysql_real_escape_string($data[12]) . "'";

                    if (isset($data[13]) && $data[13])
                        $participants['ethnicity'] = "'" . mysql_real_escape_string($data[13]) . "'";

                    if (isset($data[14]) && $data[14])
                        $participants['occupation'] = "'" . mysql_real_escape_string($data[14]) . "'";

                    if (isset($data[15]) && $data[15])
                        $participants['brand_segment'] = "'" . mysql_real_escape_string($data[15]) . "'";

                    if (isset($data[16]) && $data[16])
                        $participants['optional1'] = "'" . mysql_real_escape_string($data[16]) . "'";

                    if (isset($data[17]) && $data[17])
                        $participants['optional2'] = "'" . mysql_real_escape_string($data[17]) . "'";

                    if (isset($data[18]) && $data[18])
                        $participants['optional3'] = "'" . mysql_real_escape_string($data[18]) . "'";

                    if (isset($data[19]) && $data[19])
                        $participants['optional4'] = "'" . mysql_real_escape_string($data[19]) . "'";

                    if (isset($data[20]) && $data[20])
                        $participants['optional5'] = "'" . mysql_real_escape_string($data[20]) . "'";

                    if ($passes) {
                        $address_result = false;
                        if (!empty($addresses)) {
                            $addresses_keys = array_keys($addresses);
                            $addresses_values = array_values($addresses);

                            $address_sql = sprintf("INSERT INTO addresses(%s, created) VALUES(%s, %s)", implode(', ', $addresses_keys), implode(', ', $addresses_values), $created);

                            mysql_select_db($database, $ifs);
                            $address_result = mysql_query($address_sql, $ifs);

                            if (!$address_result) {
                                $messages[] = 'Address could not be saved';
                            }
                        } else {
                            $messages[] = 'Please provide additonal address information (e.g. street, suburb, post code, state and/or country) for the user on line ' . $counter1;
                        }

                        $users_result = false;
                        if (!empty($users) && $address_result) {
                            $address_id = mysql_insert_id($ifs);

                            $users_keys = array_keys($users);
                            $users_values = array_values($users);

                            $users_sql = sprintf("INSERT INTO users(%s, created, address_id) VALUES(%s, %s, %d)", implode(', ', $users_keys), implode(', ', $users_values), $created, $address_id);
                            mysql_select_db($database, $ifs);
                            $users_result = mysql_query($users_sql, $ifs);

                            if (!$users_result) {
                                $messages[] = 'User could not be saved for the user on line ' . $counter1;
                            }
                        } else {
                            $messages[] = 'Please provide additonal user information (e.g. first name, last name and email) for the user on line ' . $counter1;
                        }

                        if (!empty($participants) && $users_result) {
                            $user_id = mysql_insert_id($ifs);

                            $participants['brand_project_id'] = $brand_project_id;

                            $participants_keys = array_keys($participants);
                            $participants_values = array_values($participants);

                            $participants_sql = sprintf("INSERT INTO participants(%s, created, user_id) VALUES(%s, %s, %d)", implode(', ', $participants_keys), implode(', ', $participants_values), $created, $user_id);

                            mysql_select_db($database, $ifs);
                            $participants_result = mysql_query($participants_sql, $ifs);

                            if (!$participants_result) {
                                $messages[] = 'Participant could not be saved for the user on line ' . $counter1;
                            }
                        }
                    }
                }

                $counter1++;
            }

            if (!feof($handle)) {
                $messages[] = "Error: unexpected fgets() fail";
            }

            fclose($handle);
        }
        // if statement
    } else { // if-- extention ==csv
        $messages[] = 'Please upload a CSV file';
    }

    return $messages;
}// function
