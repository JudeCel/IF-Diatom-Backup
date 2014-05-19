<?php 
// initialization
require_once("Connections/ifs.php");
if($_SESSION['MM_UserTypeId'] == -1 ||$_SESSION['MM_UserTypeId'] == 1 || $_SESSION['MM_UserTypeId'] == 2){
 	
 	$page  = (isset($_GET['page']) ? strip_tags(mysql_real_escape_string($_GET['page'])) : NULL); // get the requested page
	$limit = (isset($_GET['rows']) ? strip_tags(mysql_real_escape_string($_GET['rows'])) : 0); // get how many rows we want to have into the grid
	$sidx  = (isset($_GET['sidx']) ? strip_tags(mysql_real_escape_string($_GET['sidx'])) : NULL); // get index row - i.e. user click to sort
	$sord  = (isset($_GET['sord']) ? strip_tags(mysql_real_escape_string($_GET['sord'])) : NULL); // get the direction
	 
	if(!$sidx) {
	    $sidx = 1;
	}

	$totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;

	mysql_select_db($database_ifs, $ifs);

	if($_SESSION['MM_CompanyId'] == -1){
		$query=" ";
	} else {
		$query="AND 
	  brand_projects.client_company_id = '".strip_tags(mysql_real_escape_string($_SESSION['MM_CompanyId']))."'";
	}
	 
	// get the count of rows
	$sql = "SELECT 
	  users.name_first,
	  users.name_last,
	  users.email,
	  users.phone,
	  users.mobile,
	  users.fax,
	  user_logins.username,
	  user_logins.id,
	  brand_projects.name AS BPName,
	  sessions.name
	FROM
	  users
	  INNER JOIN user_logins ON (users.user_login_id = user_logins.id)
	  INNER JOIN participants ON (users.id = participants.user_id)
	  INNER JOIN participant_lists ON (participants.id = participant_lists.participant_id)
	  INNER JOIN brand_projects ON (participants.brand_project_id = brand_projects.id)
	  INNER JOIN sessions ON (participant_lists.session_id = sessions.id)
	WHERE
	  participant_lists.participant_reply_id = 1 " . $query;
	
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
	 
	 
	// get the actual stuff to be displayed in the grid
	$SQL    = "SELECT 
				  users.name_first,
				  users.name_last,
				  users.email,
				  users.phone,
				  users.mobile,
				  users.fax,
				  user_logins.username,
				  user_logins.id,
				  brand_projects.name AS BPName,
				  sessions.name
				FROM
				  users
				  INNER JOIN user_logins ON (users.user_login_id = user_logins.id)
				  INNER JOIN participants ON (users.id = participants.user_id)
				  INNER JOIN participant_lists ON (participants.id = participant_lists.participant_id)
				  INNER JOIN brand_projects ON (participants.brand_project_id = brand_projects.id)
				  INNER JOIN sessions ON (participant_lists.session_id = sessions.id)
				WHERE
				  participant_lists.participant_reply_id = 1 ".$query."
				ORDER BY 
					$sidx $sord LIMIT $start , $limit";

		
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
							
			$example="<a title='Reset Password'  href='reset_password.php?user_login_id=".$row['id']."'><span class='ui-icon ui-icon-folder-open'></span></a>";
			$name = $row['name_first'].' '.$row['name_last'];
			$brand_project_details = $row['BPName'].' (<b>Session:</b> '.$row['name'].')';
			
	    $response->rows[$i]['id']   = $row['id'];
	    $response->rows[$i]['cell'] = array($row['id'],$name, $row['username'] , $brand_project_details, $row['phone'] ,$row['mobile'] , $row['fax'], $row['email']);
	    $i++;		
		}
	}

	// convert the response into JSON representation
	echo json_encode($response);
	 
	// close the database_ifs connection
	mysql_close($ifs);

} else {
	mysql_close($ifs);

	header('Location: index.php');

	die();
}