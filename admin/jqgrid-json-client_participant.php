<?php 
// initialization
require_once("Connections/ifs.php");
require_once("core.php");

$page  = (isset($_GET['page']) ? strip_tags(mysql_real_escape_string($_GET['page'])) : NULL); // get the requested page
$limit = (isset($_GET['rows']) ? strip_tags(mysql_real_escape_string($_GET['rows'])) : 0); // get how many rows we want to have into the grid
$sidx  = (isset($_GET['sidx']) ? strip_tags(mysql_real_escape_string($_GET['sidx'])) : NULL); // get index row - i.e. user click to sort
$sord  = (isset($_GET['sord']) ? strip_tags(mysql_real_escape_string($_GET['sord'])) : NULL); // get the direction
 
$brand_project_id = (isset($_GET['brand_project_id']) ? $_GET['brand_project_id'] : NULL);
 
if(!$sidx) {
    $sidx = 1;
}
 
$totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;

/* Set user type */
$user_type = NULL;
if($_SESSION['MM_UserTypeId']){
  $user_type = $_SESSION['MM_UserTypeId'];
}
 
mysql_select_db($database_ifs, $ifs);
 
// get the count of rows
$SQL    = "SELECT 
  brand_projects.name AS brand_project_name,
  client_companies.name AS client_companies_name,
  users.name_first,
  users.name_last,
  users.email,
  users.phone,
  users.fax,
  users.mobile,
  users.job_title,
  users.Gender,
  addresses.street,
  addresses.post_code,
  addresses.suburb,
  addresses.state,
  participants.dob,
  participants.ethnicity,
  participants.occupation,
  participants.brand_segment,
  participants.id,
  country_lookup.country_name,
  participants.optional2,
  participants.optional1,
  participants.optional3,
  participants.optional4,
  participants.optional5
FROM
  client_users
  INNER JOIN client_companies ON (client_users.client_company_id = client_companies.id)
  INNER JOIN brand_projects ON (client_users.client_company_id = brand_projects.client_company_id)
  INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
  INNER JOIN users ON (participants.user_id = users.id)
  INNER JOIN addresses ON (users.address_id = addresses.id)
  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id)
  
WHERE 
  brand_projects.id=".$brand_project_id."
GROUP BY
  participants.id";

$count = 0;
$row = array();

// get the count of rows
$result = mysql_query($SQL);
if($result){  
  $count = mysql_num_rows($result);
}
if($count){
  $row   = mysql_fetch_array($result, MYSQL_ASSOC);
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

switch($sidx){
	case 'name':
		$sidx = 'users.name_first';		
	break;
	case 'gender':
		$sidx = 'users.Gender';
	break;
	case 'email':
		$sidx = 'users.email';
	break;
	case 'mobile':
		$sidx = 'users.mobile';
	break;
	case 'phone':
		$sidx = 'users.phone';
	break;
	case 'fax':
		$sidx = 'users.fax';
	break;
	case 'state':
		$sidx = 'addresses.state';
	break;
	case 'suburb':
		$sidx = 'addresses.suburb';
	break;
	case 'postcode':
		$sidx = 'addresses.postcode';
	break;
	case 'dob':
		$sidx = 'addresses.dob';
	break;
	case 'ethnicity':
		$sidx = 'participants.ethnicity';
	break;
	case 'occupation':
		$sidx = 'participants.occupation';
	break;
	case 'brandsegment':
		$sidx = 'participants.brand_segment';
	break;
	case 'country':
		$sidx = 'country_lookup.country_name';
	break;
	case 'number_of_invites':
		$sidx = 'users.invites';
	break;
	case 'last_invited_session_name':
		$sidx = 'users.last_invite_name';
	break;
	case 'rating15':
		$sidx = 'p_list.participant_rating_id';
	break;
	case 'more':
		$sidx = 'brand_project_name';
	break;
}
 
// get the actual stuff to be displayed in the grid
$SQL    = "SELECT 
  brand_projects.name AS brand_project_name,
  client_companies.name AS client_companies_name,
  users.name_first,
  users.name_last,
  users.email,
  users.phone,
  users.fax,
  users.mobile,
  users.job_title,
  users.Gender,
  users.invites,
  users.invites_accepted,
  users.invites_not_now,
  users.invites_not_interested,
  users.invites_no_reply,
  users.last_invite_name,
  addresses.street,
  addresses.post_code,
  addresses.suburb,
  addresses.state,
  participants.dob,
  participants.ethnicity,
  participants.occupation,
  participants.brand_segment,
  participants.invite_again,
  participants.interested,
  participants.id AS 'participant_id',
  country_lookup.country_name,
  participants.optional2,
  participants.optional1,
  participants.optional3,
  participants.optional4,
  participants.optional5,
  p_list.pid,
  p_list.participant_rating_id,
  p_list.comments
FROM
  client_users
  INNER JOIN client_companies ON (client_users.client_company_id = client_companies.id)
  INNER JOIN brand_projects ON (client_users.client_company_id = brand_projects.client_company_id)
  INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
  LEFT JOIN (SELECT id AS `plid`, participant_id AS `pid`, participant_rating_id, comments FROM participant_lists) AS p_list ON (participants.id = p_list.pid)
  INNER JOIN users ON (participants.user_id = users.id)
  INNER JOIN addresses ON (users.address_id = addresses.id)
  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id)
  
WHERE 
  brand_projects.id = " . $brand_project_id . "
GROUP BY
  participants.id, plid
ORDER BY
	$sidx $sord
LIMIT $start, $limit";

$count = 0;

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

if($count){
  while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {	

  	$pariticipant_name=$row['name_first'].' '.$row['name_last'];

    //Record interest
    $interest = 'Interest Not Recorded Yet';
    if($row['interested']){
      //Set the level of interest
      if($row['interested'] == 1){
        $interest = 'Interested';
      } else {
        $interest = 'Not Interested';
      }
    }
  	
  	$response->rows[$i]['id'] = $row['participant_id'];
  	 
  	$edit ="<a title='Edit Participant' href='participant-edit.php?participant_id=".$row['participant_id']."'><span class='ui-icon ui-icon-pencil'></span></a>";

      $response->rows[$i]['cell'] = array(
        $pariticipant_name,
        $row['Gender'],
        $row['email'],
        $row['mobile'],
        $row['phone'],
        $row['fax'],
        $row['street'],
        $row['state'],
        $row['suburb'],
        $row['post_code'],
        date('d-m-Y', strtotime($row['dob'])),
        $row['ethnicity'],
        $row['occupation'],
        $row['brand_segment'], 
        $row['country_name'],
        $row['invites'],
        $row['invites_accepted'],
        $row['invites_not_now'],
        $row['invites_not_interested'],
        $row['invites_no_reply'],
        $row['last_invite_name'],
				$interest, 
        (isset($row['participant_rating_id']) && $row['participant_rating_id'] ? $row['participant_rating_id'] : 'None Available'),        
        $row['invite_again'],
        (isset($row['comments']) ? $row['comments'] : ''),  
        $row['optional1'],
        $row['optional2'],
        $row['optional3'],
        $row['optional4'],
        $row['optional5'],
        ($user_type < 4 ? $edit : 'N/A')
      );
      $i++;
  }
}
 
// convert the response into JSON representation
echo json_encode($response);

mysql_close($ifs);
