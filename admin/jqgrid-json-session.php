<?php
// initialization
require_once("Connections/ifs.php");
require_once("core.php");
require_once('talkToNode.php');

//User type
$user_type = null;
if(isset($_SESSION['MM_UserTypeId'])){
  $user_type = $_SESSION['MM_UserTypeId'];
}

if($_SESSION['MM_UserTypeId'] == 2 || $_SESSION['MM_UserTypeId'] == 3 || $_SESSION['MM_UserTypeId'] == 4){

  $page = 0;
  if(isset($_GET['page'])){
    $page = strip_tags(mysql_real_escape_string($_GET['page'])); // get the requested page
  }

  $limit = 0; // get how many rows we want to have into the grid
  if(isset($_GET['rows'])){
    $limit = strip_tags(mysql_real_escape_string($_GET['rows']));
  }

  $sidx = NULL;
  if(isset($_GET['sidx'])){
    $sidx = strip_tags(mysql_real_escape_string($_GET['sidx'])); // get index row - i.e. user click to sort
  }

  $sord = NULL;
  if(isset($_GET['sord'])){
    $sord = strip_tags(mysql_real_escape_string($_GET['sord'])); // get the direction
  }

  if($_SESSION['MM_CompanyId'] == -1){
    $query="  ";
  }
  else{
    $query=" and
    client_users.client_company_id = '".strip_tags(mysql_real_escape_string($_SESSION['MM_CompanyId']))."'";
    }


  if(!$sidx) {
      $sidx = 1;
  }

  $totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;

  // connect to the database_ifs


  // get the count of rows
    $params =new stdClass();
    $params->companyId=$_SESSION['MM_CompanyId'];
    talkToNode("/getSessionDataForGrid",$params,$result);

  $rows   = json_decode($result,true);
  $count = sizeof($rows); //

  //$count  = $row['count'];

  // get the required variables
  if( $count>0 && $limit) {
      $total_pages = ceil($count / $limit);
  } else {
      $total_pages = 0;
  }

  if ($page> $total_pages) {
      $page = $total_pages;
  }

  if ($limit <0) {
      $limit = 0;
  }

  $start = $limit * $page - $limit;

  if ($start <0) {
      $start = 0;
  }

  switch($sidx){
    case 'name':
      $sidx = 'sessions.name';
    break;
    case 'moderator_name':
      $sidx = 'users.name_first';
    break;
    case 'status':
      $sidx = 'sessions.status_id';
    break;
  }

  ///// Session Facilitator

  //Session Facilitator SQL
  $count = 0;

  // get the count of rows
    $params->sidx=$sidx;
    $params->sord=$sord;
    $params->start=$start;
    $params->limit=$limit;
    $params->userId=$_SESSION['MM_UserId'];
    $params->type=2;
    talkToNode("/getSessionDataForGridByUser",$params,$result);
    $rows = json_decode($result,true);
    if($rows)
    {
        $count = sizeof($rows);
    }

    $count1 = 0;


    $params->type=4;
    talkToNode("/getSessionDataForGridByUser",$params,$result1);
    $rows1 = json_decode($result1,true);
    if($rows1){
        $count1 = sizeof($rows1);
    }

  // create a response array from the obtained result
  $response = new StdClass;
  $response->page    = $page;
  $response->total   = $total_pages;
  $response->records = $count;
  $i= 0;

  ///// session Facilitator loop
  if($count){
    for($j=0;$j<sizeof($rows);$j++)
    {
      $row = $rows[$j];

      $example="<a title='Configure Session' href='session-emails.php?session_id=".$row['session_id']."'><span class='ui-icon ui-icon-gear'></span></a>";
      $makeAgreement="<a title='Enter Session2' target='_blank' href='IFS/index.php?session_id=".$row['session_id']."&bypass=1'><span class='ui-icon ui-icon-play'></span></a>";
      $empty = "";

      $moderator_name=$row['name_first'] . ' ' . $row['name_last'];

      /* Set status */
      $status = 'Open';
      if(isset($row['status_id']) && $row['status_id'] == 2){
        $status = 'Closed';
      }

      $response->rows[$i]['session_id']   = $row['session_id'];
      $response->rows[$i]['cell'] = array($row['companyName'],$row['bPName'],$row['name'],$moderator_name, date('d-m-Y H:i', strtotime($row['start_time'])),date('d-m-Y H:i', strtotime($row['end_time'])), $status, $example, $makeAgreement, 'N/A');
      $i++;
    }
  }

  ////// Session Observer Loop
    $params =new stdClass();
  if($count1){
      for($j=0;$j<sizeof($rows1);$j++)
    {
        $row = $rows1[$j];

      $example="<a title='Configure Session' href='session-emails.php?session_id=".$row['session_id']."'><span class='ui-icon ui-icon-gear'></span></a>";
      $makeAgreement="<a title='Enter Session3' target='_blank' href='$BASE_URL?id=" . $_SESSION['MM_UserId'] . "&sid=" . $row['session_id']. "'><span class='ui-icon ui-icon-play'></span></a>";
      $empty = "";
        //retrieve the session moderator
        $params->sessionId=$row['session_id'];
        talkToNode("/getSessionMod",$params,$retSessionMod);
        //$retSessionMod = mysql_query($query_retSessionMod, $ifs) or die(mysql_error());
        $row_retSessionMod = json_decode($retSessionMod,true);

      /* Set status */
      $status = 'Open';
      if(isset($row['status_id']) && $row['status_id'] == 2){
        $status = 'Closed';
      }

      $moderator_name=$row_retSessionMod['name_first'] . ' ' . $row_retSessionMod['name_last'];

      $response->rows[$i]['session_id']   = $row['session_id'];
      $response->rows[$i]['cell'] = array($row['companyName'],$row['bPName'],$row['name'],$moderator_name, date('d-m-Y H:i', strtotime($row['start_time'])),date('d-m-Y H:i', strtotime($row['end_time'])), $status, $makeAgreement,$empty, 'N/A');
      $i++;
    }
  }

  // convert the response into JSON representation
  echo json_encode($response);
  // close the database_ifs connection

} else if($_SESSION['MM_UserTypeId'] == -1 || $_SESSION['MM_UserTypeId'] == 1){

  $page = NULL;
  if(isset($_GET['page'])){
    $page = strip_tags(mysql_real_escape_string($_GET['page'])); // get the requested page
  }

  $limit = 0; // get how many rows we want to have into the grid
  if(isset($_GET['rows'])){
    $limit = strip_tags(mysql_real_escape_string($_GET['rows']));
  }

  $sidx = NULL;
  if(isset($_GET['sidx'])){
    $sidx = strip_tags(mysql_real_escape_string($_GET['sidx'])); // get index row - i.e. user click to sort
  }

  $sord = NULL;
  if(isset($_GET['sord'])){
    $sord = strip_tags(mysql_real_escape_string($_GET['sord'])); // get the direction
  }

  if($_SESSION['MM_CompanyId'] == -1){
    $query = "  ";
  } else{
    $query = " and
    client_users.client_company_id = '".strip_tags(mysql_real_escape_string($_SESSION['MM_CompanyId']))."'";
  }


  if(!$sidx) {
      $sidx = 1;
  }

  $totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;

    $params =new stdClass();
    $params->companyId=$_SESSION['MM_CompanyId'];
    talkToNode("/getSessionDataForGrid",$params,$result);

    $rows   = json_decode($result,true);
    $count = sizeof($rows); //

  //$count  = $row['count'];

  // get the required variables
  if( $count>0 && $limit) {
      $total_pages = ceil($count / $limit);
  } else {
      $total_pages = 0;
  }

  if ($page> $total_pages) {
      $page = $total_pages;
  }

  if ($limit <0) {
      $limit = 0;
  }

  $start = $limit * $page - $limit;

  if ($start <0) {
      $start = 0;
  }

  /* Get user id */
  $user_id = 0;
  if(isset($_SESSION['MM_UserId']['user_id'])){
    $user_id = $_SESSION['MM_UserId'];
  }


  switch($sidx){
    case 'name':
      $sidx = 'sessions.name';
    break;
    case 'moderator_name':
      $sidx = 'users.name_first';
    break;
    case 'status':
      $sidx = 'sessions.status_id';
    break;
  }

  if($sidx == 'configure' || $sidx == 'enter' || $sidx == 'delete'){
    $sidx = NULL;
  }

    $SQL = "";
    if($sidx && $sord){
        $SQL .= "\n";
        $SQL .= 'ORDER BY' . "\n";
        $SQL .= $sidx . ' ' . $sord . "\n";
    }

    if(isset($start) && isset($limit)){
        $SQL .= "\n";
        $SQL .= 'LIMIT' . "\n";
        $SQL .= $start . ', ' . $limit;
    }


  $count = 0;
  $row = array();

    $params->limit=$SQL;
    talkToNode("/getSessionDataForGrid",$params,$result1);

    $rows   = json_decode($result1,true);

    // get the count of rows
  //$result = mysql_query($SQL);
  if($rows){
    $count = sizeof($rows);
  }

  $response = new StdClass;
  $response->page    = $page;
  $response->total   = $total_pages;
  $response->records = $count;
  $response->rows = array();
  $i= 0;

  if($count){
      for($j=0;$j<sizeof($rows);$j++)
      {
          $row = $rows[$j];

      $url = $BASE_URL."?id=" . $_SESSION['MM_UserId'] . "&sid=" . $row['session_id'];

      $delete = 'N/A';
      if($user_type == -1){
        $delete = "<a title='Delete Session' href='SessionDelete.php?session_id=".$row['session_id']."'><span class='ui-icon delete'></span></a>";
      }

      /* Set status */
      $status = 'Open';
      if(isset($row['status_id']) && $row['status_id'] == 2){
        $status = 'Closed';
      }

      $example="<a title='Configure Session' href='session-emails.php?session_id=".$row['session_id']."'><span class='ui-icon ui-icon-gear'></span></a>";

      $makeAgreement = "<a title='Enter Session4' target='_blank' href='" . $url . "'><span class='ui-icon ui-icon-play'></span></a>";

      $moderator_name = stripslashes($row['name_first'] . ' ' . $row['name_last']);

      $response->rows[$i]['session_id']   = $row['session_id'];
      $response->rows[$i]['cell'] = array(stripslashes($row['companyName']), stripslashes($row['bPName']), stripslashes($row['name']), $moderator_name, date('d-m-Y H:i', strtotime($row['start_time'])),date('d-m-Y H:i', strtotime($row['end_time'])), $status, $example, $makeAgreement, $delete);
      $i++;
    }
  }

  // convert the response into JSON representation
  echo json_encode($response);


}