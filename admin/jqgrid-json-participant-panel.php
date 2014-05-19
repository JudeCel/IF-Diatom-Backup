<?php 
// initialization
require_once("Connections/ifs.php");
require_once("getage.php");


$page  = (isset($_GET['page']) ? strip_tags(mysql_real_escape_string($_GET['page'])) : NULL); // get the requested page
$limit = (isset($_GET['rows']) ? strip_tags(mysql_real_escape_string($_GET['rows'])) : 0); // get how many rows we want to have into the grid
$sidx  = (isset($_GET['sidx']) ? strip_tags(mysql_real_escape_string($_GET['sidx'])) : NULL); // get index row - i.e. user click to sort
$sord  = (isset($_GET['sord']) ? strip_tags(mysql_real_escape_string($_GET['sord'])) : NULL); // get the direction
 
$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id'])); 
$session_id = strip_tags(mysql_real_escape_string($_GET['session_id'])); 
 
if(!$sidx) {
    $sidx = 1;
}
 
$totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;
 
mysql_select_db($database_ifs, $ifs) or die("database_ifs connection error.");
 
// get the count of rows
$pp_sql = "SELECT 
  participants.id AS ParticipantId,
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
  country_lookup.country_name
FROM
  client_users
  INNER JOIN client_companies ON (client_users.client_company_id = client_companies.id)
  INNER JOIN brand_projects ON (client_users.client_company_id = brand_projects.client_company_id)
  INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
  INNER JOIN users ON (participants.user_id = users.id)
  LEFT OUTER JOIN addresses ON (users.address_id = addresses.id)
  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id)  
WHERE
  brand_projects.id = $brand_project_id 
  AND (participants.participant_reply_id NOT IN (2) || participants.participant_reply_id IS NULL)
  AND participants.invite_again = 'Yes'
  AND 
  participants.id NOT IN (SELECT 
  participant_lists.participant_id
FROM
  participant_lists
WHERE
  participant_lists.session_id=$session_id)
GROUP BY
  participants.id";

$count = 0;
$row = array();

// get the count of rows
$result = mysql_query($pp_sql);
if($result){  
  $count = mysql_num_rows($result);
}
if($count){
  $row   = mysql_fetch_array($result, MYSQL_ASSOC);
}

//$count  = $row['count'];
 
// get the required variables
if($count >0 && $limit) {
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
}
 
// get the actual stuff to be displayed in the grid
$SQL    = "SELECT 
  participants.id AS ParticipantId,
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
  participants.optional1,
  participants.optional2,
  participants.optional3,
  participants.optional4,
  participants.optional5,
  country_lookup.country_name,
  p_list.participant_rating_id,
  p_list.comments,
  p_list.pl_id
FROM
  client_users
  INNER JOIN client_companies ON (client_users.client_company_id = client_companies.id)
  INNER JOIN brand_projects ON (client_users.client_company_id = brand_projects.client_company_id)
  INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
  LEFT JOIN (SELECT participant_id AS `pid`, id AS `pl_id`, participant_rating_id, comments FROM participant_lists) AS p_list ON (participants.id = p_list.pid)
  INNER JOIN users ON (participants.user_id = users.id)
  LEFT OUTER JOIN addresses ON (users.address_id = addresses.id)
  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id)
WHERE
  brand_projects.id = $brand_project_id  
  AND (participants.participant_reply_id NOT IN (2) || participants.participant_reply_id IS NULL)
  AND participants.invite_again = 'Yes'
  AND 
  participants.id NOT IN (SELECT 
  participant_lists.participant_id
FROM
  participant_lists
WHERE
  participant_lists.session_id=$session_id)  
GROUP BY
  participants.id
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

  	$example="";
      $makeAgreement="";
  	
  	$ParticipantName=$row['name_first'].' '.$row['name_last'];

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
  	
  	if($row['dob'])
  		$age='';
  	else
  		$age='';	
  	
      $response->rows[$i]['ParticipantId']   = $row['ParticipantId'];
      $response->rows[$i]['cell'] = array(
        $row['ParticipantId'],
				$ParticipantName,
        $row['Gender'],
        $row['email'],
        $row['mobile'],
        $row['phone'],
        $row['fax'],
				$row['street'],
        $row['state'],
        $row['suburb'],
        $row['post_code'],
        $row['dob'],
        $row['ethnicity'],
        $row['occupation'],
        $row['brand_segment'],
        $row['country_name'],
        $row['invites'],
        $row['invites_accepted'],
        $row['invites_not_now'],
        $row['invites_not_interested'],
        $row['invites_no_reply'],
        $row['optional1'],
        $row['optional2'],
        $row['optional3'],
        $row['optional4'],
        $row['optional5'],
        ($row['last_invite_name'] ? $row['last_invite_name'] : 'None Available'),        
        $interest,
        (isset($row['participant_rating_id']) && $row['participant_rating_id'] ? $row['participant_rating_id'] : 'None Available'),
				($row['invite_again'] ? 'Yes' : 'No'),
        (isset($row['comments']) && $row['comments'] ? '<a href="get_comments.php?participant_list_id=' . $row['pl_id'] . '" class="view_comments normal">View Comments</a>' : 'None Available')
      );
      $i++;
  }
}
 
// convert the response into JSON representation
echo json_encode($response);
 
// close the database_ifs connection
mysql_close($ifs);
