<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');

	/* Set user type */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'] )){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	$filename=basename($_SERVER['PHP_SELF']);

	if(isset($_GET['brand_project_id']) && (admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3))){
		$old_brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
		$client_company_id = strip_tags(mysql_real_escape_string($_GET['client_company_id']));
		
		//retrieve the brand project's details 
		mysql_select_db($database_ifs, $ifs);
		$query_retBPInfo = "
		SELECT 
		  *
		FROM
		  brand_projects
		WHERE
			brand_projects.id=$old_brand_project_id	
		";
		$retBPInfo = mysql_query($query_retBPInfo, $ifs) or die(mysql_error());
		
		$row_retBPInfo = array();
		$totalRows_retBPInfo = 0;

		//If the query was available
		if($retBPInfo){
			$row_retBPInfo = mysql_fetch_assoc($retBPInfo);
			$totalRows_retBPInfo = mysql_num_rows($retBPInfo);
		}

		if($totalRows_retBPInfo){
			//get the variables to duplicate BP entry
			$name = "Copy Of ". mysql_escape_string($row_retBPInfo['name']);
			$max_sessions = $row_retBPInfo['max_sessions'];
			$start_date = date('Y-m-d', strtotime($row_retBPInfo['start_date']));
			$end_date = date('Y-m-d', strtotime($row_retBPInfo['end_date']));
			$session_replay_date = $row_retBPInfo['session_replay_date'];	
			$created = date('Y-m-d H:i:s'); 
			
			
			//insert into brand projects
			$insert3SQL = sprintf("INSERT INTO brand_projects (name, client_company_id, max_sessions, start_date, end_date,session_replay_date, created) VALUES ('$name', $client_company_id, $max_sessions,'$start_date','$end_date','$session_replay_date','$created')");
			
			
			mysql_select_db($database_ifs, $ifs);
			$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
			
			$new_brand_project_id = mysql_insert_id($ifs);	
		
			//retrieve the sessions for the old brand_project_id
			mysql_select_db($database_ifs, $ifs);
			$query_retSession = "
			SELECT 
			  sessions.*
			FROM
			  sessions
			  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
			WHERE
			  brand_projects.id=$old_brand_project_id   
			";
			$retSession = mysql_query($query_retSession, $ifs) or die(mysql_error());

			$totalRows_retSession = 0;

			if($retSession){
				$totalRows_retSession = mysql_num_rows($retSession);

				//we will loop through all the sessions of a BP
				while($row_retSession = mysql_fetch_assoc($retSession)){
					$old_session_id = $row_retSession['id'];
					
					//retrieve the session staff
					mysql_select_db($database_ifs, $ifs);
					$query_retSessionStaff = "
					SELECT 
					  session_staff.*
					FROM
					  session_staff
					  INNER JOIN users ON (session_staff.user_id = users.id)
					 WHERE
					   session_staff.session_id=".$old_session_id." 
					";
					$retSessionStaff = mysql_query($query_retSessionStaff, $ifs) or die(mysql_error());

					$totalRows_retSessionStaff = 0;

					if($retSessionStaff){
						$totalRows_retSessionStaff = mysql_num_rows($retSessionStaff);
					}
					
					if($totalRows_retSessionStaff){
						//get the variables to copy the session entry
						$name="Copy Of ". mysql_escape_string($row_retSession['name']);
						$start_time=date('Y-m-d H:i:s', strtotime($row_retSession['start_time']));
						$end_time=date('Y-m-d H:i:s', strtotime($row_retSession['end_time']));
						
						$created=date('Y-m-d H:i:s'); 
						
						//insert into sessions
						$insert3SQL = sprintf("INSERT INTO sessions (name, brand_project_id,start_time, end_time, created) VALUES ('$name', $new_brand_project_id,'$start_time', '$end_time','$created')");
						mysql_select_db($database_ifs, $ifs);
						$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
						
						$new_session_id = mysql_insert_id($ifs);				
					
						//now loop the session staff to copy them over.
						while($row_retSessionStaff = mysql_fetch_assoc($retSessionStaff))
						{
							
							$user_id=$row_retSessionStaff['user_id'];
							$type_id=$row_retSessionStaff['type_id'];
							$created=date('Y-m-d H:i:s');
							$comments=$row_retSessionStaff['comments'];
							
							//insert the staff for the new session id
							
							$insert3SQL = sprintf("INSERT INTO session_staff (user_id, session_id, type_id, comments,created) VALUES ($user_id, $new_session_id, $type_id, '$comments','$created')");
							mysql_select_db($database_ifs, $ifs);
							$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
								
						
						}
						
						mysql_data_seek($retSessionStaff, 0);	
						
						//get all the topics for old session
						mysql_select_db($database_ifs, $ifs);
						$query_retTopic = "
						SELECT 
						  topics.*
						FROM
						  topics
						WHERE
						   topics.session_id=$old_session_id
						";
						$retTopic = mysql_query($query_retTopic, $ifs) or die(mysql_error());

						$totalRows_retTopic = 0;

						if($retTopic){
							$totalRows_retTopic = mysql_num_rows($retTopic);
						}
						
						if($totalRows_retTopic){
							//now loop thetopics to copy them over to the new session id
							while($row_retTopic = mysql_fetch_assoc($retTopic))
							{
								$topic_name = htmlentities(mysql_escape_string($row_retTopic['name']));

								$created=date('Y-m-d H:i:s'); 
								
								//insert the new topics
								$insertSQL = sprintf("INSERT INTO topics (session_id, name,created) VALUES ($new_session_id,'$topic_name', '".$created."')");
								mysql_select_db($database_ifs, $ifs);
								$Result1 = mysql_query($insertSQL, $ifs) or die(mysql_error());

							}
							mysql_data_seek($retTopic, 0);
						}
					}
				}
			}	
		}

		mysql_close($ifs);	
		
		//redirect back to the brand project page from registration
		$updateGoTo = "newBrandProject.php?client_company_id=".$client_company_id;
		header(sprintf("Location: %s", $updateGoTo));	

		die();
	} else {
		$_SESSION['notification'] = 'You are logged out, please login again.';
	
		if(!admin($database_ifs, $ifs)){
			$_SESSION['current_location'] = $form_action;
		}

		mysql_close($ifs);

		header("Location: index.php");
		die();
	}

	mysql_close($ifs);