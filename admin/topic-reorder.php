<?php	
  require_once('Connections/ifs.php');     
  require_once('core.php');

  /* Get user type id */
  $user_type = null;
  if(isset($_SESSION['MM_UserTypeId'])){
    $user_type = $_SESSION['MM_UserTypeId'];
  }

  if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
    if(isset($_GET['session_id'])){
      $session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
    } else {
      $_SESSION['notification'] = 'An appropriate session hasn\'t been set';

      mysql_close($ifs);

      header('Location: session.php'); //return to main page
      die();    
    }

    $page = 'Reorder Topics';
    $title = $page;
    $main_script = 'session_topic_reorder';
    $other_content = 'session_topic_reorder';
    $validate = false;
    $inline_scripting = 'session_topic_reorder_inline';

    //retrieve the brand project's details 
    mysql_select_db($database_ifs, $ifs);
    $query_retSessionInfo = "
    SELECT 
      sessions.brand_project_id,
      sessions.name,
      sessions.start_time,
      sessions.end_time,
      sessions.incentive_details,
      users.name_first,
      users.name_last,
      client_users.client_company_id,
      brand_projects.end_date,
      brand_projects.start_date
    FROM
      sessions
      INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
      INNER JOIN client_users ON (brand_projects.moderator_user_id = client_users.user_id)
      INNER JOIN users ON (client_users.user_id = users.id)
    WHERE
      sessions.id = $session_id  
    ";

    $retSessionInfo = mysql_query($query_retSessionInfo, $ifs) or die(mysql_error());

    $totalRows_retSessionInfo = 0;

    $brand_project_id = NULL;
    $client_company_id = NULL;

    /* If the session information query was successful */
    if($retSessionInfo){
      $totalRows_retSessionInfo = mysql_num_rows($retSessionInfo); //get total rows
    }

    /* If results were returned */
    if($totalRows_retSessionInfo){
      $row_retSessionInfo = mysql_fetch_assoc($retSessionInfo); //get session info

      /* Retrieve the brand project and client company IDs */
      $brand_project_id = $row_retSessionInfo['brand_project_id'];
      $client_company_id = $row_retSessionInfo['client_company_id'];
    }

    //retrieve the topics
    mysql_select_db($database_ifs, $ifs);
    $query_retTopic = "
    SELECT 
      topics.*
    FROM
      topics
    WHERE
       topics.session_id=$session_id
    ORDER BY
      topics.topic_order_id       
    ";

    $retTopic = mysql_query($query_retTopic, $ifs) or die(mysql_error());

    $totalRows_retTopic = 0;

    /* If the topic query was successful */
    if($retTopic){
      $totalRows_retTopic = mysql_num_rows($retTopic); //set total rows
    }

    //if the form was submitted
    if(isset($_POST['btnSubmit'])){
      // use $i to increment the order
      $i = 1;

      $order = array();
      if(isset($_POST['order']) && $_POST['order']){
        $order = $_POST['order'];
      }

      // loop through post array in the order it was submitted
      foreach($order as $order_id) {
        $sql = sprintf("UPDATE topics SET topic_order_id = %d WHERE id= %d", $i, mysql_real_escape_string($order_id));

        // update the row
        $result = mysql_query($sql, $ifs);
        // increment weight to make the next fruit heavier
        $i++;
      }

      mysql_close($ifs);
      
      $updateGoTo = $form_action;
      header(sprintf("Location: %s", $updateGoTo));
      die();
    }    
  } else {
    if(!$user_type){
      $_SESSION['notification'] = 'You are logged out, please login again.';
    } else {
      $_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
    }

    mysql_close($ifs);

    header('Location: index.php');
    die();
  }

  require_once('views/popup.php');

  mysql_close($ifs);