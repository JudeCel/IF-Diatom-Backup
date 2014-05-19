<?php
require_once("../Connections/ifs.php");
require('../core.php');
require_once('../models/users_model.php');
require_once('../models/participant-email-model.php');
require_once('../models/brand_model.php');
require_once('../models/green_room_model.php');

if (!admin($database_ifs, $ifs) || !$session_id) {
    $_SESSION['notification'] = array();

    /* Make sure taht the user is not sent through a loop */
    if ($session_id) {
        $_SESSION['current_location'] = $current_location;
    } else {
        $_SESSION['notification'][] = 'The session is not available at the moment. Please try again later.';
    }

    $_SESSION['notification'][] = 'Please enter your Username and Password.';

    header("Location: " . $ADMIN_URL . "login.php");
    die();
}

//Specify if you want to go straight to the chatroom
$bypass = FALSE;
if (isset($_GET['bypass'])) {
    $bypass = TRUE;
}

$preview = FALSE;
if (isset($_GET['preview'])) {
    $preview = TRUE;
}

$import = TRUE;

//available options: production, dev, ua
$environment = 'production';
if ($environment == 'dev') {
    $environment = FALSE;
}

$replacements = array();
$green_room_updated = FALSE;

$user_type = $_SESSION['MM_UserTypeId']; //user type

/* Get Brand Information */
$brand_project = retrieve_brand_project($database_ifs, $ifs);
$row_retBrandProject = NULL;
if ($brand_project) {
    $row_retBrandProject = mysql_fetch_assoc($brand_project);
    $totalRows_retBrandProject = mysql_num_rows($brand_project);
}

/* Get Brand Properties */
$session_name = '';
$brand_name = '';
if (isset($row_retBrandProject['Session_Name'])) {
    $session_name = $row_retBrandProject['Session_Name'];
    $brand_name = $row_retBrandProject['brand_project_name'];
}

//Check whether session has been closed or not
$closed = false;
if (isset($row_retBrandProject['status_id']) && $row_retBrandProject['status_id'] == 2) {
    $closed = true; //session is closed
}

$replacements['Session Name'] = $session_name;

/* Logo URL */
$brand_project_logo_url = NULL;
$chatroom_logo_url = NULL;
if ($row_retBrandProject['enable_chatroom_logo'] && $row_retBrandProject['chatroom_logo_url'] != null) {
    $chatroom_logo_url = $row_retBrandProject['chatroom_logo_url'];
}

//Set Start Date
$start_date = '';
if (isset($row_retBrandProject['start_time'])) {
    $start_date_unix = strtotime($row_retBrandProject['start_time']);
    $start_date = date('h:i a l d F Y', $start_date_unix);
}

//Set End Date
$end_date = '';
if (isset($row_retBrandProject['end_time'])) {
    $end_date_unix = strtotime($row_retBrandProject['end_time']);
    $end_date = date('h:i a l d F Y', $end_date_unix);
}

//Incentive Details
$incentive_details = '';
if (isset($row_retBrandProject['incentive_details'])) {
    $incentive_details = $row_retBrandProject['incentive_details'];
}

//Close Green Room
$x_close_url = $ADMIN_URL . "index.php";
if ($user_type > 3) {
    $x_close_url = $ADMIN_URL . "logout.php";
}

//User ID
$user_id = NULL;
if (isset($_SESSION['MM_UserId']['user_id'])) {
    $user_id = $_SESSION['MM_UserId'];
} elseif (isset($_SESSION['MM_UserId'])) {
    $user_id = $_SESSION['MM_UserId'];
}

/* Get Facilitator Properties */
$facilitator_name = '';
$facilitator_email = '';
$facilitator_phone = '';

//If Brand Project Details are available
if (isset($row_retBrandProject['user_id'])) {

    $facil_user_id = $row_retBrandProject['user_id'];

    /* Get Facilitator */
    $facilitator_result = retrieve_users($database_ifs, $ifs, true, true, $facil_user_id, false, null, true);
    if (!empty($facilitator_result)) {

        $facilitator = mysql_fetch_assoc($facilitator_result);

        $facilitator_name = $facilitator['name_first'];
        $facilitator_last_name = $facilitator['name_last'];

        //If the facilitator is the user, then set the names
        if ($row_retBrandProject['user_id'] == $user_id) {
            $participant_name = $facilitator_name;
            $participant_last_name = $facilitator_last_name;
        }

        $facilitator_email = $facilitator['email'];
        $facilitator_mobile = (isset($facilitator['mobile']) ? $facilitator['mobile'] : $facilitator['phone']);

        /* if the facilitator is the user */
        if ($facil_user_id == $user_id) {
            if (!$facilitator['green_room_visit']) { //Check if the facilitator has visited the green room before
                if (!check_if_avatar_default_state($facilitator, $user_id)) {
                    update_green_room_visit_status($database_ifs, $ifs, $user_id);
                    $green_room_updated = TRUE;
                }
            } elseif (!$green_room_updated && $bypass) {
                $chat_url = $BASE_URL . '?id=' . $user_id . '&sid=' . $session_id;

                header(sprintf("Location: %s", $chat_url));
                exit();
            }
        } elseif ($user_type == 2) {
            /* Send notification that they do not have permission to access this area */
            $_SESSION['notification'] = array(
                'You do not have permission to access this green room. Please contact the administrator.'
            );

            header("Location: " . $x_close_url);
            exit();
        }
    } else { //A facilitator has not been set yet.
        $_SESSION['notification'] = array(
            'A facilitator has not been allocated to this session. Please try again later'
        );

        header("Location: " . $x_close_url);
        exit();
    }
} else { //A facilitator has not been set yet.
    $_SESSION['notification'] = array(
        'A facilitator has not been allocated. Please try again later'
    );

    header("Location: " . $x_close_url);
    exit();
}

//If not admin and not facilitator, check if session has begun
if ($user_type > 1 && $facil_user_id != $user_id) {
    /* Check if start time is correct and if the session hasn't ended */
    if (!$start_date || !$end_date || $closed) {
        /* Send notification that neither a start or end date has been set */
        $_SESSION['notification'] = array(
            'Sorry, this Session has not yet started, or has now closed. Please check the dates &amp; times, or contact your Facilitator.'
        );

        header("Location: " . $x_close_url);
        exit();
    } else {
        $current_time = time();
        if ($current_time < $start_date_unix || $current_time > $end_date_unix) {
            $_SESSION['notification'] = array(
                'Sorry, this Session has not yet started, or has now closed. Please check the dates &amp; times, or contact your Facilitator.'
            );

            header("Location: " . $x_close_url);
            exit();
        }
    }
}

/* Get Participany Name */
$participant_name = '';
if (isset($_SESSION['MM_FirstName'])) {
    $participant_name = $_SESSION['MM_FirstName'];
}

$participant_last_name = '';

/**
 * Make sure the user have to the green room
 **/
if (!$user_id) {
    /* Send notification that no user id was found */
    $_SESSION['notification'] = array(
        'A valid user was not found.'
    );

    header("Location: " . $x_close_url);
} else {
    //Find out whether the user has visited the green room before
    $green_room_result = find_participant($database_ifs, $ifs, null, true, true, $user_id);

    if (!is_string($green_room_result)) {
        /* If query is valid */
        if ($green_room_result) {
            $green_room_row = mysql_fetch_assoc($green_room_result); //green room row

            //Set participant first name
            if (isset($green_room_row['name_first'])) {
                $participant_first_name = $green_room_row['name_first'];
            }

            //Set participant last name
            if (isset($green_room_row['name_last'])) {
                $participant_last_name = $green_room_row['name_last'];
            }

            /* Show to chat room */
            if ($green_room_row['green_room_visit'] && $_SESSION['MM_UserTypeId'] == 5 && $bypass) {
                $chat_url = $BASE_URL . '?id=' . $user_id . '&sid=' . $session_id;

                header(sprintf("Location: %s", $chat_url));
                exit();
            } elseif ($_SESSION['MM_UserTypeId'] == 5) { //if participant, but have not visited befor
                if (!check_if_avatar_default_state($green_room_row, $user_id)) { //check if avatar is at default state
                    update_green_room_visit_status($database_ifs, $ifs, $user_id);
                    $green_room_updated = TRUE;
                }
            }
        } elseif ($user_type == 5) {
            /* Send notification that they do not have permission to access this area */
            $_SESSION['notification'] = array(
                'You do not have permission to access this green room. Please contact the administrator.'
            );

            header("Location: " . $x_close_url);
            exit();
        }
    } elseif ($user_type == 5) {
        /* Send notification that they do not have permission to access this area */
        $_SESSION['notification'] = array(
            'You do not have permission to access this green room. Please contact the administrator.'
        );

        header("Location: " . $x_close_url);
        exit();
    }
}


//Check if the user is part of the staff and if they've visted the green room before
$staff_result = retrieve_users($database_ifs, $ifs, true, null, $user_id, false, null, true);

//Make sure that the staff user has visted the green room before
if ($staff_result && !is_string($staff_result)) {
    $staff_user = mysql_fetch_assoc($staff_result);

    $staff_first_name = $staff_user['name_first'];
    $staff_last_name = $staff_user['name_last'];

    //If the user is an observer, set as participant names
    if (!$participant_last_name && ($staff_first_name == $participant_name)) {
        $participant_name = $staff_first_name;
        $participant_last_name = $staff_last_name;
    }

    if (!$staff_user['green_room_visit']) { //Check if the facilitator has visited the green room before
        if (!check_if_avatar_default_state($staff_user, $user_id)) {
            update_green_room_visit_status($database_ifs, $ifs, $user_id);
            $green_room_updated = TRUE;
        }
    } elseif (!$green_room_updated && $bypass) {
        $chat_url = $BASE_URL . '?id=' . $user_id . '&sid=' . $session_id;

        header(sprintf("Location: %s", $chat_url));
        exit();
    }
}

/**
 * Green Room Details
 **/
$green_room_session = array();
$green_room_details = retrieve_green_room_information($database_ifs, $ifs, $session_id);

/* Check if the GR details are available */
if ($green_room_details && !is_string($green_room_details)) {
    $green_room_session = mysql_fetch_assoc($green_room_details);
}

//Specify the arguments that will be included in the URL
$replacements['Participant First Name'] = $participant_name;
$replacements['Participant Last Name'] = $participant_last_name;
$replacements['Facilitator Name'] = $facilitator_name;
$replacements['Email'] = $facilitator_email;
$replacements['Mobile'] = $facilitator_mobile;

//Display Content
ob_start();

$_GET['session_id'] = $session_id;

include($VIEWS_PATH . "green_room.php");

$content = ob_get_contents();
ob_end_clean(); //clean output

//Parse replacements of content
$content = parse_tags_for_template($content, $replacements, array(
    'Session Name',
    'Participant First Name',
    'Participant Last Name',
    'Facilitator Name',
    'Email',
    'Mobile'
));

echo $content;
?>