<?php 
// initialization
require_once("Connections/ifs.php");
require_once("core.php");
require_once("models/users_model.php");

$page  = (isset($_GET['page']) ? strip_tags(mysql_real_escape_string($_GET['page'])) : NULL); // get the requested page
$limit = (isset($_GET['rows']) ? strip_tags(mysql_real_escape_string($_GET['rows'])) : 0); // get how many rows we want to have into the grid
$sidx  = (isset($_GET['sidx']) ? strip_tags(mysql_real_escape_string($_GET['sidx'])) : NULL); // get index row - i.e. user click to sort
$sord  = (isset($_GET['sord']) ? strip_tags(mysql_real_escape_string($_GET['sord'])) : NULL); // get the direction
 
$brand_project_id = (isset($_GET['brand_project_id']) ? $_GET['brand_project_id'] : NULL);
 
if(!$sidx) {
    $sidx = 1;
}
 
$totalrows = isset($_GET['totalrows']) ? $_GET['totalrows']: false;

/* Set user type */
$user_type = NULL;
if($_SESSION['MM_UserTypeId']){
  $user_type = strip_tags(mysql_real_escape_string($_SESSION['MM_UserTypeId']));
}

/* Check if session id is set */
$session_id = NULL;
if(isset($_GET['session_id'])){
  $session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
}

switch($sidx){
	case 'email':
		$sidx = 'users.email';
	break;
	case 'name':
		$sidx = 'users.name_first';
	break;
}
 
$result = retrieve_potential_observers($database_ifs, $ifs, $brand_project_id, $session_id);

if($result && !is_string($result)){
  //$row   = mysql_fetch_array($result, MYSQL_ASSOC);
  $count = mysql_num_rows($result);
} else {
  //$row = array();
  $count = 0;
}

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
 
 
// create a response array from the obtained result
$response = new StdClass;
$response->page    = $page;
$response->total   = $total_pages;
$response->records = $count;
$i= 0;

if($count){
  while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {	

  	$observer_name=$row['name_first'].' '.$row['name_last'];

  	$response->rows[$i]['id'] = $row['user_id'];
  	 
  	$edit = '<a title="Edit Observer" class="editContact" href="clientCompanyUsers-edit.php?user_id=' . $row["user_id"] . '&staff=1"><span class="ui-icon ui-icon-pencil"></span></a>';

    $response->rows[$i]['cell'] = array(
      $observer_name,
      $row['job_title'],
      $row['email'],
      $row['mobile'],      
      ($user_type < 2 ? $edit : 'N/A')
    );

    $i++;
  }
}
 
// convert the response into JSON representation
echo json_encode($response);

mysql_close($ifs);
