<?php

// initialization
	require_once("Connections/ifs.php");
	require_once("models/brand_model.php");
	require_once("models/array_repository_model.php");
    require_once('talkToNode.php');

	//User type
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	//Company id
	$company_id = null;
	if(isset($_SESSION['MM_CompanyId'])){
		$company_id = $_SESSION['MM_CompanyId'];
	}

	//User id
	$user_id = null;
	if(isset($_SESSION['MM_UserId'])){
		$user_id = $_SESSION['MM_UserId'];
	}

	$allowed_types = array(-1, 1, 2, 3, 4);

	//Check if they can be used
	if(in_array($user_type, $allowed_types)){
		$query = ''; //initialise query

		if($company_id !== -1 && $company_id){
			$query = 'WHERE client_company_id = ' . $company_id;
		}
	} else {
		return FALSE;
	}

	$page = null;
	if(isset($_GET['page'])){
		$page = strip_tags(mysql_real_escape_string($_GET['page']));
	}

	$limit = 0;
	if(isset($_GET['rows'])){
		$limit = strip_tags(mysql_real_escape_string($_GET['rows']));
	}


	$sidx = 'name';
	if(isset($_GET['sidx']) && $_GET['sidx']){
		$sidx = strip_tags(mysql_real_escape_string($_GET['sidx']));
	}


	$sord = 0;
	if(isset($_GET['sord'])){
		$sord = strip_tags(mysql_real_escape_string($_GET['sord']));
	}

    $total_pages = 0;

    if($page > $total_pages){
        $page = $total_pages;
    }

    if($limit < 0){
        $limit = 0;
    }

    //Determin start point
    $start = ($limit * $page) - $limit;


	$totalrows = 0;
	if(isset($_GET['totalrows'])){
		$totalrows = strip_tags(mysql_real_escape_string($_GET['totalrows']));
	}

    switch($sidx){
        case 'name':
            $sidx = 'brand_projects.name';
            break;
        case 'max_sessions':
            $sidx = 'brand_projects.max_sessions';
            break;
        case 'start_date_bp':
            $sidx = 'brand_projects.start_date';
            break;
        case 'end_date_bp':
            $sidx = 'brand_projects.end_date';
            break;
    }

    $params =new stdClass();
    $params->companyId=$_SESSION['MM_CompanyId'];
    $params->sidx=$sidx;
    $params->sord=$sord;
    if ($start>=0)
        $params->start=$start;
    else
        $params->start=0;
    $params->limit=$limit;
	talkToNode("/getBrandProject",$params,$brand_projects);

	//Get brand project information
	$bp_rows = json_decode($brand_projects,true);
	$bp_count = 0;
	if($bp_rows){
		$bp_count = sizeof($bp_rows);
	}

	//Make sure that the response information uses correct information
	$total_pages = 0;
	if($bp_count > 0 && $bp_count && $limit){
		$total_pages = ceil($bp_count / $limit);
	}

	if($page > $total_pages){
		$page = $total_pages;
	}

	if($limit < 0){
		$limit = 0;
	}

	//Determine start point
	$start = ($limit * $page) - $limit;

	switch($sidx){
		case 'name':
			$sidx = 'brand_projects.name';
		break;
		case 'max_sessions':
			$sidx = 'brand_projects.max_sessions';
		break;
		case 'start_date_bp':
			$sidx = 'brand_projects.start_date';
		break;
		case 'end_date_bp':
			$sidx = 'brand_projects.end_date';
		break;
	}

	if($sidx == 'configure' || $sidx == 'delete'){
		$sidx = NULL;
	}

    if($start<0)
        $start=0;

	$properties = array();
	if($sidx && $sord){
		$params->sidx=$sidx;
        $params->dord=$sord;
	}
	if($start && $limit){
        $params->start=$start;
        $params->limit=$limit;
	}

	//Use the correct query according to user type
	$brand_project_rows = null;
	if($user_type === -1 || $user_type == 1){ //Admin or Global Admin
        talkToNode("/getBrandProject",$params,$brand_project_rows); //NB! it's not really rows, it's JSON. (Legacy name)
	} else { //Facilitators or Observers
        talkToNode("/getBrandProjectBySession",$params,$brand_project_rows);
	}



	//Prepare response to send back to Grid plugin
	$response = new StdClass;
	$response->page = $page;
	$response->total = $total_pages;
	$response->records = $bp_count;
	$response->rows = array();
	$i = 0;


$bp_foreach=json_decode($brand_project_rows,true);
	if($bp_foreach){


		if(!empty($bp_foreach)){
			foreach($bp_foreach as $row){
				$example = "<a title='Configure Brand Project' href='newSession.php?brand_project_id=";
                $example=$example. $row['brand_project_id'];
                $example=$example."'><span class='ui-icon ui-icon-gear'></span></a>";
		    $reportLink = "<a title='Panel Analysis' href='participantPanel-reports.php?brand_project_id=" . $row['brand_project_id'] . "'><span class='ui-icon ui-icon-note'></span></a>";
		    $delete_url = '<a title="Delete Brand Project" href="BrandProjectDelete.php?brand_project_id=' . $row['brand_project_id'] . '&client_company_id=' . $row['client_company_id']. '&panel=1"><span class="ui-icon delete"></span></a>';

		    $response->rows[$i]['brand_project_id']   = $row['brand_project_id'];
		    $response->rows[$i]['cell'] = array(
		    	stripslashes($row['companyName']),
		    	stripslashes($row['name']),
		    	$row['max_sessions'],
		    	date('d-m-Y', strtotime($row['start_date'])),
		    	date('d-m-Y', strtotime($row['end_date'])),
		    	($user_type == 3 || $user_type == 1 || $user_type == -1 ? $example : ''),
		    	($user_type == 1 || $user_type == -1 ? $reportLink : ''),
		    	($user_type == -1 ? $delete_url : 'N/A')
		    );
		    $i++;
			}
		}
	}

	echo json_encode($response);







