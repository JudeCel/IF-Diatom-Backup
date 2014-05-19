<?php 
// initialization
require_once("Connections/ifs.php");
require_once('talkToNode.php');
if($_SESSION['MM_UserTypeId'] == 1 || $_SESSION['MM_UserTypeId'] == -1){
if($_SESSION['MM_CompanyId'] == -1){
	$query="";
}
else{
	$query=" and
	client_company_contacts.client_company_id = '".$_SESSION['MM_CompanyId']."'";
	}
$page  = (isset($_GET['page']) ? strip_tags(mysql_real_escape_string($_GET['page'])) : NULL); // get the requested page
$limit = (isset($_GET['rows']) ? strip_tags(mysql_real_escape_string($_GET['rows'])) : 0); // get how many rows we want to have into the grid
$sidx  = (isset($_GET['sidx']) ? strip_tags(mysql_real_escape_string($_GET['sidx'])) : NULL); // get index row - i.e. user click to sort
$sord  = (isset($_GET['sord']) ? strip_tags(mysql_real_escape_string($_GET['sord'])) : NULL); // get the direction
 
if(!$sidx) {
    $sidx = 1;
}
 
$totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;
 

if ($limit <0) {
    $limit = 0;
}
 
$start = $limit * $page - $limit;
 
if ($start <0) {
    $start = 0;
}

switch($sidx){
	case 'name':
		$sidx = 'client_companies.name';
	break;
	case 'PrimaryContact':
		$sidx = 'client_company_contacts.name_first';
	break;
	case 'CompanyComments':
		$sidx = 'client_companies.comments';
	break;
	case 'start_date':
		$sidx = 'client_companies.start_date';
	break;
	case 'end_date':
		$sidx = 'client_companies.end_date';
	break;
	case 'Address':
		$sidx = 'addresses.street';
	break;
	case 'URL':
		$sidx = 'client_companies.URL';
	break;
	case 'ABN':
		$sidx = 'client_companies.ABN';
	break;
}

    $params =new stdClass();
    $params->companyId=$_SESSION['MM_CompanyId'];
    $params->sidx=$sidx;
    $params->sord=$sord;
    $params->start=$start;
    $params->limit=$limit;
    talkToNode("/getClientCompanyInfo",$params,$result);


    $count = 0;
    $rows = array();


// get the count of rows
    if($result){
        $rows   = json_decode($result);
        $count = sizeof($rows);
    }

//$count  = $row['count'];

// get the required variables
    if( $count>0 && $limit) {
        $total_pages = ceil($count / $limit);
    } else {
        $total_pages = 0;
    }
/*
    if ($page> $total_pages) {
        $page = $total_pages;
    }*/

//$result = mysql_query($SQL) or die("Could not execute query." . mysql_error());

// create a response array from the obtained result
$response = new StdClass;
$response->page    = $page;
$response->total   = $total_pages;
$response->records = $count;
$i= 0;

$rows   = json_decode($result,true);

for ($j=0;$j<sizeof($rows);$j++) {
    $row = $rows[$j];
	/* Go to specific file according to user type */
  $page_file = 'signup.php';
  if($_SESSION['MM_UserTypeId'] == 1){
    $page_file = 'clientCompanyUsers.php';
  }

  $example="<a title='Configure Company' href='" . $page_file . "?client_company_id=" . $row['client_company_id'] . "'><span class='ui-icon ui-icon-gear'></span></a>";

	$primary_contact = $row['name_first'].' '.$row['name_last'];
	$address = $row['street'].', '.$row['suburb'].', '.$row['state'].' '.$row['post_code'];

  $delete_url = 'N/A';
  if($_SESSION['MM_UserTypeId'] == -1){
    $delete_url = '<a title="Delete Client Company" href="CompanyDelete.php?client_company_id=' . $row['client_company_id'] . '&panel=1"><span class="ui-icon delete"></span></a>';
  }

  $response->rows[$i]['client_company_id']   = $row['client_company_id'];
  $response->rows[$i]['cell'] = array($row['name'],$primary_contact,stripslashes($row['comments']), date('d-m-Y', strtotime($row['start_date'])),date('d-m-Y', strtotime($row['end_date'])),stripslashes($address),$row['uRL'], $row['aBN'], $example, $delete_url);
  $i++;
}

// convert the response into JSON representation
echo json_encode($response);
 
// close the database_ifs connection


?>




<?php
}else{

if($_SESSION['MM_CompanyId'] == -1){
	$query="  ";
}
else{
	$query=" and
	client_company_contacts.client_company_id = '".$_SESSION['MM_CompanyId']."'";
	}
$page  = strip_tags(mysql_real_escape_string($_GET['page'])); // get the requested page
$limit = strip_tags(mysql_real_escape_string($_GET['rows'])); // get how many rows we want to have into the grid
$sidx  = strip_tags(mysql_real_escape_string($_GET['sidx'])); // get index row - i.e. user click to sort
$sord  = strip_tags(mysql_real_escape_string($_GET['sord'])); // get the direction
 
if(!$sidx) {
    $sidx = 1;
}
 
$totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;
 
if($totalrows) {
    $limit = $totalrows;
}
 

 
// get the count of rows

 
$start = $limit * $page - $limit;
 
if ($start <0) {
    $start = 0;
}

switch($sidx){
  case 'name':
    $sidx = 'client_companies.name';
  break;
  case 'PrimaryContact':
    $sidx = 'client_company_contacts.name_first';
  break;
  case 'CompanyComments':
    $sidx = 'client_companies.comments';
  break;
  case 'start_date':
    $sidx = 'client_companies.start_date';
  break;
  case 'end_date':
    $sidx = 'client_companies.end_date';
  break;
  case 'Address':
    $sidx = 'addresses.street';
  break;
  case 'URL':
    $sidx = 'client_companies.URL';
  break;
  case 'ABN':
    $sidx = 'client_companies.ABN';
  break;
}

    if ($limit <0) {
        $limit = 0;
    }

// get the actual stuff to be displayed in the grid
    $params =new stdClass();
    $params->companyId=$_SESSION['MM_CompanyId'];
    $params->sidx=$sidx;
    $params->sord=$sord;
    $params->start=$start;
    $params->limit=$limit;
    talkToNode("/getClientCompanyInfo",$params,$result);



// get the count of rows
    if($result){
        $rows   = json_decode($result);
        $count = sizeof($rows);
    }

    if( $count>0 && $limit) {
        $total_pages = ceil($count / $limit);
    } else {
        $total_pages = 0;
    }

// create a response array from the obtained result
$response = new StdClass;
$response->page    = $page;
$response->total   = $total_pages;
$response->records = $count;
$i= 0;

$rows   = json_decode($result,true);

for ($j=0;$j<sizeof($rows);$j++) {
    $row = $rows[$j];

	$example="<a title='Configure Company'  href='signup.php?client_company_id=".$row['client_company_id']."'><span class='ui-icon ui-icon-gear'></span></a>";
	
	$primary_contact=$row['name_first'].' '.$row['name_last'].'  (M:'.$row['mobile'].')';
	$address=$row['street'].','.$row['suburb'].','.$row['state'].'-'.$row['post_code'];

  $delete_url = 'N/A';
  if($_SESSION['MM_UserTypeId'] == -1){
    $delete_url = '<a title="Delete Client Company" href="CompanyDelete.php?client_company_id=' . $row['client_company_id'] . '&panel=1"><span class="ui-icon delete"></span></a>';
  }
   
  $response->rows[$i]['client_company_id']   = $row['client_company_id'];
  $response->rows[$i]['cell'] = array($row['name'],$primary_contact,stripslashes($row['comments']),stripslashes($address),$row['uRL'], $row['aBN'], $delete_url, '');
  $i++;
}
 
// convert the response into JSON representation
echo json_encode($response);

// close the database_ifs connection

}

?>
