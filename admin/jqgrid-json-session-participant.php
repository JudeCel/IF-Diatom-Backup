<?php 
// initialization
require_once("Connections/ifs.php");

if($_SESSION['MM_UserTypeId'] == 5){
  $page  = (isset($_GET['page']) ? strip_tags(mysql_real_escape_string($_GET['page'])) : NULL); // get the requested page
  $limit = (isset($_GET['rows']) ? strip_tags(mysql_real_escape_string($_GET['rows'])) : 0); // get how many rows we want to have into the grid
  $sidx  = (isset($_GET['sidx']) ? strip_tags(mysql_real_escape_string($_GET['sidx'])) : NULL); // get index row - i.e. user click to sort
  $sord  = (isset($_GET['sord']) ? strip_tags(mysql_real_escape_string($_GET['sord'])) : NULL); // get the direction
   
   
  if(!$sidx) {
      $sidx = 1;
  }
   
  $totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;
   
  mysql_select_db($database_ifs, $ifs);
   
  // get the count of rows
  $sql = "SELECT 
    brand_projects.name AS BPName,
    sessions.name,
    sessions.start_time,
    sessions.end_time,
    sessions.id As session_id, 
    users.name_last,
    users.name_first,
    users.mobile,
    client_companies.name AS CompanyName
  FROM
    sessions
    INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
    INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
    INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
    INNER JOIN users ON (users.id = participants.user_id)
  WHERE
    brand_projects.client_company_id = '".strip_tags(mysql_real_escape_string($_SESSION['MM_CompanyId']))."'
    ";

  $count = 0;
  $row = array();

  // get the count of rows
  $result = mysql_query($sql);
  if($result){  
    $count = mysql_num_rows($result);
  }
  if($count){
    $row   = mysql_fetch_array($result, MYSQL_ASSOC);
  } 

  //$count  = $row['count'];
   
  // get the required variables
  if( $count > 0 && $limit) {
      $total_pages = ceil($count / $limit);
  } else {
      $total_pages = 0;
  }
   
  if ($page > $total_pages) {
      $page = $total_pages;
  }
   
  if ($limit < 0) {
      $limit = 0;
  }
   
  $start = $limit * $page - $limit;
   
  if ($start <0) {
      $start = 0;
  }

  ///// Session Facilitator
  $SQL    = "SELECT 
    brand_projects.name AS BPName,
    sessions.name,
    sessions.start_time,
    sessions.end_time,
    sessions.id As session_id, 
    users.name_last,
    users.name_first,
    users.mobile,
    client_companies.name AS CompanyName
  FROM
    sessions
    INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
    INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
    INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
    INNER JOIN users ON (users.id = participants.user_id)
  WHERE
    brand_projects.client_company_id = '".$_SESSION['MM_CompanyId']."' AND 
    participants.user_id = '".strip_tags(mysql_real_escape_string($_SESSION['MM_UserId']))."'
  ORDER BY 
  	$sidx $sord LIMIT $start , $limit";
  	
  	
  $count = 0;
  $row = array();

  // get the count of rows
  $result = mysql_query($SQL);
  if($result){  
    $count = mysql_num_rows($result);
  }
  
  // create a response array from the obtained result
  $response = new StdClass;
  $response->page    = $page;
  $response->total   = $total_pages;
  $response->records = $count;
  $i= 0;

  //// Session Participant
  if($count){
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {	

    	$example="  ";
      $url = 'IFS/index.php?session_id=' . $row['session_id'];
      $makeAgreement ="<a title='Enter Session1' target='_blank' href='" . $url . "'><span class='ui-icon ui-icon-play'></span></a>";
    	
    	//retrieve the session moderator
    		mysql_select_db($database_ifs, $ifs);
    		$query_retSessionMod = "
    		SELECT 
    		  session_staff.user_id,
    		  session_staff.session_id,
    		  users.name_first,
    		  users.name_last,
    		  users.mobile
    		FROM
    		  session_staff
    		  INNER JOIN users ON (session_staff.user_id = users.id)
    		 WHERE
    		   session_staff.session_id=".$row['session_id']." 
    		   AND session_staff.type_id=2
    		";
    		$retSessionMod = mysql_query($query_retSessionMod, $ifs) or die(mysql_error());
    		$row_retSessionMod = mysql_fetch_assoc($retSessionMod);
    		$totalRows_retSessionMod = mysql_num_rows($retSessionMod);

    	
    	
    	$moderator_name=$row_retSessionMod['name_first'].' '.$row_retSessionMod['name_last'].'  (M: '.$row_retSessionMod['mobile'].')';    	
    	
      $response->rows[$i]['session_id']   = $row['session_id'];
      $response->rows[$i]['cell'] = array($row['CompanyName'],$row['BPName'],$row['name'],$moderator_name, date('d-m-Y H:i', strtotime($row['start_time'])),date('d-m-Y H:i', strtotime($row['end_time'])),$makeAgreement, $example );
      $i++;
    }
  }

  // convert the response into JSON representation
  echo json_encode($response);
}

// close the database_ifs connection
mysql_close($ifs);
