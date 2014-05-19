<?php 
  require_once('Connections/ifs.php');
  require_once("core.php");
  require_once('models/users_model.php');
  require_once('models/participant-email-model.php');

  /* Get message information */
  $message = null;
  if(isset($_SESSION['notification'])){
    $message_val = new StdClass;
    $message_val->other = $_SESSION['notification'];
    
    $message = process_messages($message_val);

    unset($_SESSION['notification']);
  }

  /* Get user id */
  $user_id = 0;
  if(isset($_SESSION['MM_UserId']['user_id'])){
    $user_id = $_SESSION['MM_UserId'];
  }

  /* Get user type id */
  $user_type = null;
  if(isset($_SESSION['MM_UserTypeId'])){
    $user_type = $_SESSION['MM_UserTypeId'];
  }

  /* Get session id */
  $session_id = NULL;
  if(isset($_GET['session_id'])){
    $session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
  } else {
    $_SESSION['notification'] = 'An appropriate session hasn\'t been set';

    mysql_close($ifs);

    header('Location: session.php'); //return to main page
    die();
  }

  if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
    //Page properties
    $main_script = false;
    $other_content = 'session_participants';
    $grid = true;
    $validate = false;
    $inline_scripting = 'session_participants_inline';
    $page_help = 'sessions';
    
    //Only initialisation - if client company id is available, it is set further down
    $page = 'Sessions | Session Participants';
    $title = 'Sessions';
    
    $sub_id = null;
    $sub_group = null;
    $sub_navigation = null;

    $footer = 'session_footer';

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
      sessions.status_id,
      session_staff.session_id,
      users.name_first,
      users.name_last,
      session_staff.user_id,
      sessions.id,
      sessions.incentive_details,
      brand_projects.client_company_id,
      brand_projects.name AS `brand_project_name`,
      brand_projects.logo_url,
      client_companies.name AS `client_company_name`
    FROM
      sessions
      INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
      INNER JOIN users ON (session_staff.user_id = users.id)
      INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
      INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
    WHERE
      session_staff.type_id=2 AND sessions.id=$session_id    
    ";

    $retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error());

    $row_retSessionInfo = array();
    $totalRows_retSessionInfo = 0;
    $brand_project_id = null;
    $client_company_id = null;
    $brand_project_name = 'New Brand Project';
    $client_company_name = 'New Client Company';
    $session_name = '';
    $brand_project_logo_url = '';

    $status_id = null;
    $subtitle_found = false;

    if($retSessionInfo){
      $row_retSessionInfo = mysql_fetch_assoc($retSessionInfo);
      $totalRows_retSessionInfo = mysql_num_rows($retSessionInfo);

      $brand_project_id = $row_retSessionInfo['brand_project_id'];
      $client_company_id = $row_retSessionInfo['client_company_id'];

      $status_id = $row_retSessionInfo['status_id'];

      //Set Names
      $brand_project_name = $row_retSessionInfo['brand_project_name'];
      $client_company_name = $row_retSessionInfo['client_company_name'];
      $session_name = $row_retSessionInfo['name'];

      $brand_project_logo_url = $row_retSessionInfo['logo_url'];   
    }

    //Set variable that will allow display of brand project name and client company name
    if($client_company_id && $brand_project_id){
      $subtitle_found = true;
    }

    //Set session name as title
    if($session_name){
      $title = $session_name;
    }

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
      users.last_invite_name,
      participants.dob,
      participants.ethnicity,
      participants.occupation,
      participants.brand_segment,
      participants.participant_reply_id AS `interest`,
      participant_lists.id,
      participant_lists.participant_id,
      participant_reply_lookup.reply_name,
      participant_lists.participant_reply_id,
      participant_lists.participant_rating_id,
      participant_lists.comments
    FROM
      participants
      INNER JOIN users ON (participants.user_id = users.id)
      INNER JOIN participant_lists ON (participants.id = participant_lists.participant_id)
      LEFT OUTER JOIN participant_reply_lookup ON (participant_lists.participant_reply_id = participant_reply_lookup.id)
    WHERE 
      participant_lists.session_id=".$session_id."
     ";

    $retSessionParticipant = mysql_query($query_retSessionParticipant,$ifs) or die(mysql_error());
    $totalRows_retSessionParticipant = 0;

    $participants = array();
    $participants_json = json_encode(array());

    if($retSessionParticipant){
      $totalRows_retSessionParticipant = mysql_num_rows($retSessionParticipant);

      while($row_retSessionParticipant = mysql_fetch_assoc($retSessionParticipant)){
        if(isset($retSessionParticipant['id'])){
          $participants[] = $retSessionParticipant['id'];
        }
      }

      if($totalRows_retSessionParticipant > 0){
        mysql_data_seek($retSessionParticipant, 0);
      }
    }

    if(!empty($participants)){
      $participants_json = json_encode($participants);
    }

    $interest_array = array(
      'No Reply',
      'Interested',
      'Not Interested'
    );

    //Find the number of accepted participants
    $total_accepted_participants = get_total_participants($database_ifs, $ifs);
    if(!$total_accepted_participants){
      $total_accepted_participants = 0;
    }

    //The sub navigation for the content
    if($session_id){
      $sub_group = 'sessions';
      $sub_navigation = array(
        'Emails' => 'session-emails.php?session_id=' . $session_id,
        'Green Room Template' => 'session-greenroom.php?session_id=' . $session_id,
        'Participants' => 'session-participants-jqgrid.php?session_id=' . $session_id,
        'Observers' => 'session-edit.php?session_id=' . $session_id,
        'Topics' => 'newTopic.php?session_id=' . $session_id
      );
      $sub_id = $session_id;
    }

    require_once('views/home.php');
    mysql_close($ifs);
    die();
  } else {
    if(!$user_type){
      $_SESSION['notification'] = 'You are logged out, please login again.';
    } else {
      $_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
    }
  
    if(!admin($database_ifs, $ifs)){
      $_SESSION['current_location'] = $form_action;
    }

    mysql_close($ifs);
    
    header('Location: index.php');
    die();
  }

  mysql_close($ifs);
