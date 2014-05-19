<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('getCounts.php');

 	/* Set user type */
	$user_type = NULL;
	if($_SESSION['MM_UserTypeId']){
		$user_type = $_SESSION['MM_UserTypeId'];
	}
 
 	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 2)){
		if(isset($_GET['brand_project_id'])){
			$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
		}
			
		//Page properties
		$main_script = 'panel_reports';
		$other_content = 'panel_reports';
		$grid = false;
		$validate = false;
		$inline_scripting = 'panel_reports_inline';
		$page_help = 'brand_projects';
		
		//Only initialisation - if client company id is available, it is set further down
		$page = 'Brand Projects | Participant Panel Analysis';
		$title = 'New Brand Project';
		
		$sub_id = null;
		$sub_group = null;
		$sub_navigation = null;

		/* Detect if should use client company name */
		$subtitle_found = false;

		/* Get client company id */
		$client_company_id = NULL;

		/* Get client company name */
		$client_company_name = 'New Company';

		//retrieve the bp info
		mysql_select_db($database_ifs, $ifs);
		$query_retBPInfo = "
		SELECT 
		  brand_projects.max_sessions,
		  brand_projects.name,
		  brand_projects.id,
		  brand_projects.end_date,
		  brand_projects.start_date,
		  brand_projects.client_company_id,
		  brand_projects.logo_url,
		  client_companies.name AS `client_company_name`
		FROM
		  brand_projects
		  INNER JOIN client_companies ON(brand_projects.client_company_id = client_companies.id)
		WHERE
		  brand_projects.id=$brand_project_id  
		";

		$retBPInfo = mysql_query($query_retBPInfo, $ifs) or die(mysql_error());
		$row_retBPInfo = array();
		$totalRows_retBPInfo = 0;
		$brand_project_logo_url = '';

		if($retBPInfo){
			$row_retBPInfo = mysql_fetch_assoc($retBPInfo);
			$totalRows_retBPInfo = mysql_num_rows($retBPInfo);

			$title = $row_retBPInfo['name'];
			$client_company_id = $row_retBPInfo['client_company_id'];
			$client_company_name = $row_retBPInfo['client_company_name'];
			$brand_project_logo_url = $row_retBPInfo['logo_url'];

			/* Should use the client company subtitle */
			if($client_company_id && $brand_project_id){
	 			$subtitle_found = true;
	 		}
		}

		//The sub navigation for the content
		if($brand_project_id){
			$sub_group = 'brand_projects';
			$sub_navigation = array(
				'Sessions' => 'newSession.php?brand_project_id=' . $brand_project_id,
				'Participant Panel' => 'participantPanel.php?brand_project_id=' . $brand_project_id								
			);

			if($user_type < 2){
				$sub_navigation['Panel Analysis'] = 'participantPanel-reports.php?brand_project_id=' . $brand_project_id;
			}

			//Add Observer List
			$sub_navigation['Observer List'] = 'bp_observers.php?brand_project_id=' . $brand_project_id;

			$sub_id = $brand_project_id;
		}

		//Get participants
		//retrieve the brand project's gender details 
		mysql_select_db($database_ifs, $ifs);

		$query_retTotalBPParticipants = "
		SELECT 
		COUNT(users.id) AS TotalParticipants
		FROM
		  participants
		  INNER JOIN users ON (participants.user_id = users.id)
		WHERE
			participants.brand_project_id=$brand_project_id
		";

		$retTotalBPParticipants = mysql_query($query_retTotalBPParticipants, $ifs) or die(mysql_error());
		
		$totalRows_retTotalBPParticipants = 0;
		$totalParticipants = 0;
		$row_retTotalBPParticipants = array();

		$male_participants = 0;
		$female_participants = 0;
		$other_participants = 0;

		//If query is successful
		if($retTotalBPParticipants){
			$row_retTotalBPParticipants = mysql_fetch_assoc($retTotalBPParticipants);
			$totalRows_retTotalBPParticipants = mysql_num_rows($retTotalBPParticipants);

			$totalParticipants = $row_retTotalBPParticipants['TotalParticipants'];

			//get the total male, female, other
			$male_participants = getBPCounts($database_ifs, $ifs, $brand_project_id,'Gender','Male');		
			$female_participants = getBPCounts($database_ifs, $ifs, $brand_project_id,'Gender','Female');		
		}

		//get all the brand segments
		$query_retAllBrandSegments = "
		SELECT 
		DISTINCT participants.brand_segment
		FROM
		participants
		WHERE
			participants.brand_project_id=$brand_project_id
		";
		
		$retAllBrandSegments = mysql_query($query_retAllBrandSegments, $ifs) or die(mysql_error());
		$totalRows_retAllBrandSegments = 0;

		if($retAllBrandSegments){
			$totalRows_retAllBrandSegments = mysql_num_rows($retAllBrandSegments);
		}

		//Encode the brand segment in a format that JS works
		$segment_participants = array();
		$brand_segment_json = json_encode(array());
		$segment_participants_json = json_encode(array());
	
		if($totalRows_retAllBrandSegments){
			while($row_retAllBrandSegments = mysql_fetch_assoc($retAllBrandSegments)){
				$brand_segment[]=$row_retAllBrandSegments['brand_segment'];
				
				//populate the array for brand segment participants
				$segment_participants[]=getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.brand_segment', $row_retAllBrandSegments['brand_segment']);
			}
			
			if(!empty($brand_segment)){
				$brand_segment_json = json_encode($brand_segment);
			}

			if(!empty($segment_participants)){
				$segment_participants_json = json_encode($segment_participants);
			}
		}

		//get all the states
		$query_retAllStates = "
		SELECT DISTINCT 
		  addresses.suburb, addresses.state, addresses.country_id, cl.country_name
		FROM
		  addresses
		  INNER JOIN users ON (addresses.id = users.address_id)
		  LEFT JOIN (SELECT id, country_name FROM country_lookup) AS `cl` ON(addresses.country_id = cl.id)
		WHERE
		  users.id IN 
		  (
			SELECT participants.user_id  FROM  participants WHERE participants.brand_project_id=$brand_project_id
		  
		  )
		";

		$retAllStates = mysql_query($query_retAllStates, $ifs) or die(mysql_error());
		$totalRows_retAllStates = 0;

		if($retAllStates){
			$totalRows_retAllStates = mysql_num_rows($retAllStates);
		}

		$state = array();
		$suburb = array();
		$country = array();

		//Encode the state and suburb in a format js understands		
		$state_json = json_encode(array());
		$suburb_json = json_encode(array());
		$country_json = json_encode(array());

		//Encode the state and suburb participants in a format js understands
		$state_participants_json = json_encode(array());
		$suburb_participants_json = json_encode(array());
		$country_participants_json = json_encode(array());
	
		/* If the query is successful */
		if($totalRows_retAllStates){
			while($row_retAllStates = mysql_fetch_assoc($retAllStates)){
				$state[] = $row_retAllStates['state'];
				$suburb[] = $row_retAllStates['suburb'];
				$country[] = $row_retAllStates['country_name'];
				
				//populate the array for state participants
				$state_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'addresses.state', $row_retAllStates['state']);

				//populate the array for suburb participants
				$suburb_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'addresses.suburb', $row_retAllStates['suburb']);

				//populate the array for country participants
				$country_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'addresses.country_id', $row_retAllStates['country_id']);
			}			

			//Encode state array to JSON
			if(!empty($state)){
				$state_json = json_encode($state);
			}			
			if(!empty($state_participants)){
				$state_participants_json = json_encode($state_participants);
			}

			//Encode suburb array to JSON
			if(!empty($suburb)){
				$suburb_json = json_encode($suburb);
			}			
			if(!empty($suburb_participants)){
				$suburb_participants_json = json_encode($suburb_participants);
			}

			//Encode country array to JSON
			if(!empty($country)){
				$country_json = json_encode($country);
			}			
			if(!empty($country_participants)){
				$country_participants_json = json_encode($country_participants);
			}
		}

		//Get all the occupations, ethnicity, and optional variables
		$query_retAllOccupations = "
		SELECT DISTINCT 
			participants.occupation, participants.ethnicity, participants.optional1,
			participants.optional2, participants.optional3, participants.optional4,
			participants.optional5
		FROM participants 
			WHERE participants.brand_project_id = $brand_project_id
		";
		$retAllOccupations = mysql_query($query_retAllOccupations, $ifs) or die(mysql_error());
		$totalRows_retAllOccupations = 0;

		if($retAllOccupations){
			$totalRows_retAllOccupations = mysql_num_rows($retAllOccupations);
		}

		//Set the json for occupations
		$occupations_json = json_encode(array());
		$occupation_participants_json = json_encode(array());

		//Set the json for optional variables
		$optional01 = array();
		$optional01_json = json_encode(array());
		$optional01_participants_json = json_encode(array());

		$optional02 = array();
		$optional02_json = json_encode(array());
		$optional02_participants_json = json_encode(array());

		$optional03 = array();
		$optional03_json = json_encode(array());
		$optional03_participants_json = json_encode(array());

		$optional04 = array();
		$optional04_json = json_encode(array());
		$optional04_participants_json = json_encode(array());

		$optional05 = array();
		$optional05_json = json_encode(array());
		$optional05_participants_json = json_encode(array());

		//Set the json for ethnicity
		$ethnicity = array();
		$ethnicity_json = json_encode(array());
		$ethnicity_participants_json = json_encode(array());

		//Set the json for optional variables
		$optional1 =

		//to get the steps for bubble charts
		// for x we keep incrementing 20 till it reaches 100 then we reset x and increment y by 10
		$x = 0;
		$y = 5;
		$i = 0;

		$bubble_array = array();
		$bubble_array_json = json_encode(array());

		$x_array = array();
		$y_array = array();
	
		//If there are any results
		if($totalRows_retAllOccupations){
			while($row_retAllOccupations = mysql_fetch_assoc($retAllOccupations)){
				$occupations[]=$row_retAllOccupations['occupation'];
				$ethnicity[] = $row_retAllOccupations['ethnicity'];

				$optional01[] = $row_retAllOccupations['optional1'];
				$optional02[] = $row_retAllOccupations['optional2'];
				$optional03[] = $row_retAllOccupations['optional3'];
				$optional04[] = $row_retAllOccupations['optional4'];
				$optional05[] = $row_retAllOccupations['optional5'];
				
				//populate the array for state participants
				$occupation_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.occupation', $row_retAllOccupations['occupation']);
				$ethnicity_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.ethnicity', $row_retAllOccupations['ethnicity']);

				//Set optional participants variables
				$optional01_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.optional1', $row_retAllOccupations['optional1']);
				$optional02_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.optional2', $row_retAllOccupations['optional2']);
				$optional03_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.optional3', $row_retAllOccupations['optional3']);
				$optional04_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.optional4', $row_retAllOccupations['optional4']);
				$optional05_participants[] = getBPCounts($database_ifs, $ifs, $brand_project_id,'participants.optional5', $row_retAllOccupations['optional5']);
			}

			//Ethnicity
			if(!empty($ethnicity)){
				$ethnicity_json = json_encode($ethnicity);
			}			
			if(!empty($ethnicity_participants)){
				$ethnicity_participants_json = json_encode($ethnicity_participants);
			}
			
			if(!empty($occupations)){
				$occupations_json = json_encode($occupations);
			}			
			if(!empty($occupation_participants)){
				$occupation_participants_json = json_encode($occupation_participants);
			}

			/* Optional01 */
			if(!empty($optional01)){
				$optional01_json = json_encode($optional01);
			}			
			if(!empty($optional01_participants)){
				$optional01_participants_json = json_encode($optional01_participants);
			}

			/* Optional02 */
			if(!empty($optional02)){
				$optional02_json = json_encode($optional02);
			}			
			if(!empty($optional02_participants)){
				$optional02_participants_json = json_encode($optional02_participants);
			}

			/* Optional03 */
			if(!empty($optional03)){
				$optional03_json = json_encode($optional03);
			}			
			if(!empty($occupation_participants)){
				$optional03_participants_json = json_encode($optional03_participants);
			}

			/* Optional04 */
			if(!empty($optional04)){
				$optional04_json = json_encode($optional04);
			}			
			if(!empty($optional04_participants)){
				$optional04_participants_json = json_encode($optional04_participants);
			}

			/* Optional05 */
			if(!empty($optional05)){
				$optional05_json = json_encode($optional05);
			}			
			if(!empty($optional05_participants)){
				$optional05_participants_json = json_encode($optional05_participants);
			}

			mysql_data_seek($retAllOccupations, 0);			

			while($row_retAllOccupations = mysql_fetch_assoc($retAllOccupations)){
				$x = $x + 20;
				$y = $y + 5;

				if($x > 100){
					$x = 20;
					$y = $y + 10;	
				}
				
				$x_array[$i] = $x;
				$y_array[$i] = $y;
				
				
				//make the bubble chart array
				//1st element is x axis
				//2nd element is y axis
				//3rd element is no of participants(radius)
				//4th is the label i.e ethnicity in this case
				$bubble_array[$i] = array($x_array[$i], $y_array[$i], $ethnicity_participants[$i], $ethnicity[$i]);

				$i++;		
			}

			//set json for bubble array
			if(!empty($bubble_array)){
				$bubble_array_json = json_encode($bubble_array);
			}	
		}			

		require_once('views/home.php');
	} else {
		if(!$user_type){
			$_SESSION['notification'] = 'You are logged out, please login again.';
		} else {
			$_SESSION['notification'] = 'You are not allowed to access this page. Please contact the administrator.';
		}
	
		if(!admin($database_ifs, $ifs)){
			$_SESSION['current_location'] = $form_action;
		}

		mysql_close($ifs);

		header('Location: index.php');
		die();
	}

	mysql_close($ifs);
