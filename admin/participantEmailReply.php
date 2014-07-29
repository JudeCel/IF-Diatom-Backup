<?php
require_once('Connections/ifs.php');
require_once('core.php');
require_once('models/participant-email-model.php');
require_once('models/users_model.php');
require_once('models/brand_model.php');

$filename = basename($_SERVER['PHP_SELF']);
$total_participants = 0;

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

// get participant id from URL
if (isset($_GET['participant_lists_id'])) {
    $participant_lists_id = strip_tags(mysql_real_escape_string($_GET['participant_lists_id']));
} else {
    $participant_lists_id = -1;
}

// get the participant_reply_id from URL
if (isset($_GET['participant_reply_id'])) {
    $participant_reply_id = strip_tags(mysql_real_escape_string($_GET['participant_reply_id']));
} else {
    $participant_reply_id = -1;
}

$replacements = array();

/* Get Participant Lists */
$retEmailReply = retrieve_participant_list($database_ifs, $ifs, $participant_lists_id);

/* Initialise values for testing */
$list_built = FALSE;
$valid = TRUE;

if (!$retEmailReply || is_string($retEmailReply)) {
    $valid = FALSE;
}

/* Set general fail message */
$error_message = "You're request could not be fulfilled. Please try again later.";
$email_not_sent = "The e-mail specifying your login details could not be sent. Please contact the Administrator";

$from = "yourvoice@insiderfocus.com"; //specify the FROM email

/* Check if can continue */
if ($valid) {
    $row_retEmailReply = mysql_fetch_assoc($retEmailReply);

    $totalRows_retEmailReply = mysql_num_rows($retEmailReply);

    $username = $row_retEmailReply['email']; //email
    $replacements['Username'] = $username;

    $first_name = $row_retEmailReply['name_first']; //first name
    $replacements['First Name'] = $first_name;

    $last_name = $row_retEmailReply['name_last']; //last name
    $replacements['Last Name'] = $last_name;

    $name = $first_name . ' ' . $last_name; //create whole name

    /* define the variables */
    $password = create_unique_password(); //password
    $default_password = md5($password);
    $replacements['Password'] = $password;

    /* Find participant information */
    $created = strip_tags(mysql_real_escape_string(date('Y-m-d H:i:s')));
    $user_id = $row_retEmailReply['user_id'];

    /* Set invite information */
    $invites = array();
    $invites['total'] = $row_retEmailReply['invites'];
    $invites['accepted'] = $row_retEmailReply['invites_accepted'];
    $invites['not_now'] = $row_retEmailReply['invites_not_now'];
    $invites['not_interested'] = $row_retEmailReply['invites_not_interested'];
    $invites['no_reply'] = $row_retEmailReply['invites_no_reply'];

    $participant_id = $row_retEmailReply['participant_id'];
    $reply_id = $row_retEmailReply['participant_reply_id'];

    $session_id = $row_retEmailReply['session_id'];
    $user_login_id = -1;

    //total participants
    $total_participants = get_total_participants($database_ifs, $ifs, $session_id);

    $retEmail = retrieve_users($database_ifs, $ifs, false, null, $user_id); //find existing user login id

    /* Make sure that user is retriveable */
    $existing_user_login_id = NULL;
    if (!is_string($retEmail) && $retEmail) {
        $totalRows_retEmail = mysql_num_rows($retEmail);

        if ($totalRows_retEmail > 0) {
            $row_retEmail = mysql_fetch_assoc($retEmail);

            $existing_user_login_id = $row_retEmail['id'];
        }
    }

    /* Initialise values for brand details */
    $brand_name = NULL;
    $start_date = NULL;
    $end_date = NULL;
    $incentive_details = NULL;

    $brand_project = retrieve_brand_project($database_ifs, $ifs, $session_id); //get brand details

    /* Get Brand Information */
    $row_retBrandProject = mysql_fetch_assoc($brand_project);
    $totalRows_retBrandProject = mysql_num_rows($brand_project);

    $brand_project_id = $row_retBrandProject['id'];
    $session_name = $row_retBrandProject['Session_Name'];

    /* Set Brand Properties and Replacement Data */
    if (isset($row_retBrandProject['name'])) { //brand name
        $brand_name = $row_retBrandProject['name'];

        $replacements['Brand Name'] = $brand_name;
    }
    if (isset($row_retBrandProject['start_time'])) { //start time
        $start_date = strtotime($row_retBrandProject['start_time']);

        $replacements['Start Date'] = date('h:ia l j F Y', $start_date);
    }
    if (isset($row_retBrandProject['end_time'])) { //end time
        $end_date = strtotime($row_retBrandProject['end_time']);

        $replacements['End Date'] = date('h:ia l j F Y', $end_date);
    }
    if (isset($row_retBrandProject['incentive_details'])) { //incentive details
        $incentive_details = $row_retBrandProject['incentive_details'];
    }

    /* Get the colours that are already used */
    $colours_used = array();
    if (isset($row_retBrandProject['colours_used']) && $row_retBrandProject['colours_used']) {
        $colours_used = (array)json_decode($row_retBrandProject['colours_used']);
    }

    /* Set address details for brand */
    $retAddress = retrieve_brand_projects_addresses($database_ifs, $ifs, $brand_project_id);
    $row_retAddress = mysql_fetch_assoc($retAddress);
    $totalRows_retAddress = mysql_num_rows($retAddress);

    $sender_street = $row_retAddress['street'];
    $sender_suburb = $row_retAddress['suburb'];
    $sender_state = $row_retAddress['state'];
    $sender_post_code = $row_retAddress['post_code'];
    $sender_country_name = $row_retAddress['country_name'];

    //Sender Addres
    $sender_address = $sender_street . "\n" . $sender_suburb . "\n" . $sender_state . " - " . $sender_post_code . "\n " . $sender_country_name . "\n";

    if ($total_participants < 8) //if less than the maximum
    {
        if (isset($row_retEmailReply['participant_reply_id'])) {

            // the participant already replied, so will not to allowed to make choice again
            // do nothing if the participant have being invited.
        } else {
            /* Built particpant list */
            $list = build_participant_list($database_ifs, $ifs, $participant_lists_id, true, $participant_reply_id, $session_id);

            if ($list) {
                $list_built = TRUE; //the list was successfully built
                $id = $total_participants + 1;

                //Create user login for session
                if ($participant_reply_id == 1) {
                    $user_login_id = create_user_logins($database_ifs, $ifs, $username, $default_password, $user_id);









                    $participant_colour_lookup_id = get_participant_colour($database_ifs, $ifs, $colours_used);

                    if ($participant_colour_lookup_id) {
                        $colours_used[] = $participant_colour_lookup_id; //add used colour id to colour used array

                        //Make sure there are no empty values
                        if (!empty($colours_used)) {
                            foreach ($colours_used as $key => $colour) {
                                if (!$colour) {
                                    unset($colours_used[$key]);
                                }
                            }
                        }

                        $colours_string = json_encode($colours_used);

                        // set the color of the participant in the participants table
                        $updateSQL = sprintf(
                            "UPDATE
                                participant_lists
                            SET
                                participant_colour_lookup_id = '%s',
                                updated = '%s'%s
                            WHERE
                                id = %d",
                            $participant_colour_lookup_id,
                            $created,
                            ($participant_reply_id == 1 ? ', ul_id=' . $user_login_id : ''),
                            $participant_lists_id
                        );

                        mysql_select_db($database_ifs, $ifs);
                        $Result = mysql_query($updateSQL, $ifs) or die(mysql_error());

                        //Update the session to set what colours were used
                        $colours_update_sql = sprintf(
                            "UPDATE
                                sessions
                            SET
                                colours_used = '%s'
                            WHERE
                                id = %d",
                            $colours_string,
                            $session_id
                        );

                        //Perform the update
                        mysql_select_db($database_ifs, $ifs);
                        mysql_query($colours_update_sql, $ifs);
                    }


















                } elseif ($participant_reply_id == 2) { //update reply id

                    build_participant_list($database_ifs, $ifs, $participant_lists_id, true, $participant_reply_id, $session_id);
                    $user_login_id = $existing_user_login_id;
                }



                //this is where colors were assigned.

                /* Update number of invites */
                iterate_number_of_invites($database_ifs, $ifs, $user_id, $participant_reply_id, $invites);

            } else {
                $valid = FALSE;
            }
        }
    } else {
        $to = $from; //use the admin's email
        $user_type = 'Admin';

        //Get Facilitator email
        if (isset($row_retBrandProject['user_id'])) {
            /* Get Facilitator */
            $facilitator_result = retrieve_users($database_ifs, $ifs, true, true, $row_retBrandProject['user_id']);
            if (!empty($facilitator_result)) {
                $facilitator = mysql_fetch_assoc($facilitator_result);

                $to = $facilitator['email'];
                $user_type = 'Facilitator';
            }
        }

        $error_message = "Sorry all the spots have been filled. We will get back to you when there are more sessions.";

        //Send e-mail to admin to say that all spots have been filled
        $email_message = sprintf('Dear %s<br /><br />', $user_type);
        $email_message .= 'The number of participants have been exceeded for ' . $session_name;
        $email_message .= ' and ' . $name . ' (' . $username . ') could not be registered<br /><br />';
        $email_message .= 'From the Insiderfocus Admin System';

        $subject = 'Insiderfocus - Number of Participants Exceeded';

        sendMail($database_ifs, $ifs, $to, null, $subject, $email_message, $from, null);
    }
}

$content = ''; //initialise content

/* Sent e-mail and display content */
if ($total_participants < 8 && $valid) {
    if ($participant_reply_id == 1 && !$reply_id) {
        /* Find specific email template */
        $retSessionEmail = retrieve_email_template($session_id, 2, $database_ifs, $ifs);

        $row_retSessionEmail = array();
        $totalRows_retSessionEmail = 0;

        if (!$retSessionEmail || ($retSessionEmail && is_string($retSessionEmail))) {

        } else {
            $row_retSessionEmail = mysql_fetch_assoc($retSessionEmail); //get email template
            $totalRows_retSessionEmail = mysql_num_rows($retSessionEmail); //number of rows available
        }

        $root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $login_link = $root . "/IFS/index.php?session_id=" . $session_id; //login link
        $change_password_link = $root . "change_password.php?user_login_id=" . $user_login_id;
        $terms = $root . "Terms_and_Conditions.pdf";

        $replacements['Login Link'] = $login_link;

        $to = $username;

        $subject = 'Subject';
        if (isset($row_retSessionEmail['subject'])) {
            $subject = $row_retSessionEmail['subject'];
        }

        /* Get content for message */
        $email_message = store_view_in_var(
            'view-email-template.php',
            array(
                'session_id' => $session_id,
                'email_type_id' => 2
            )
        );

        /* Parse Template for tags */
        $email_message = parse_tags_for_template($email_message, $replacements);

        if (!sendMail($database_ifs, $ifs, $to, $name, $subject, $email_message, $from, $user_id)) {
            $content .= '<p>' . $email_not_sent . '</p>';

            if ($list_built) {
                build_participant_list($database_ifs, $ifs, $participant_lists_id, true, 4, $session_id); //Inform system taht e-mail is not sent
            }
        } else {
            $content .= '<p>Thanks for accepting. A confirmation email has been sent with your Username And Password.</p>';
        }
    } elseif ($participant_reply_id == 2 && !$reply_id) {
        // Not at all intrested

        /* Find specific email template */
        $retSessionEmail = retrieve_email_template($session_id, 4, $database_ifs, $ifs);
        $row_retSessionEmail = mysql_fetch_assoc($retSessionEmail); //get email template
        $totalRows_retSessionEmail = mysql_num_rows($retSessionEmail); //number of rows available

        $to = $username;

        $subject = 'Subject';
        if (isset($row_retSessionEmail['subject'])) {
            $subject = $row_retSessionEmail['subject'];
        }

        /* Get content for message */
        $email_message = store_view_in_var(
            'view-email-template.php',
            array(
                'session_id' => $session_id,
                'email_type_id' => 4
            )
        );

        /* Parse Template for tags */
        $email_message = parse_tags_for_template($email_message, $replacements);

        $from = "yourvoice@insiderfocus.com";


        if (!sendMail($database_ifs, $ifs, $to, $name, $subject, $email_message, $from, $user_id)) {
            $content .= '<p>' . $email_not_sent . '</p>';

            if ($list_built) {
                build_participant_list($database_ifs, $ifs, $participant_lists_id, true, 4, $session_id); //Inform system taht e-mail is not sent
            }
        } else {
            $content .= "<p>Sorry you're not able to join us on the " . $brand_name . " Insider team discussion.</p>";
        }
    } elseif ($participant_reply_id == 3 && !$reply_id) {
                                                     /* Find specific email template */
                                                     $retSessionEmail = retrieve_email_template($session_id, 3, $database_ifs, $ifs);
                                                     $row_retSessionEmail = mysql_fetch_assoc($retSessionEmail); //get email template
                                                     $totalRows_retSessionEmail = mysql_num_rows($retSessionEmail); //number of rows available

                                                     $to = $username;
                                                     $subject = $row_retSessionEmail['subject'];

                                                     /* Get content for message */
                                                     $email_message = store_view_in_var(
                                                         'view-email-template.php',
                                                         array(
                                                             'session_id' => $session_id,
                                                             'email_type_id' => 3
                                                         )
                                                     );

                                                     /* Parse Template for tags */
                                                     $email_message = parse_tags_for_template($email_message, $replacements);

                                                     $from = "yourvoice@insiderfocus.com";

        if (!sendMail($database_ifs, $ifs, $to, $name, $subject, $email_message, $from, $user_id)) {
            $content .= "<p>You're request could not be fulfilled. Please try again later.</p>";

            if ($list_built) {
                build_participant_list($database_ifs, $ifs, $participant_lists_id, true, 4, $session_id); //Inform system taht e-mail is not sent
            }
        } else {
            $content .= "<p>Sorry you're not able to join us on the " . $brand_name . " Insider team discussion at this time.</p>";
        }
    } else {
        $content .= "<p>You have already been registered.</p>";
    }
} else {
    if ($valid) {
        $content .= "<p>Sorry, the Session is now full, and the Facilitator will be in touch. Thanks for your interest, we will let you know the next available Session.</p>";
    } else {
        if ($list_built) {
            build_participant_list($database_ifs, $ifs, $participant_lists_id, true, 4, $session_id); //Inform system taht e-mail is not sent
        }

        $content .= '<p>' . $error_message . '</p>';
    }
}

require_once('views/not_logged_in.php');

mysql_close($ifs);