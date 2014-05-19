<?php
require_once('Connections/ifs.php');
require_once('core.php');
require_once('models/users_model.php');

$message_val = new stdClass;
$message_val->other = array();
$message_val->fields = array();
$fields = array();

$user_type = null;
if (isset($_SESSION['MM_UserTypeId'])) {
    $user_type = $_SESSION['MM_UserTypeId'];
}

if (admin($database_ifs, $ifs) && $user_type == -1 || $user_type == 1) {
    if (isset($_GET['brand_project_id'])) {
        $brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
    } else {
        $_SESSION['notification'] = 'The brand project was not set.';

        mysql_close($ifs);

        header("Location: clientCompany.php");
        die();
    }

    /* The brand project was successflly updated */
    if (isset($_GET['update'])) {
        $message_val->other[] = 'The Brand Project was successfully updated';

        unset($_GET['update']);
    }

    //Page properties
    $page = 'Edit Brand Project';
    $title = $page;
    $main_script = 'brand_project_edit';
    $other_content = 'brand_project_edit';
    $validate = true;
    $inline_scripting = 'brand_project_edit_inline';

    $import = true;
    $import_page = 'brand_project_upload_pic.php';

    //retrieve the brand project's details
    mysql_select_db($database_ifs, $ifs);
    $query_retBPs = "
		SELECT 
		  brand_projects.moderator_user_id,
		  brand_projects.max_sessions,
		  brand_projects.name,
		  brand_projects.id,
		  brand_projects.end_date,
		  brand_projects.start_date,
		  brand_projects.client_company_id,
		  brand_projects.logo_url,
		  brand_projects.chatroom_logo_url,
		  client_companies.start_date AS `min_date`,
		  client_companies.end_date AS `max_date`,
		  client_companies.enable_chatroom_logo
		FROM
		  brand_projects
		  INNER JOIN client_companies ON(brand_projects.client_company_id = client_companies.id)
		WHERE
			brand_projects.id=$brand_project_id	
		";
    $retBPs = mysql_query($query_retBPs, $ifs) or die(mysql_error());
    $row_retBPs = array();
    $totalRows_retBPs = 0;
    $client_company_id = null;

    $company_start_date = date('d-m-Y', time());
    $company_end_date = date('d-m-Y', time());

    $min_date = date('d-m-Y', time());
    $max_date = date('d-m-Y', time());

    $logo_url = '';
    $chatroom_logo_url = '';

    $enable_chatroom_logo = 0;

    if ($retBPs) {
        $row_retBPs = mysql_fetch_assoc($retBPs);
        $totalRows_retBPs = mysql_num_rows($retBPs);
    }

    if ($totalRows_retBPs) {
        $client_company_id = $row_retBPs['client_company_id'];

        $company_start_date = date('d-m-Y', strtotime($row_retBPs['start_date']));
        $company_end_date = date('d-m-Y', strtotime($row_retBPs['end_date']));

        $min_date = date('d-m-Y', strtotime($row_retBPs['min_date']));
        $max_date = date('d-m-Y', strtotime($row_retBPs['max_date']));

        $logo_url = $row_retBPs['logo_url'];
        $chatroom_logo_url = $row_retBPs['chatroom_logo_url'];
        $enable_chatroom_logo = $row_retBPs['enable_chatroom_logo'];
    } else {
        $_SESSION['notification'] = 'The client companies available to this brand project was not found.';

        mysql_close($ifs);

        header("Location: clientCompany.php");
        die();
    }

    //***************************Cheng Added
    //To retrieve Brand Project Preference details
    mysql_select_db($database_ifs, $ifs);
    $query_retBPPs = "
		SELECT 
		  *
		FROM
		  brand_project_preferences
		WHERE
		  brand_project_preferences.brand_project_id=$brand_project_id	
		";
    $retBPPs = mysql_query($query_retBPPs, $ifs) or die(mysql_error());
    $row_retBPPs = array();
    $totalRows_retBPPs = 0;

    //Set default colour settings
    $colour_browser_background = '#def1f8';
    $colour_background = '#ffffff';
    $colour_border = '#e51937';
    $colour_whiteboard_background = '#e1d8d8';
    $colour_whiteboard_border = '#a4918b';
    $colour_whiteboard_icon_background = '#408ad2';
    $colour_whiteboard_icon_border = '#a4918b';
    $colour_menu_background = '#679fd2';
    $colour_menu_border = '#043a6b';
    $colour_icon = '#e51937';
    $colour_text = '#e51937';
    $colour_label = '#679fd2';
    $colour_button_background = '#a66500';
    $colour_button_border = '#ffc973';

    $has_preference_record = false;

    if ($retBPPs) {
        $row_retBPPs = mysql_fetch_assoc($retBPPs);
        $totalRows_retBPPs = mysql_num_rows($retBPPs);
    }

    if ($totalRows_retBPPs) {
        $has_preference_record = true;

        $colour_browser_background = $row_retBPPs['colour_browser_background'];
        $colour_background = $row_retBPPs['colour_background'];
        $colour_border = $row_retBPPs['colour_border'];

        $colour_whiteboard_background = $row_retBPPs['colour_whiteboard_background'];
        $colour_whiteboard_border = $row_retBPPs['colour_whiteboard_border'];
        $colour_whiteboard_icon_background = $row_retBPPs['colour_whiteboard_icon_background'];
        $colour_whiteboard_icon_border = $row_retBPPs['colour_whiteboard_icon_border'];

        $colour_menu_background = $row_retBPPs['colour_menu_background'];
        $colour_menu_border = $row_retBPPs['colour_menu_border'];
        $colour_icon = $row_retBPPs['colour_icon'];
        $colour_text = $row_retBPPs['colour_text'];
        $colour_label = $row_retBPPs['colour_label'];
        $colour_button_background = $row_retBPPs['colour_button_background'];
        $colour_button_border = $row_retBPPs['colour_button_border'];
    }
    //End

    //do the insert
    if (isset($_POST['btnSubmit'])) {
        $update_success = TRUE;

        /* Set session name */
        if (isset($_POST['name']) && $_POST['name']) {
            $brand_name = strip_tags(mysql_real_escape_string($_POST['name']));
        } else {
            $update_success = FALSE;
            $message_val->fields['name'] = 'Session Name';
        }

        /* Set max sessions */
        $max_sessions = NULL;
        if (isset($_POST['max_sessions']) && $_POST['max_sessions']) {
            $max_sessions = strip_tags(mysql_real_escape_string($_POST['max_sessions']));
        }

        /* Set start date */
        if (isset($_POST['start_date']) && $_POST['start_date']) {
            $start_date = date('Y-m-d', strtotime(strip_tags(mysql_real_escape_string($_POST['start_date']))));

            if (strtotime($start_date) < strtotime($min_date)) {
                $update_success = FALSE;
                $message_val->fields['start_date'] = 'Start Date';
                $message_val->error_types['start_date'] = 'less';
            } elseif (strtotime($start_date) > strtotime($max_date)) {
                $update_success = FALSE;
                $message_val->fields['start_date'] = 'Start Date';
                $message_val->error_types['start_date'] = 'exceed';
            }
        } else {
            $update_success = FALSE;
            $message_val->fields['start_date'] = 'Start Date';
        }

        /* Set End Date */
        if (isset($_POST['end_date']) && $_POST['end_date']) {
            $end_date = date('Y-m-d', strtotime(strip_tags(mysql_real_escape_string($_POST['end_date']))));
            $session_replay_date = date('Y-m-d', strtotime($end_date . " +90 days"));

            if (strtotime($end_date) < strtotime($min_date)) {
                $update_success = FALSE;
                $message_val->fields['end_date'] = 'End Date';
                $message_val->error_types['end_date'] = 'less';
            } elseif (strtotime($end_date) > strtotime($max_date)) {
                $update_success = FALSE;
                $message_val->fields['end_date'] = 'End Date';
                $message_val->error_types['end_date'] = 'exceed';
            }
        } else {
            $update_success = FALSE;
            $message_val->fields['end_date'] = 'End Date';
        }

        //Put in color setting
        if (isset($_POST['browser_background']) && $_POST['browser_background']) {
            $colour_browser_background = $_POST['browser_background'];
        }
        if (isset($_POST['background']) && $_POST['background']) {
            $colour_background = $_POST['background'];
        }
        if (isset($_POST['border']) && $_POST['border']) {
            $colour_border = $_POST['border'];
        }

        if (isset($_POST['whiteboard_background']) && $_POST['whiteboard_background']) {
            $colour_whiteboard_background = $_POST['whiteboard_background'];
        }
        if (isset($_POST['whiteboard_border']) && $_POST['whiteboard_border']) {
            $colour_whiteboard_border = $_POST['whiteboard_border'];
        }
        if (isset($_POST['whiteboard_icon_background']) && $_POST['whiteboard_icon_background']) {
            $colour_whiteboard_icon_background = $_POST['whiteboard_icon_background'];
        }
        if (isset($_POST['whiteboard_icon_border']) && $_POST['whiteboard_icon_border']) {
            $colour_whiteboard_icon_border = $_POST['whiteboard_icon_border'];
        }

        if (isset($_POST['menu_background']) && $_POST['menu_background']) {
            $colour_menu_background = $_POST['menu_background'];
        }
        if (isset($_POST['menu_border']) && $_POST['menu_border']) {
            $colour_menu_border = $_POST['menu_border'];
        }
        if (isset($_POST['icon']) && $_POST['icon']) {
            $colour_icon = $_POST['icon'];
        }
        if (isset($_POST['text']) && $_POST['text']) {
            $colour_text = $_POST['text'];
        }
        if (isset($_POST['label']) && $_POST['label']) {
            $colour_label = $_POST['label'];
        }
        if (isset($_POST['button_background']) && $_POST['button_background']) {
            $colour_button_background = $_POST['button_background'];
        }
        if (isset($_POST['button_border']) && $_POST['button_border']) {
            $colour_button_border = $_POST['button_border'];
        }
        //End Colour setting pickup

        //Make sure that the filesize is not larger than 2MB
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $filesize = $_FILES['image']['size'];
            $max_filesize = 2;

            //Check for filesize
            if ($filesize > ($max_filesize * 1048576)) {
                $message_val->other[] = 'Images must be under ' . $max_filesize . 'MB in size';
                $update_success = FALSE;
            }
        }
        if (isset($_FILES['chatroom_image']) && !empty($_FILES['chatroom_image']['name'])) {
            $filesize = $_FILES['chatroom_image']['size'];
            $max_filesize = 2;

            //Check for filesize
            if ($filesize > ($max_filesize * 1048576)) {
                $message_val->other[] = 'Images must be under ' . $max_filesize . 'MB in size';
                $update_success = FALSE;
            }
        }

        $created = date('Y-m-d H:i:s');

        /* Check if the Logo should be cleared */
        if (isset($_POST['clear_image'])) {
            if (file_exists($logo_url)) {
                unlink($logo_url); // Delete the original image
            }

            // Delete the currently stored images for the user
            $clear_logo_sql = sprintf("UPDATE brand_projects SET logo_thumbnail_url = NULL, logo_url = NULL WHERE brand_projects.id = %d", $brand_project_id);

            $clear_logo_success = mysql_query($clear_logo_sql, $ifs);

            /* Give message that logo could not be cleared */
            if (!$clear_logo_success) {
                $message_val->other[] = 'The Brand Project logo could not be cleared';
            }
        }
        if (isset($_POST['clear_chatroom_image'])) {
            if (file_exists($chatroom_logo_url)) {
                unlink($chatroom_logo_url); // Delete the original image
            }

            // Delete the currently stored images for the user
            $clear_logo_sql = sprintf("UPDATE brand_projects SET  chatroom_logo_url = NULL WHERE brand_projects.id = %d", $brand_project_id);

            $clear_logo_success = mysql_query($clear_logo_sql, $ifs);

            /* Give message that logo could not be cleared */
            if (!$clear_logo_success) {
                $message_val->other[] = 'The Chatroom & Greenroom logo could not be cleared';
            }
        }

        if ($update_success) {
            //Update colour settings into database
            if ($has_preference_record) { //update record
                $insert3SQL = sprintf("UPDATE brand_project_preferences SET colour_browser_background='$colour_browser_background', colour_background='$colour_background', colour_border='$colour_border', colour_whiteboard_background='$colour_whiteboard_background', colour_whiteboard_border='$colour_whiteboard_border', colour_whiteboard_icon_background='$colour_whiteboard_icon_background', colour_whiteboard_icon_border='$colour_whiteboard_icon_border', colour_menu_background='$colour_menu_background', colour_menu_border='$colour_menu_border', colour_icon='$colour_icon', colour_text='$colour_text', colour_label='$colour_label', colour_button_background='$colour_button_background', colour_button_border='$colour_button_border' WHERE brand_project_id=$brand_project_id");
                mysql_select_db($database_ifs, $ifs);
                $Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
            } else { //create new record
                $insert3SQL = sprintf("INSERT INTO `brand_project_preferences` (`brand_project_id`, `colour_browser_background`, `colour_background`, `colour_border`, `colour_whiteboard_background`, `colour_whiteboard_border`, `colour_whiteboard_icon_background`, `colour_whiteboard_icon_border`, `colour_menu_background`, `colour_menu_border`, `colour_icon`, `colour_text`, `colour_label`, `colour_button_background`, `colour_button_border`) VALUES ('$brand_project_id','$colour_browser_background','$colour_background','$colour_border','$colour_whiteboard_background','$colour_whiteboard_border','$colour_whiteboard_icon_background','$colour_whiteboard_icon_border','$colour_menu_background','$colour_menu_border','$colour_icon','$colour_text','$colour_label','$colour_button_background','$colour_button_border')");
                mysql_select_db($database_ifs, $ifs);
                $Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
                $has_preference_record = true;
            }

            //edit the  brand projects
            $insert3SQL = sprintf("UPDATE brand_projects SET name='$brand_name', client_company_id='$client_company_id', max_sessions=$max_sessions, start_date='$start_date', end_date='$end_date',session_replay_date='$session_replay_date', updated='$created' WHERE brand_projects.id=$brand_project_id");
            mysql_select_db($database_ifs, $ifs);
            $Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
            $user_login_id = mysql_insert_id($ifs);

            /* If another image should be uploaded */
            if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                /* Upload Image */
                $_SESSION['enable_chatroom_logo'] = $enable_chatroom_logo;
                $_GET['brand_project_id'] = $brand_project_id;
                $_GET['client_company_id'] = $client_company_id;

                $default_content = $other_content; //save other content value
                include($import_page);

                //If message set
                if ($message) {
                    $message_val->other[] = $message;

                    unset($message);
                    $update_success = FALSE;
                    $other_content = $default_content; //use the value again
                }
            }
            /* If another image should be uploaded */
            if (isset($_FILES['chatroom_image']) && !empty($_FILES['chatroom_image']['name'])) {
                //only when enabled to show
                $_SESSION['enable_chatroom_logo'] = $enable_chatroom_logo;
                /* Upload Image */
                $_GET['brand_project_id'] = $brand_project_id;
                $_GET['client_company_id'] = $client_company_id;

                $default_content = $other_content; //save other content value
                include($import_page);

                //If message set
                if ($message) {
                    $message_val->other[] = $message;

                    unset($message);
                    $update_success = FALSE;
                    $other_content = $default_content; //use the value again
                }
            }

            if ($update_success) {
                mysql_close($ifs);
                $updateGoTo = $form_action . '&update=1';
                header(sprintf("Location: %s", $updateGoTo));
                die();
            } else {
                $message_val->other[] = 'The Brand Project was updated, except the image field. Please read the error message.';
            }

        } elseif (!empty($message_val->fields)) {
            $fields = array_keys($message_val->fields);
        }
    }

    if (!empty($message_val->other) || !empty($fields)) {
        $message = process_messages($message_val);
    }

    require_once('views/popup.php');
} else {
    if (!$user_type) {
        $_SESSION['notification'] = 'You are logged out, please login again.';
    } else {
        $_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
    }

    mysql_close($ifs);

    header('Location: index.php');
    die();
}

mysql_close($ifs);