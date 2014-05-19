<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/users_model.php');

	/* Set user type */
	$user_type = NULL;
	if($_SESSION['MM_UserTypeId']){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	$message = '';
	$message_val = new stdClass;
	$message_val->other = array();

	if(admin($database_ifs, $ifs) && ($user_type >= -1 && $user_type <= 3)){
		if(isset($_GET['brand_project_id'])){
			$brand_project_id =	strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
		} else {
			$brand_project_id=-1;
		}

		$participant_id = null;
		if(isset($_GET['participant_id'])){
			$participant_id = strip_tags(mysql_real_escape_string($_GET['participant_id']));
		}

		$js = false;
		if(isset($_GET['js'])){
			$js = TRUE;
		}

		//Page properties
		$page = 'Participant Details';

		/* If a participant is being edited */
		if($participant_id){
			$page = 'Edit ' . $page;
		}

		$title = $page;
		$main_script = 'participant_edit';
		$other_content = 'participant_edit';
		$validate = true;
		$inline_scripting = null;

		$CompanySuburbId=-1;

		$update_message = null; //if update_message is shown		

		/* Get message information */
		if(isset($_SESSION['notification'])){
			$message_val->other = $_SESSION['notification'];
			
			$message = process_messages($message_val);

			unset($_SESSION['notification']);
		}

		$fields = array();

		//retrieve the various postal suburbs 
		mysql_select_db($database_ifs, $ifs);
		$query_retState = "SELECT DISTINCT State FROM post_code_suburb_lookup ORDER BY Suburb ASC";
		$retState = mysql_query($query_retState, $ifs) or die(mysql_error());
		//$row_retState = mysql_fetch_assoc($retState);
		$totalRows_retState = mysql_num_rows($retState);


		$row_retParticipantDetails = array();
		$totalRows_retParticipantDetails = 0;
		$dob = '';

		if($participant_id){
			//retrieve the participant's details
			mysql_select_db($database_ifs, $ifs);
			$query_retParticipantDetails = "
			SELECT 
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
			  users.address_id,
			  addresses.street,
			  addresses.post_code,
			  addresses.suburb,
			  addresses.state,
			  addresses.country_id,
			  participants.dob,
			  participants.ethnicity,
			  participants.occupation,
			  participants.brand_segment,
			  participants.id,
			  participants.brand_project_id,
			  participants.user_id,
			  country_lookup.country_name,
			  participants.optional2,
			  participants.optional1,
			  participants.optional3,
			  participants.optional4,
			  participants.optional5,
			  p_list.comments,
			  p_list.pid,
			  p_list.pl_id
			FROM
			  client_users
			  INNER JOIN client_companies ON (client_users.client_company_id = client_companies.id)
			  INNER JOIN brand_projects ON (client_users.client_company_id = brand_projects.client_company_id)
			  INNER JOIN participants ON (brand_projects.id = participants.brand_project_id)
			  INNER JOIN users ON (participants.user_id = users.id)
			  INNER JOIN addresses ON (users.address_id = addresses.id)
			  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id)
			  LEFT JOIN (SELECT id AS `pl_id`, participant_id AS `pid`, participant_rating_id, comments FROM participant_lists) AS p_list ON (participants.id = p_list.pid)
			WHERE 
				participants.id=$participant_id
			GROUP BY
				participants.id
			";

			$retParticipantDetails = mysql_query($query_retParticipantDetails, $ifs) or die(mysql_error());	

			if($retParticipantDetails){
				$row_retParticipantDetails = mysql_fetch_assoc($retParticipantDetails);
				$totalRows_retParticipantDetails = mysql_num_rows($retParticipantDetails);

				//Set up date of birth
				if(isset($row_retParticipantDetails['dob']) && $row_retParticipantDetails['dob']){
					$dob = $row_retParticipantDetails['dob']; //date of birth
				}
			}
		}

		/* Default Values */

		$pl_id = null;
		if(isset($row_retParticipantDetails['pl_id'])){
			$pl_id = $row_retParticipantDetails['pl_id'];
		}

		//select the id of Australia to default on
		$country_id = 1;

		$name_first = '';
		$name_last = '';
		$gender = '';
		$email = '';
		$mobile = '';
		$phone = '';
		$fax = '';
		$street = '';
		$state = '';
		$suburb = '';
		$postcode = '';
		$ethnicity = '';
		$occupation = '';
		$brand_segment = '';
		$optional1 = '';
		$optional2 = '';
		$optional3 = '';
		$optional4 = '';
		$optional5 = '';
		$comments = '';
        $uses_landline =null;

		//do the inserts
		if(isset($_POST['btnSubmit']))
		{
			$update_success = TRUE; //Update was successful
			$fields = array();
			
			if(isset($_POST['country'])){
				$country_id = strip_tags(mysql_real_escape_string($_POST['country'])); //clean post value
			}

			$created = date('Y-m-d H:i:s');	//created date

			//Date of birth
			if(isset($_POST['dob']) && $_POST['dob']){
				$dob = strip_tags(mysql_real_escape_string($_POST['dob'])); //clean post value
			}			

			//First Name
			if(isset($_POST['name_first'])){
				$name_first = strip_tags(mysql_real_escape_string($_POST['name_first']));
			}

			//Last Name
			if(isset($_POST['name_last'])){
				$name_last = strip_tags(mysql_real_escape_string($_POST['name_last']));
			}

			//Gender
			if(isset($_POST['gender'])){
				$gender = strip_tags(mysql_real_escape_string($_POST['gender']));
			}

			//Email
			if(isset($_POST['email'])){
				$email = htmlentities(mysql_real_escape_string($_POST['email']));
			}

			//Mobile
			if(isset($_POST['mobile']) && $_POST['mobile']){
				$mobile = strip_tags(mysql_real_escape_string($_POST['mobile']));

			}

			//Phone
			if(isset($_POST['phone']) && $_POST['phone']){
				$phone = strip_tags(mysql_real_escape_string($_POST['phone']));

				$phone_count = strlen($phone);
			}

			//Fax
			if(isset($_POST['fax']) && $_POST['fax']){
				$fax = strip_tags(mysql_real_escape_string($_POST['fax']));

			}

			//Street		
			if(isset($_POST['street'])){
				$street = strip_tags(mysql_real_escape_string($_POST['street']));
			}

			//State		
			if(isset($_POST['state'])){
				$state = strip_tags(mysql_real_escape_string($_POST['state']));
			}

			//Suburb		
			if(isset($_POST['suburb'])){
				$suburb = strip_tags(mysql_real_escape_string($_POST['suburb']));
			}

			//Postcode		
			if(isset($_POST['postcode'])){
				$postcode = strip_tags(mysql_real_escape_string($_POST['postcode']));
			}
			
			//Ethnicity		
			if(isset($_POST['ethnicity'])){
				$ethnicity = strip_tags(mysql_real_escape_string($_POST['ethnicity']));
			}

			//Occupation		
			if(isset($_POST['occupation'])){
				$occupation = strip_tags(mysql_real_escape_string($_POST['occupation']));
			}

			//Brand Segment		
			if(isset($_POST['brand_segment'])){
				$brand_segment = strip_tags(mysql_real_escape_string($_POST['brand_segment']));
			}			
			
			//Optional 1		
			if(isset($_POST['optional1'])){
				$optional1 = htmlentities(mysql_real_escape_string($_POST['optional1']));
			}

			//Optional 2		
			if(isset($_POST['optional2'])){
				$optional2 = htmlentities(mysql_real_escape_string($_POST['optional2']));
			}

			//Optional 3		
			if(isset($_POST['optional3'])){
				$optional3 = htmlentities(mysql_real_escape_string($_POST['optional3']));
			}

			//Optional 4
			$optional4 = '';
			if(isset($_POST['optional4'])){
				$optional4 = htmlentities(mysql_real_escape_string($_POST['optional4']));
			}

			//Optional 5		
			if(isset($_POST['optional5'])){
				$optional5 = htmlentities(mysql_real_escape_string($_POST['optional5']));
			}

			//Comments		
			if(isset($_POST['comments'])){
				$comments = htmlentities(mysql_real_escape_string($_POST['comments']));
			}

            if(isset($_POST['uses_landline'])){
                $uses_landline = htmlentities(mysql_real_escape_string('uses_landline'));
            }

			//Set that the db will need to do an update instead of an insert
			if($update_success){
				$update = TRUE;
				if(!$participant_id){
					$update = FALSE;
				}
			  
				if(!$update){
					  //Update user profile
					  $user_id = update_user_profile($database_ifs, $ifs, null);		  
				} else {			

						//update the participant's entry
						$participant_user_id=$row_retParticipantDetails['user_id'];
						$participant_address_id=$row_retParticipantDetails['address_id'];
						$brand_project_id=$row_retParticipantDetails['brand_project_id'];

						//Update user profile
					  $user_id = update_user_profile($database_ifs, $ifs, $participant_user_id);				
				}

				if(is_array($user_id)){
					$update_message = true;

					//Set error messages
					$message = $user_id['html'];
					$fields = $user_id['fields'];
				} else {
					//Insert addresses and participants
					if(!$update){
						// insert into address
					  $insert2SQL = sprintf("INSERT INTO addresses(street, suburb, state, post_code, country_id, created) VALUES ('$street', '$suburb','$state','$postcode','$country_id','$created')");
					  mysql_select_db($database_ifs, $ifs);
					  $Result2 = mysql_query($insert2SQL, $ifs) or die(mysql_error());
					  $address_id = mysql_insert_id($ifs);

					  update_user_address_id($database_ifs, $ifs, $user_id, $address_id);		  	  
					  
					  // insert into participant
					  $insert4SQL = sprintf("INSERT INTO participants( user_id, brand_project_id, dob, ethnicity, occupation, brand_segment, optional1,optional2,optional3,optional4,optional5,created) VALUES ($user_id, $brand_project_id, '$dob', '$ethnicity', '$occupation', '$brand_segment', '$optional1','$optional2','$optional3','$optional4','$optional5','$created')");
					  mysql_select_db($database_ifs, $ifs);
					  $Result4 = mysql_query($insert4SQL, $ifs) or die(mysql_error());
					  $participant_id= mysql_insert_id($ifs);
                      $_GET['participant_id']=$participant_id;

					  $message_val->other[] = 'A participant has been created.';
                      $other_content = null;


					} else {
						if($participant_user_id && $participant_address_id){
						  // update into address
						  $insert2SQL = sprintf("UPDATE  addresses SET street='$street', suburb='$suburb', state='$state', post_code='$postcode', country_id=$country_id, updated= '$created' WHERE addresses.id=$participant_address_id");
						  mysql_select_db($database_ifs, $ifs);
						  $Result2 = mysql_query($insert2SQL, $ifs) or die(mysql_error());

						  $address_result = update_user_address_id($database_ifs, $ifs, $participant_user_id, $participant_address_id);
						}			  	
						
						 // update into participants
						$insert2SQL = sprintf("UPDATE  participants SET dob='$dob', ethnicity='$ethnicity', occupation='$occupation', brand_segment='$brand_segment', optional1='$optional1',optional2='$optional2',optional3='$optional3',optional4='$optional4',optional5='$optional5', updated= '$created' WHERE participants.id=$participant_id");
						mysql_select_db($database_ifs, $ifs);
						$Result2 = mysql_query($insert2SQL, $ifs) or die(mysql_error());
						$address_id = mysql_insert_id($ifs);


					  	if($pl_id && $comments){
						  	$pl_update = sprintf("UPDATE participant_lists SET comments='$comments' WHERE id=%d", $pl_id);
						  	mysql_select_db($database_ifs, $ifs);
						  	$pl_result = mysql_query($pl_update, $ifs);
					  	}

				  		$message_val->other[] = 'The participant has been updated.';
					}
				}
			} else {
				$update_message = TRUE;
			}

			if(!$update_message && $js){
				$_SESSION['notification'] = array();
				$_SESSION['notification'][] = process_messages($message_val);

				//close fancybox

			} elseif(!$message) {
				$message = process_messages($message_val);
			}
            echo '<script type="text/javascript" src="js/fancybox_close.js" />';
		}

		//If gender is not set via POST
		if(!isset($_POST['gender']) && isset($row_retParticipantDetails['Gender'])){
			$gender = $row_retParticipantDetails['Gender'];
		}

        if(!isset($_POST['uses_landline']) && isset($row_retParticipantDetails['uses_landline'])){
            $uses_landline = $row_retParticipantDetails['uses_landline'];
        }

		mysql_select_db($database_ifs, $ifs);
		$query_retCountry = "
		SELECT 
		  country_lookup.country_name,
		  country_lookup.id
		FROM
		  country_lookup
		ORDER BY
		  country_lookup.country_name
		  ";
		$retCountry = mysql_query($query_retCountry, $ifs) or die(mysql_error());
		
		$totalRows_retCountry = 0;

		if($retCountry){
			//$row_retCountry = mysql_fetch_assoc($retCountry);
			$totalRows_retCountry = mysql_num_rows($retCountry);
		}
								
		/* Select country id */
		if(isset($row_retParticipantDetails['country_id']) && $row_retParticipantDetails['country_id'] && !isset($_POST['country'])){
			$country_id = $row_retParticipantDetails['country_id'];
		} 

		mysql_close($ifs);

		require_once('views/popup.php');
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