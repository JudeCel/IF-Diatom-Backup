<?php 
require_once('Connections/ifs.php');     
require_once('core.php');
require_once('models/users_model.php');
require_once('models/array_repository_model.php');

$all = FALSE;

$participant_id = NULL;
if(isset($_GET['participant_id']))
{
	$participant_id_raw = strip_tags(mysql_real_escape_string($_GET['participant_id']));	
	
	$participant_id = explode(",",$participant_id_raw);
}

$brand_project_id = NULL;
if(isset($_GET['brand_project_id'])){
	$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));	
}

if(!$brand_project_id && !$participant_id){
	$updateGoTo = 'index.php';

	mysql_close($ifs);

	header(sprintf("Location: %s", $updateGoTo));
	die();
} elseif($brand_project_id && !$participant_id){
	$all = $brand_project_id;
}

//set the goto url now that you know the brand project is available
$updateGoTo = "participantPanel.php?brand_project_id=" . $brand_project_id;
	
//loop through the participants array
if(count($participant_id) || $all){
	
	$participants = find_participant($database_ifs, $ifs, $participant_id, true, false, NULL, $all); //get partcicipants
	
	if($participants && !is_string($participants)){
		// send response headers to the browser
	  header( 'Content-Type: text/csv' );
	  header( 'Content-Disposition: attachment;filename=export_users.csv');
	  $fp = fopen('php://output', 'w');

	  //Set headers names and equivalent db columns
	  $headers_columns = array(
	  	'First Name' => 'name_first',
	  	'Last Name' => 'name_last',
			'Gender' => 'Gender', 
			'Email' => 'email',
			'Mobile' => 'mobile',
			'Phone' => 'phone',
			'Fax' => 'fax', 
			'Street' => 'street',
			'Suburb' => 'suburb', 
			'State' => 'state',			
			'Postcode' => 'post_code',
			'Country' => 'country_name',
			'Invite Again' => 'invite_again', 
			'Age Value' => 'dob', 
			'Ethnicity' => 'ethnicity', 
			'Occupation' => 'occupation',
			'Brand Segment' => 'brand_segment',			
			'Optional 1' => 'optional1',
			'Optional 2' => 'optional2',
			'Optional 3' => 'optional3',
			'Optional 4' => 'optional4',
			'Optional 5' => 'optional5'
	  );

		$headers = array_keys($headers_columns); //get only the keys

	  /* Output header */
	  fputcsv($fp, $headers);

	  $part_foreach = prepare_foreach($participants);
	  
	  foreach($part_foreach as $row){
	  	$row_values = array();

	  	foreach($headers_columns as $header_label=>$col_req){
	  		$final_value = '';

	  		if(is_array($col_req)){ //if it requires multiple columns to form the value
	  			foreach($col_req as $col){
	  				//Check if the column is available in the db row
	  				if(isset($row[$col])){
	  					//Add to final value or initialise it
	  					if(!$final_value){
	  						$final_value = $row[$col];
	  					} else {
	  						$final_value .= ' ' . $row[$col];
	  					}
	  				}
	  			}
	  		} else {
  				//Check if the column is available in the db row
  				if(isset($row[$col_req])){
  					$final_value = $row[$col_req]; //set final value
  				}
  			}

  			$row_values[] = $final_value;
	  	}

	  	fputcsv($fp, $row_values);
	  }

	  fclose($fp); //close file  
	} else {		
		mysql_close($ifs);

		header(sprintf("Location: %s", $updateGoTo));
		die();
	}
}

mysql_close($ifs);