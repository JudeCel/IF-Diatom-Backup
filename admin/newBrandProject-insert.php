<?php
require_once('Connections/ifs.php');
require_once('core.php');
require_once('getCounts.php');
require_once('models/users_model.php');

$message_val = new stdClass;
$message_val->other = array();
$message_val->fields = array();
$message_val->error_types = array();
$fields = array();
$updated = null;

$user_type = null;
if (isset($_SESSION['MM_UserTypeId'])) {
    $user_type = $_SESSION['MM_UserTypeId'];
}

if (admin($database_ifs, $ifs) && $user_type == -1 || $user_type == 1) {
    /* Get client company id */
    if (isset($_GET['client_company_id'])) {
        $client_company_id = strip_tags(mysql_real_escape_string($_GET['client_company_id']));
    } else {
        //Set message
        $message_val->other[] = 'The Client Company is not set';
        $_SESSION['notification'] = $message_val->other;

        mysql_close($ifs);

        header("Location: clientCompany.php");
        die();
    }

    /* Manual notification */
    if (isset($_GET['update'])) {
        $message_val->other[] = 'A New Brand Project was created.';

        unset($_GET['update']);
    }

    //Page properties
    $page = 'Add Brand Project';
    $title = $page;
    $main_script = 'brand_project_insert';
    $other_content = 'brand_project_insert';
    $validate = true;
    $inline_scripting = 'brand_project_insert_inline';

    $import = true;
    $import_page = 'brand_project_upload_pic.php';
    $image_new = TRUE;

    //retrieve the client company info
    mysql_select_db($database_ifs, $ifs);
    $query_retCompany = "
		SELECT 
		  client_companies.name,
		  client_companies.start_date,
		  client_companies.end_date,
		  client_companies.number_of_brands,
		  client_companies.max_sessions_brand,
		  client_companies.client_company_logo_thumbnail_url,
 		  client_companies.client_company_logo_url,
 	      client_companies.enable_chatroom_logo
		FROM
		  client_companies
		WHERE
		  client_companies.id=$client_company_id  
		";

    $retCompany = mysql_query($query_retCompany, $ifs) or die(mysql_error());
    $totalRows_retCompany = 0;
    $row_retCompany = array();
    $company_start_date = time();
    $company_end_date = time();
    $number_of_brands = 0;
    $client_company_name = '';
    $enable_chatroom_logo = 0;

    /* If client company was found */
    if ($retCompany) {
        $row_retCompany = mysql_fetch_assoc($retCompany);
        $totalRows_retCompany = mysql_num_rows($retCompany);

        $company_start_date = strtotime($row_retCompany['start_date']);
        $company_end_date = strtotime($row_retCompany['end_date']);
        $number_of_brands = $row_retCompany['number_of_brands'] - 1;

        $client_company_name = $row_retCompany['name'];
        $enable_chatroom_logo = $row_retCompany['enable_chatroom_logo'];
    } else {
        //Set message
        $message_val->other[] = 'The Client Company is not set';
        $_SESSION['notification'] = $message_val->other;

        mysql_close($ifs);

        header("Location: signup.php");
        die();
    }

    //If the client company name is set
    if (isset($retCompany['name'])) {
        $client_company_name = $retCompany['name'];

        //Change the page properties using the client company name
        $page = 'Brand Projects | ' . $client_company_name;
        $title = $client_company_name;
    }

    //retrieve the brand projects
    mysql_select_db($database_ifs, $ifs);
    $query_retBPs = "
		SELECT 
		  brand_projects.max_sessions,
		  brand_projects.name,
		  brand_projects.id,
		  brand_projects.end_date,
		  brand_projects.start_date,
		  brand_projects.session_replay_date
		FROM
		  brand_projects
		WHERE
		   brand_projects.client_company_id=$client_company_id  
		";

    $retBPs = mysql_query($query_retBPs, $ifs) or die(mysql_error());
    $totalRows_retBPs = 0;

    /* If Brand Project found */
    if ($retBPs) {
        $totalRows_retBPs = mysql_num_rows($retBPs);
    }

    //do the inserts
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

            if (strtotime($start_date) < $company_start_date) {
                $update_success = FALSE;
                $message_val->fields['start_date'] = 'Start Date';
                $message_val->error_types['start_date'] = 'less';
            } elseif (strtotime($start_date) > $company_end_date) {
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

            if (strtotime($end_date) < $company_start_date) {
                $update_success = FALSE;
                $message_val->fields['end_date'] = 'End Date';
                $message_val->error_types['end_date'] = 'less';
            } elseif (strtotime($end_date) > $company_end_date) {
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

        $created = date('Y-m-d H:i:s');

        if ($update_success) {
            //insert into brand projects
            $insert3SQL = sprintf("INSERT INTO brand_projects (name, client_company_id, max_sessions, start_date, end_date,session_replay_date, created) VALUES ('$brand_name','$client_company_id', $max_sessions,'$start_date','$end_date','$session_replay_date','$created')");
            mysql_select_db($database_ifs, $ifs);
            $updated = mysql_query($insert3SQL, $ifs) or die(mysql_error());

            //To retrieve Brand Project id details
            mysql_select_db($database_ifs, $ifs);
            $query_retBPPs = "
 				SELECT
 				  brand_projects.id
 				FROM
 				  brand_projects
 				WHERE
 				  brand_projects.client_company_id=$client_company_id
 				  AND brand_projects.name = '$brand_name'
 				";
            $retBPPs = mysql_query($query_retBPPs, $ifs) or die(mysql_error());
            $row_retBPPs = array();
            $totalRows_retBPPs = 0;

            if ($retBPPs) {
                $row_retBPPs = mysql_fetch_assoc($retBPPs);
                $totalRows_retBPPs = mysql_num_rows($retBPPs);
            }

            if ($totalRows_retBPPs) {
                $brand_project_id = $row_retBPPs['id'];
                //insert colour setting
                $insert3SQL = sprintf("INSERT INTO `brand_project_preferences` (`brand_project_id`, `colour_browser_background`, `colour_background`, `colour_border`, `colour_whiteboard_background`, `colour_whiteboard_border`, `colour_whiteboard_icon_background`, `colour_whiteboard_icon_border`, `colour_menu_background`, `colour_menu_border`, `colour_icon`, `colour_text`, `colour_label`, `colour_button_background`, `colour_button_border`) VALUES ('$brand_project_id','$colour_browser_background','$colour_background','$colour_border','$colour_whiteboard_background','$colour_whiteboard_border','$colour_whiteboard_icon_background','$colour_whiteboard_icon_border','$colour_menu_background','$colour_menu_border','$colour_icon','$colour_text','$colour_label','$colour_button_background','$colour_button_border')");
                mysql_select_db($database_ifs, $ifs);
                $Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
            }

            //If image uploaded
            if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                /* Upload Image */
                $_SESSION['enable_chatroom_logo'] = $enable_chatroom_logo;
                $_GET['brand_project_id'] = $row_retBPPs['id'];
                $_GET['client_company_id'] = $client_company_id;
                $_GET['brand_project_name'] = $brand_name;

                $default_content = $other_content; //save other content value
                include($import_page);

                //message was set
                if ($message) {
                    $message_val->other[] = $message;
                    unset($message);
                    $update_success = FALSE;
                    $other_content = $default_content; //use the value again
                }
            }
            /* If another image should be uploaded */
            if (isset($_FILES['chatroom_image']) && !empty($_FILES['chatroom_image']['name'])) {
                /* Upload Image */
                $_SESSION['enable_chatroom_logo'] = $enable_chatroom_logo;
                $_GET['brand_project_id'] = $row_retBPPs['id'];
                $_GET['client_company_id'] = $client_company_id;
                $_GET['brand_project_name'] = $brand_name;

                $default_content = $other_content; //save other content value
                include($import_page);

                //message was set
                if ($message) {
                    $message_val->other[] = $message;
                    unset($message);
                    $update_success = FALSE;
                    $other_content = $default_content; //use the value again
                }
            }

            if ($updated && $update_success) {
                $message_val->other[] = 'A New Brand Project was created.';
            } elseif (!$update_success && $updated) {
                $message_val->other[] = 'The Brand Project was created, except for the image was not used. Please read the error message.';
            }
        } else {
            $fields = array_keys($message_val->fields);
        }
    }

    /* Transform message into a string to be used by the view */
    if (!empty($message_val->other) || !empty($fields)) {
        if (!$updated) {
            $message = process_messages($message_val);
        } else {
            $_SESSION['notification'] = array();
            $_SESSION['notification'][] = process_messages($message_val);

            //close fancybox
            echo '<script type="text/javascript" src="js/fancybox_close.js" />';
        }
    }

    require_once('views/popup.php');
    mysql_close($ifs);
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