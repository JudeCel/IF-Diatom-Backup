<?php
require_once('Connections/ifs.php');

/* If this file is not being being imported */
if (!isset($import) || (isset($import) && !$import)) {
    $user_type = null;
    if (isset($_SESSION['MM_UserTypeId'])) {
        $user_type = $_SESSION['MM_UserTypeId'];
    }
}
//only show chatroom logo when it's enabled
$enable_chatroom_logo = 0;
if (isset($_SESSION['enable_chatroom_logo'])) {
    $enable_chatroom_logo = $_SESSION['enable_chatroom_logo'];
}

$brand_project_id = null;
if (isset($_GET['brand_project_id'])) {
    $brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));

    if (!is_numeric($brand_project_id)) {
        $brand_project_id = NULL;
    }
}

//Ensure that a valid bpid was selected
if (!$brand_project_id) {
    $_SESSION['notification'] = 'Invalid Options for uploading image';

    mysql_close($ifs);

    header("Location: index.php");
    die();
}


$_SESSION['brand_project_id'] = $brand_project_id;

$client_company_id = null;
if (isset($_GET['client_company_id'])) {
    $client_company_id = strip_tags(mysql_real_escape_string($_GET['client_company_id']));

    if (!is_numeric($client_company_id)) {
        $client_company_id = NULL;
    }
}

$brand_project_name = null;
if (isset($_GET['brand_project_name'])) {
    $brand_project_name = strip_tags(mysql_real_escape_string($_GET['brand_project_name']));
}

/* Set redirection after finalising image */
$edit = FALSE;
$updateGoTo = 'newBrandProject-insert.php?client_company_id=' . $client_company_id . '&update=1';
if (isset($_GET['edit']) && $_GET['edit']) {
    $edit = TRUE;

    $updateGoTo = 'newBrandProject-edit.php?brand_project_id=' . $brand_project_id . '&update=1';
}

$logo_exists = false;
$chatroom_logo_exists = false;

if ($user_type != -1 && $user_type != 1) {
    $_SESSION['notification'] = 'You do not have the permission to access this page';

    mysql_close($ifs);

    header("Location: index.php");
    die();
};

//Page properties
$page = 'Upload Brand Project Image';
$title = $page;
$main_script = null;
$other_content = 'brand_project_logo_upload';
$validate = false;
$inline_scripting = null;

if ($brand_project_id && (!isset($image_new) || (isset($image_new) && !$image_new))) {
    //Get the details of the image file for the selected user
    mysql_select_db($database_ifs, $ifs);
    $query_retImageInfo = sprintf(
        "SELECT
            brand_projects.name,
            brand_projects.logo_thumbnail_url,
            brand_projects.logo_url
        FROM
            brand_projects
        WHERE
            brand_projects.id = %d", $brand_project_id);

    $retImageInfo = mysql_query($query_retImageInfo, $ifs) or die(mysql_error());

    $totalRows_retImageInfo = 0;

    $brand_project_name = 'Brand Project';

    //The query was successful
    if ($retImageInfo) {
        $totalRows_retImageInfo = mysql_num_rows($retImageInfo);
    }

    //If there were results available
    if ($totalRows_retImageInfo) {
        $row_retImageInfo = mysql_fetch_assoc($retImageInfo);

        $brand_project_name = $row_retImageInfo['name'];
        $_SESSION['brand_project_name'] = $brand_project_name;

        //If the logo is set
        if ($row_retImageInfo['logo_url']) {
            $logo_exists = true;
        }

        //Set image path into the sessions
        if ($logo_exists) {
            $_SESSION['OriginalPath'] = $row_retImageInfo['logo_url'];
        }
    }
}
//chatroom
if ($brand_project_id && (!isset($chatroom_image_new) || (isset($chatroom_image_new) && !$chatroom_image_new))) {
    //Get the details of the image file for the selected user
    mysql_select_db($database_ifs, $ifs);
    $query_retChatroomImageInfo = sprintf(
        "SELECT
            brand_projects.name,
            brand_projects.chatroom_logo_url
        FROM
            brand_projects
        WHERE
            brand_projects.id = %d", $brand_project_id);

    $retChatroomImageInfo = mysql_query($query_retChatroomImageInfo, $ifs) or die(mysql_error());

    $totalRows_retChatroomImageInfo = 0;

    $brand_project_name = 'Brand Project';

    //The query was successful
    if ($retChatroomImageInfo) {
        $totalRows_retChatroomImageInfo = mysql_num_rows($retChatroomImageInfo);
    }

    //If there were results available
    if ($totalRows_retChatroomImageInfo) {
        $row_retChatroomImageInfo = mysql_fetch_assoc($retChatroomImageInfo);

        $brand_project_name = $row_retChatroomImageInfo['name'];
        $_SESSION['brand_project_name'] = $brand_project_name;

        //If the logo is set
        if ($row_retChatroomImageInfo['chatroom_logo_url']) {
            $chatroom_logo_exists = true;
        }

        //Set image path into the sessions
        if ($chatroom_logo_exists) {
            $_SESSION['OriginalChatroomPath'] = $row_retChatroomImageInfo['chatroom_logo_url'];
        }
    }
}


/*
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* "PHP & Jquery image upload & crop"
* Date: 2008-11-21
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*/

error_reporting(E_ALL ^ E_NOTICE);

//Do not remove this
//only assign a new timestamp if the session variable is empty
if (!$logo_exists) {
    if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key']) == 0) {
        $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
        $_SESSION['bp_file_ext'] = "";
    }

    /* Set client company name */
    if ($brand_project_name) {
        $_SESSION['brand_project_name'] = $brand_project_name;
    }
}

if (!$chatroom_logo_exists) {
    if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key']) == 0) {
        $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
        $_SESSION['bp_chatroom_file_ext'] = "";
    }

    /* Set client company name */
    if ($brand_project_name) {
        $_SESSION['brand_project_name'] = $brand_project_name;
    }
}

//Ensure that the brand project name is set
if (!isset($_SESSION['brand_project_name'])) {
    exit();
}

#########################################################################################################
# CONSTANTS																								#
# You can alter the options below																		#
#########################################################################################################
$upload_dir = $ADMIN_PATH . "upload/logo"; // The directory for the images to be saved in
$upload_path = $upload_dir . "/"; // The path to where the image will be saved
$large_image_prefix = "logo_"; // The prefix name to large image
$thumb_image_prefix = "thumbnail_"; // The prefix name to the thumb image
$large_image_name = $large_image_prefix . $_SESSION['brand_project_name'] . "-" . $brand_project_id . "-" . $_SESSION['random_key']; // New name of the large image (append the timestamp to the filename)
$thumb_image_name = $thumb_image_prefix . $_SESSION['brand_project_name'] . "-" . $brand_project_id . "-" . $_SESSION['random_key']; // New name of the thumbnail image (append the timestamp to the filename)
$max_file = "2"; // Maximum file size in MB
$max_width = "180"; // Max width allowed for the large image
$thumb_width = "300"; // Width of thumbnail image
$thumb_height = "100"; // Height of thumbnail image

$upload_chatroom_dir = $ADMIN_PATH . "upload/chatroom";
$upload_chatroom_path = $upload_chatroom_dir . "/";
$large_chatroom_image_prefix = "chatroom_logo_";
$large_chatroom_image_name = $large_chatroom_image_prefix . $_SESSION['brand_project_name'] . "-" . $brand_project_id . "-" . $_SESSION['random_key'];
$max_chatroom_width = "180";
$max_chatroom_height = "53";

// Only one of these image types should be allowed for upload
$allowed_image_types = array('image/pjpeg' => "jpg", 'image/jpeg' => "jpg", 'image/jpg' => "jpg", 'image/png' => "png", 'image/x-png' => "png", 'image/gif' => "gif");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this

$image_ext = ""; // initialise variable, do not change this.
foreach ($allowed_image_ext as $mime_type => $ext) {
    $image_ext .= strtoupper($ext) . " ";
}
$chatroom_image_ext = "";
foreach ($allowed_image_ext as $mime_type => $ext) {
    $chatroom_image_ext .= strtoupper($ext) . " ";
}

//Include all the functions necessary to process the image
require_once('models/image_upload_model.php');

//Image Locations
$large_image_location = $upload_path . $large_image_name . $_SESSION['bp_file_ext'];
$large_chatroom_image_location = $upload_chatroom_path . $large_chatroom_image_name . $_SESSION['bp_chatroom_file_ext'];

//Create the upload directory with the right permissions if it doesn't exist
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777);
    chmod($upload_dir, 0777);
}
if (!is_dir($upload_chatroom_dir)) {
    mkdir($upload_chatroom_dir, 0777);
    chmod($upload_chatroom_dir, 0777);
}

//Check to see if any images with the same name already exist
if (file_exists($large_image_location)) {
    $thumb_photo_exists = "";
    $large_photo_exists = "<img src=\"" . $upload_path . $large_image_name . $_SESSION['bp_file_ext'] . "\" alt=\"Large Image\"/>";
} else {
    $large_photo_exists = "";
    $thumb_photo_exists = "";
}

if (file_exists($large_chatroom_image_location)) {
    $large_chatroom_photo_exists = "<img src=\"" . $upload_chatroom_path . $large_chatroom_image_name . $_SESSION['bp_chatroom_file_ext'] . "\" alt=\"Large Chatroom Image\"/>";
} else {
    $large_chatroom_photo_exists = "";
}


// first upload
if (isset($_POST["upload"]) || isset($_POST["btnSubmit"])) {
    //Check if this is a new Brand Project
    $edit_value = 0;
    if (isset($_POST["btnSubmit"])) {
        $edit_value = 1;
    }

    //Get the file information
    if (!empty($_FILES['image'])) {
        $userfile_name = $_FILES['image']['name'];
        $userfile_tmp = $_FILES['image']['tmp_name'];
        $userfile_size = $_FILES['image']['size'];
        $userfile_type = $_FILES['image']['type'];
        $filename = basename($_FILES['image']['name']);
        $names = explode(".", $filename);
        $file_ext = $names[1];
        $image_size = getimagesize($userfile_tmp);
    }

    if (!empty($_FILES['chatroom_image'])) {
        $userfile_chatroom_name = $_FILES['chatroom_image']['name'];
        $userfile_chatroom_tmp = $_FILES['chatroom_image']['tmp_name'];
        $userfile_chatroom_size = $_FILES['chatroom_image']['size'];
        $userfile_chatroomtype = $_FILES['chatroom_image']['type'];
        $chatroom_filename = basename($_FILES['chatroom_image']['name']);
        $chatroom_names = explode(".", $chatroom_filename);
        $chatroom_file_ext = $chatroom_names[1];
        $chatroom_image_size = getimagesize($userfile_chatroom_tmp);
    }

    $mime = NULL;
    if (isset($image_size['mime'])) {
        $mime = $image_size['mime'];
    }
    $chatroom_mime = NULL;
    if (isset($chatroom_image_size['mime'])) {
        $chatroom_mime = $chatroom_image_size['mime'];
    }

    //$file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

    //Only process if the file is a JPG, PNG or GIF and below the allowed limit
    if ((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

        foreach ($allowed_image_types as $mime_type => $ext) {
            //loop through the specified image types and if they match the extension then break out
            //everything is ok so go and check file size
            if ($file_ext == $ext || ($mime && $mime_type == $mime)) { //&& $userfile_type==$mime_type
                $file_ext = $ext;

                $error = "";
                break;
            } else {
                $error = "Only <strong>" . $image_ext . "</strong> images accepted for upload<br />";
            }
        }
        //check if the file size is above the allowed limit
        if ($userfile_size > ($max_file * 1048576)) {
            $error .= "Images must be under " . $max_file . "MB in size";
        }
    } else {
        $error = "Select an image for upload";
    }

    //Everything is ok, so we can upload the image.
    if (strlen($error) == 0) {
        if (isset($_FILES['image']['name'])) {
            //put the file ext in the session so we know what file to look for once its uploaded
            $_SESSION['bp_file_ext'] = "." . $file_ext;

            //this file could now has an unknown file extension (we hope it's one of the ones set above!)

            $large_image_location = str_replace("." . $file_ext, "", $large_image_location);
            $large_image_location = $large_image_location . "." . $file_ext;

            move_uploaded_file($userfile_tmp, $large_image_location);

            chmod($large_image_location, 0777);

            $width = getWidth($large_image_location);
            $height = getHeight($large_image_location);
            //Scale the image if it is greater than the width set above
            if ($width > $max_width) {
                $scale = $max_width / $width;
                $uploaded = resizeImage($large_image_location, $width, $height, $scale);
            } else {
                $scale = 1;
                $uploaded = resizeImage($large_image_location, $width, $height, $scale);
            }

            $brand_project_id = $_SESSION['brand_project_id'];
            $temp_index = strpos($large_image_location, '/admin/upload');
            if ($temp_index === false) {
                $processed_str = $large_image_location;
            } else {
                $processed_str = substr($large_image_location, $temp_index + 7);
            }

            if (!empty($brand_project_id)) {
                mysql_select_db($database_ifs, $ifs);

                $addOriginal = sprintf(
                    "UPDATE brand_projects
                     SET
                         logo_url = '%s'
                     WHERE
                         brand_projects.id=%d", $processed_str, $brand_project_id);

                mysql_query($addOriginal, $ifs) or die(mysql_error());

                //Delete the thumbnail file so the user can create a new one
                if (file_exists($thumb_image_location)) {
                    unlink($thumb_image_location);
                }
            }
        }
        /*mysql_close($ifs);

        //Refresh the page to show the new uploaded image
        header("location: brand_project_upload_pic.php?brand_project_id=" . $brand_project_id . ($client_company_id ? '&client_company_id=' . $client_company_id . '&edit=' . $edit_value : ''));
        die();*/
    }

    if ((!empty($_FILES["chatroom_image"])) && ($_FILES['chatroom_image']['error'] == 0)) {
        foreach ($allowed_image_types as $mime_type => $ext) {
            //loop through the specified image types and if they match the extension then break out
            //everything is ok so go and check file size
            if ($chatroom_file_ext == $ext || ($chatroom_mime && $mime_type == $chatroom_mime)) { //&& $userfile_type==$mime_type
                $chatroom_file_ext = $ext;
                $chatroom_error = "";
                break;
            } else {
                $chatroom_error = "Only <strong>" . $chatroom_image_ext . "</strong> images accepted for upload<br />";
            }
        }
        //check if the file size is above the allowed limit
        if ($userfile_chatroom_size > ($max_file * 1048576)) {
            $chatroom_error .= "Images must be under " . $max_file . "MB in size";
        }
    } else {
        $chatroom_error = "Select an image for upload";
    }

    if (strlen($chatroom_error) == 0) {
        if (isset($_FILES['chatroom_image']['name'])) {
            //put the file ext in the session so we know what file to look for once its uploaded
            $_SESSION['bp_file_ext'] = "." . $file_ext;

            //this file could now has an unknown file extension (we hope it's one of the ones set above!)

            $large_chatroom_image_location = str_replace("." . $chatroom_file_ext, "", $large_chatroom_image_location);
            $large_chatroom_image_location = $large_chatroom_image_location . "." . $chatroom_file_ext;

            move_uploaded_file($userfile_chatroom_tmp, $large_chatroom_image_location);

            chmod($large_chatroom_image_location, 0777);

            $width = getWidth($large_chatroom_image_location);
            $height = getHeight($large_chatroom_image_location);
            //Scale the image if it is greater than the width set above
            if ($width > $max_chatroom_width || $height > $max_chatroom_height) {
                $scale = ($max_chatroom_height / $height < $max_chatroom_width / $width) ? ($max_chatroom_height / $height) : ($max_chatroom_width / $width);
                $uploaded = resizeImage($large_chatroom_image_location, $width, $height, $scale);
            } else {
                $scale = 1;
                $uploaded = resizeImage($large_chatroom_image_location, $width, $height, $scale);
            }

            $brand_project_id = $_SESSION['brand_project_id'];
            $temp_index = strpos($large_chatroom_image_location, '/admin/upload');
            if ($temp_index === false) {
                $processed_str = $large_chatroom_image_location;
            } else {
                $processed_str = substr($large_chatroom_image_location, $temp_index + 7);
            }

            if (!empty($brand_project_id)) {
                mysql_select_db($database_ifs, $ifs);

                $addOriginal = sprintf(
                    "UPDATE brand_projects
                     SET
                         chatroom_logo_url = '%s'
                     WHERE
                         brand_projects.id=%d", $processed_str, $brand_project_id);

                mysql_query($addOriginal, $ifs) or die(mysql_error());
            }
        }
    }

    if (strlen($error) == 0 || strlen($chatroom_error) == 0) {
        mysql_close($ifs);

        //Refresh the page to show the new uploaded image
        header("location: brand_project_upload_pic.php?brand_project_id=" . $brand_project_id . ($client_company_id ? '&client_company_id=' . $client_company_id . '&edit=' . $edit_value : ''));
        die();
    }
}

if (!isset($image_new) || (isset($image_new) && !$image_new)) {
    // delete
    if ($_GET['a'] == "delete" && strlen($_GET['t']) > 0) {
        //get the file locations
        $large_image_location = $upload_path . $large_image_prefix . $_GET['t'];

        $pos = strpos($large_image_location, $_SESSION['bp_file_ext']);

        if ($pos <= 0 && isset($_SESSION['bp_file_ext'])) {
            $large_image_location = $large_image_location . $_SESSION['bp_file_ext'];
        }

        if (file_exists($large_image_location)) {
            unlink($large_image_location);
        }

        if (isset($_SESSION['brand_project_id'])) {
            // Delete the currently stored images for the user
            $deleteImage = sprintf("UPDATE brand_projects SET logo_thumbnail_url = NULL , logo_url = NULL WHERE brand_projects.id = %d", $brand_project_id);
            mysql_query($deleteImage, $ifs) or die(mysql_error());
        }

        mysql_close($ifs);

        header("location: brand_project_upload_pic.php?brand_project_id=" . $brand_project_id);
        exit();
    }


    // delete image from database
    if (isset($_POST['deleteImage']) && $totalRows_retImageInfo > 0) {
        if ($logo_exists == true) {
            mysql_data_seek($retImageInfo, 0);
        }

        $row_retImageInfo = mysql_fetch_assoc($retImageInfo);

        if (file_exists($row_retImageInfo['logo_url'])) {
            unlink($row_retImageInfo['logo_url']); // Delete the original image
        }

        // Delete the currently stored images for the user
        $deleteImage = sprintf("UPDATE brand_projects SET logo_thumbnail_url = NULL, logo_url = NULL WHERE brand_projects.id = %d", $brand_project_id);
        mysql_query($deleteImage, $ifs) or die(mysql_error());

        mysql_close($ifs);

        header("location: brand_project_upload_pic.php?brand_project_id=" . $brand_project_id);
        exit();
    }
}

if (!isset($chatroom_image_new) || (isset($chatroom_image_new) && !$chatroom_image_new)) {
    // delete
    if ($_GET['a'] == "delete" && strlen($_GET['t']) > 0) {
        //get the file locations
        $large_chatroom_image_location = $upload_chatroom_path . $large_chatroom_image_prefix . $_GET['t'];

        $pos = strpos($large_chatroom_image_location, $_SESSION['bp_file_ext']);

        if ($pos <= 0 && isset($_SESSION['bp_file_ext'])) {
            $large_chatroom_image_location = $large_chatroom_image_location . $_SESSION['bp_file_ext'];
        }

        if (file_exists($large_chatroom_image_location)) {
            unlink($large_chatroom_image_location);
        }

        if (isset($_SESSION['brand_project_id'])) {
            // Delete the currently stored images for the user
            $deleteChatroomImage = sprintf("UPDATE brand_projects SET chatroom_logo_url = NULL WHERE brand_projects.id = %d", $brand_project_id);
            mysql_query($deleteChatroomImage, $ifs) or die(mysql_error());
        }

        mysql_close($ifs);

        header("location: brand_project_upload_pic.php?brand_project_id=" . $brand_project_id);
        exit();
    }


    // delete image from database
    if (isset($_POST['deleteChatroomImage']) && $totalRows_retChatroomImageInfo > 0) {
        if ($chatroom_logo_exists == true) {
            mysql_data_seek($retChatroomImageInfo, 0);
        }

        $row_retChatroomImageInfo = mysql_fetch_assoc($retChatroomImageInfo);

        if (file_exists($row_retChatroomImageInfo['chatroom_logo_url'])) {
            unlink($row_retChatroomImageInfo['chatroom_logo_url']); // Delete the original image
        }

        // Delete the currently stored images for the user
        $deleteChatroomImage = sprintf("UPDATE brand_projects SET chatroom_logo_url = NULL WHERE brand_projects.id = %d", $brand_project_id);
        mysql_query($deleteChatroomImage, $ifs) or die(mysql_error());

        mysql_close($ifs);

        header("location: brand_project_upload_pic.php?brand_project_id=" . $brand_project_id);
        exit();
    }
}

//Set message
if (strlen($error) > 0) {
    $message = $error;
}

/* If not importing file */
if (!$import) {
    include('views/popup.php');

    mysql_close($ifs);
} elseif (!$message) {
    include('views/import.php');
}
