<?php 
	require_once('Connections/ifs.php');     
	require_once('core.php');
	require_once('models/users_model.php');

	$message_val = new stdClass;

	$message_val->other = array();
	$message_val->fields = array();
	$fields = array();

	/* Get user type of user */
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	if(isset($_GET['client_company_id'])){
		$new = false;
		$client_company_id=	strip_tags(mysql_real_escape_string($_GET['client_company_id']));
	}	else {
		$new = true;
		$client_company_id = 0;
	}

	/* Ensure that the user has access to these pages */
	if($user_type != -1){
		$message_val->other[] = 'You do not have permission to the Client Companies signup page'; //set message

		$_SESSION['notification'] = $message_val->other;

		mysql_close($ifs);

		header("Location: index.php");
		die();
	}

	//If the client company matches the client company saved in the session, is IFS Admin or is editing page as Global Admin
	if($client_company_id == $_SESSION['MM_CompanyId'] || $user_type == -1){
		//Page properties
		$main_script = 'signup';
		$other_content = 'signup';
		$grid = false;
		$validate = true;
		$inline_scripting = 'signup_inline';
		$page_help = 'companies';

		//Only initialisation - if client company id is available, it is set further down
		$page = 'Signup';
		$title = 'New Company';
		$sub_navigation = null;
		$sub_id = null;
		$sub_group = null;

		$import = true;
		$import_page = 'client_company_upload_pic.php';
		$image_new = TRUE;

		//Set default results
		$row_retClientCompany = array();
		$row_retBillingAddress = array();
		$row_retPrimaryContact = array();
		$row_retBillingContact = array();

		//retrieve the various countries
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

	  //Number of results for country query
	  $totalRows_retCountry = 0;
	  if($retCountry){
	  	$totalRows_retCountry = mysql_num_rows($retCountry);
	  }

	  //Initialise start and end date
	  $start_date = date('d-m-Y', time());
	  $end_date = date('d-m-Y', time());

		//Input Values Initialisation
		$trading_street = '';
		$trading_suburb = '';
		$trading_state = '';
		$trading_post_code = '';
		$trading_country_id = '';
		$billing_street = '';
		$billing_suburb = '';
		$billing_state = '';
		$billing_post_code = '';
		$billing_country_id = '';
		$client_company_type = '';
		$name = '';
		$URL = '';
		$ABN = '';
		$number_of_brands = '';
		$max_sessions_brand = '';
		$max_number_of_observers = '';
		$self_moderated = '';
		$global_admin = '';
		$enable_chatroom_logo = '';
		$comments = '';
		$name_first_primary = '';
		$name_last_primary = '';
		$phone_primary = '';
		$mobile_primary = '';
		$email_primary = '';
		$name_first_billing = '';
		$name_last_billing = '';
		$phone_billing = '';
		$mobile_billing = '';
		$email_billing = '';

		if(!$new){
			//retrieve the company types
			mysql_select_db($database_ifs, $ifs);
			$query_retClientCompany = "
			SELECT
			  client_companies.name,
			  client_companies.address_id,
			  client_companies.URL,
			  client_companies.ABN,
			  client_companies.start_date,
			  client_companies.end_date,
			  client_companies.max_number_of_observers,
			  client_companies.number_of_brands,
			  client_companies.max_sessions_brand,
			  client_companies.self_moderated,
			  client_companies.global_admin,
			  client_companies.comments,
			  client_companies.client_company_logo_url,
			  client_companies.enable_chatroom_logo,
			  addresses.street,
			  client_companies.client_company_type,
			  addresses.country_id,
			  addresses.state,
			  addresses.suburb,
			  addresses.post_code,
			  addresses.address_type
			FROM
			  client_companies
			  INNER JOIN addresses ON (client_companies.address_id = addresses.id)
			WHERE
			  addresses.address_type = 'Trading'
			  AND client_companies.id=".$client_company_id."
			";

			//The client company results
			$retClientCompany = mysql_query($query_retClientCompany, $ifs) or die(mysql_error());
			$totalRows_retClientCompany = 0;
			$row_retClientCompany = array();

			$client_company_logo_url = null;

			//If Results are available, then find the appropriate information
			if($retClientCompany){
				$row_retClientCompany = mysql_fetch_assoc($retClientCompany);
				$totalRows_retClientCompany = mysql_num_rows($retClientCompany);
			} else {
				//Set message
				$message_val->other[] = 'The Client Company could not be found';
				$_SESSION['notification'] = $message;

				mysql_close($ifs);

				header("Location: index.php");
				die();
			}

			//If the client company name is set
			if(isset($row_retClientCompany['name'])){
				$client_company_name = $row_retClientCompany['name'];

				//Change the page properties using the client company name
				$page = 'Signup | ' . $client_company_name;
				$title = $client_company_name;
			}

			$trading_address_id = $row_retClientCompany['address_id'];

			//Set start date
			if(isset($row_retClientCompany['start_date'])){
				$start_date = date('d-m-Y', strtotime($row_retClientCompany['start_date']));
			}

			//Set end date
			if(isset($row_retClientCompany['end_date'])){
				$end_date = date('d-m-Y', strtotime($row_retClientCompany['end_date']));
			}

			//Set Client Company Logo
			if(isset($row_retClientCompany['client_company_logo_url'])){
				$client_company_logo_url = $row_retClientCompany['client_company_logo_url'];
			}

			if(isset($row_retClientCompany['enable_chatroom_logo'])){
				$enable_chatroom_logo = $row_retClientCompany['enable_chatroom_logo'];
			}

			//Set Global Admin
			if(isset($row_retClientCompany['global_admin'])){
				$global_admin = $row_retClientCompany['global_admin'];
			}

			//Set Self Moderated
			if(isset($row_retClientCompany['self_moderated'])){
				$self_moderated = $row_retClientCompany['self_moderated'];
			}

			//retrieve the company billing address
			mysql_select_db($database_ifs, $ifs);
			$query_retBillingAddress = "
			SELECT
			  addresses.*
			FROM
			  client_companies
			  INNER JOIN addresses ON (client_companies.billing_address_id = addresses.id)
			WHERE
			   client_companies.id=".$client_company_id."
			   AND addresses.address_type='Billing'
			";
			$retBillingAddress = mysql_query($query_retBillingAddress, $ifs) or die(mysql_error());
			$totalRows_retBillingAddress = 0;
			$row_retBillingAddress = array();

			//Initialise billing ids
			$billing_country_id = NULL;
			$billing_address_id = NULL;

			/* Get billing address info */
			if($retBillingAddress){
				$row_retBillingAddress = mysql_fetch_assoc($retBillingAddress);
				$totalRows_retBillingAddress = mysql_num_rows($retBillingAddress);
			}

			//Find the correct billing ids if a billing address was found
			if($totalRows_retBillingAddress > 0){
				$billing_country_id=$row_retBillingAddress['country_id'];
			  $billing_address_id=$row_retBillingAddress['id'];
			}

			//retrieve the company primary contact
			mysql_select_db($database_ifs, $ifs);
			$query_retPrimaryContact = "
			SELECT
			  client_company_contacts.*
			FROM
			  client_company_contacts
			WHERE
			  client_company_contacts.contact_type_id = 2
			  AND client_company_contacts.client_company_id=$client_company_id
			";

			//Get the results of the client company primary contact query
			$retPrimaryContact = mysql_query($query_retPrimaryContact, $ifs) or die(mysql_error());
			$totalRows_retPrimaryContact = 0;
			$row_retPrimaryContact = array();

			//If results were found, set the appropriate information
			if($retPrimaryContact){
				$row_retPrimaryContact = mysql_fetch_assoc($retPrimaryContact);
				$totalRows_retPrimaryContact = mysql_num_rows($retPrimaryContact);
			}

			//retrieve the company billing contact
			mysql_select_db($database_ifs, $ifs);
			$query_retBillingContact = "
			SELECT
			  client_company_contacts.*
			FROM
			  client_company_contacts
			WHERE
			  client_company_contacts.contact_type_id = 1
			  AND client_company_contacts.client_company_id=$client_company_id
			";

			//If results were found, set the appropriate information
			$retBillingContact = mysql_query($query_retBillingContact, $ifs) or die(mysql_error());
			$totalRows_retBillingContact = 0;
			$row_retBillingContact = array();

			//Get the results of the client company billing contact query
			if($retBillingContact){
				$row_retBillingContact = mysql_fetch_assoc($retBillingContact);
				$totalRows_retBillingContact = mysql_num_rows($retBillingContact);
			}

			if($totalRows_retClientCompany > 0){
				$country_id = $row_retClientCompany['country_id'];
				$trading_country_id = $row_retClientCompany['country_id'];
			}
		}

		//The sub navigation for the content
		if($client_company_id){
			$sub_group = 'client_company';
			$sub_navigation = array(
				'Registration' => 'signup.php?client_company_id=' . $client_company_id,
				'Global Admin' => 'clientCompanyUsers.php?client_company_id=' . $client_company_id,
				'Brand Projects' => 'newBrandProject.php?client_company_id=' . $client_company_id
			);
			$sub_id = $client_company_id;
		}

		$update_success = TRUE;
		$submitted = (isset($_POST['btnSubmit']));

		//get the trading street field
		if(isset($_POST['trading_street']) && $_POST['trading_street']){
			$trading_street = strip_tags(mysql_real_escape_string($_POST['trading_street']));
		} elseif($submitted){ //Set the field as required
			$update_success = FALSE;
			$message_val->fields['trading_street'] = 'Trading Street';
		}

		//get the trading suburb field
		if(isset($_POST['trading_suburb']) && $_POST['trading_suburb']){
			$trading_suburb = strip_tags(mysql_real_escape_string($_POST['trading_suburb']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['trading_suburb'] = 'Trading Suburb';
		}

		//get the trading state field
		if(isset($_POST['trading_state']) && $_POST['trading_state']){
			$trading_state = strip_tags(mysql_real_escape_string($_POST['trading_state']));
		}

		//get the trading post code field
		if(isset($_POST['trading_post_code']) && $_POST['trading_post_code']){
			$trading_post_code = strip_tags(mysql_real_escape_string($_POST['trading_post_code']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['trading_post_code'] = 'Trading Post Code';
		}

		//get the trading country id field
		if(isset($_POST['trading_country_id']) && $_POST['trading_country_id']){
			$trading_country_id = strip_tags(mysql_real_escape_string($_POST['trading_country_id']));
		}

		//get the billing street field
		if(isset($_POST['billing_street']) && $_POST['billing_street']){
			$billing_street = strip_tags(mysql_real_escape_string($_POST['billing_street']));
		} elseif($submitted){ //Set the field as required
			$update_success = FALSE;
			$message_val->fields['billing_street'] = 'Billing Street';
		}

		//get the billing suburb field
		if(isset($_POST['billing_suburb']) && $_POST['billing_suburb']){
			$billing_suburb = strip_tags(mysql_real_escape_string($_POST['billing_suburb']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['billing_suburb'] = 'Billing Suburb';
		}

		//get the billing state field
		if(isset($_POST['billing_state']) && $_POST['billing_state']){
			$billing_state = strip_tags(mysql_real_escape_string($_POST['billing_state']));
		}

		//get the billing post code field
		if(isset($_POST['billing_post_code']) && $_POST['billing_post_code']){
			$billing_post_code = strip_tags(mysql_real_escape_string($_POST['billing_post_code']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['billing_post_code'] = 'Billing Post Code';
		}

		//get the billing country id field
		$billing_country_id = NULL;
		if(isset($_POST['billing_country_id']) && $_POST['billing_country_id']){
			$billing_country_id = strip_tags(mysql_real_escape_string($_POST['billing_country_id']));
		}

		$created = date('Y-m-d H:i:s', time());

		//get the client company type field
		if(isset($_POST['client_company_type']) && $_POST['client_company_type']){
			$client_company_type = strip_tags(mysql_real_escape_string($_POST['client_company_type']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['client_company_type'] = 'Company Type';
		}

		//get the client company name field
		if(isset($_POST['name']) && $_POST['name']){
			$name = strip_tags(mysql_real_escape_string($_POST['name']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['name'] = 'Company Name';
		}

		//get the url field
		if(isset($_POST['URL']) && $_POST['URL']){
			$URL = mysql_real_escape_string($_POST['URL']);
		}

		$ABN = NULL;
		if(isset($_POST['ABN']) && $_POST['ABN']){
			$ABN = strip_tags(mysql_real_escape_string($_POST['ABN']));
		}

		//get the number of brands field
		if(isset($_POST['number_of_brands']) && $_POST['number_of_brands']){
			$number_of_brands = strip_tags(mysql_real_escape_string($_POST['number_of_brands']));
		}	elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['number_of_brands'] = 'Number of Brands';
		}

		//get the number of sessions field
		$max_sessions_brand = NULL;
		if(isset($_POST['max_sessions_brand']) && $_POST['max_sessions_brand']){
			$max_sessions_brand = strip_tags(mysql_real_escape_string($_POST['max_sessions_brand']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['max_sessions_brand'] = 'Max Number of Sessions';
		}

		//get the number of observers field
		if(isset($_POST['max_number_of_observers']) && $_POST['max_number_of_observers']){
			$max_number_of_observers = strip_tags(mysql_real_escape_string($_POST['max_number_of_observers']));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['max_number_of_observers'] = 'Max Number of Observers';
		}

		if(isset($_POST['self_moderated']) && $_POST['self_moderated']){
			$self_moderated = strip_tags(mysql_real_escape_string($_POST['self_moderated']));
		}

		//get the global admin field
		if(isset($_POST['global_admin']) && $_POST['global_admin']){
			$global_admin = strip_tags(mysql_real_escape_string($_POST['global_admin']));
		}

		/******Cheng get radio button value*********/
		if(isset($_POST['global_admin'])){
			if($_POST['enable_chatroom_logo']==null){
				$enable_chatroom_logo = 0;
			}else{
				$enable_chatroom_logo = $_POST['enable_chatroom_logo'];
			}
		}
		/*******End*******/

		//get the start date field
		if(isset($_POST['start_date']) && $_POST['start_date']){
			$start_date = date('Y-m-d', strtotime(strip_tags(mysql_real_escape_string($_POST['start_date']))));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['start_date'] = 'Start Date';
		}

		//get the end date field
		if(isset($_POST['end_date']) && $_POST['end_date']){
			$end_date = date('Y-m-d', strtotime(strip_tags(mysql_real_escape_string($_POST['end_date']))));
		} elseif($submitted){ //set the field as required
			$update_success = FALSE;
			$message_val->fields['end_date'] = 'End Date';
		}

		//get the end date field
		$comments = NULL;
		if(isset($_POST['comments']) && $_POST['comments']){
			$comments = htmlentities(mysql_real_escape_string($_POST['comments']));
		}

		//block for primary contact

		//get the primary first name field
		if(isset($_POST['name_first_primary']) && $_POST['name_first_primary']){
			$name_first_primary = strip_tags(mysql_real_escape_string($_POST['name_first_primary']));
		} elseif($submitted){
			$update_success = FALSE;
			$message_val->fields['name_first_primary'] = 'First Name Primary';
		}

		//get the primary last name field
		$name_last_primary = NULL;
		if(isset($_POST['name_last_primary']) && $_POST['name_last_primary']){
			$name_last_primary = strip_tags(mysql_real_escape_string($_POST['name_last_primary']));
		}

		//get the primary phone field
		$phone_primary = NULL;
		if(isset($_POST['phone_primary']) && $_POST['phone_primary']){
			$phone_primary = strip_tags(mysql_real_escape_string($_POST['phone_primary']));
		}

		//get the primary mobile field
		$mobile_primary = NULL;
		if(isset($_POST['mobile_primary']) && $_POST['mobile_primary']){
			$mobile_primary = strip_tags(mysql_real_escape_string($_POST['mobile_primary']));
		}

		//get the primary email field
		if(isset($_POST['email_primary']) && $_POST['email_primary']){
			$email_primary = strip_tags(mysql_real_escape_string($_POST['email_primary']));
		} elseif($submitted){
			$update_success = FALSE;
			$message_val->fields['email_primary'] = 'Email Primary';
		}

		$contact_type_id_primary = 2;

		//block for billing contact

		//get the billing first name field
		if(isset($_POST['name_first_billing']) && $_POST['name_first_billing']){
			$name_first_billing = strip_tags(mysql_real_escape_string($_POST['name_first_billing']));
		} elseif($submitted){
			$update_success = FALSE;
			$message_val->fields['name_first_billing'] = 'First Name Billing';
		}

		//get the billing last name field
		$name_last_billing = NULL;
		if(isset($_POST['name_last_billing']) && $_POST['name_last_billing']){
			$name_last_billing = strip_tags(mysql_real_escape_string($_POST['name_last_billing']));
		}

		//get the billing phone field
		$phone_billing = NULL;
		if(isset($_POST['phone_billing']) && $_POST['phone_billing']){
			$phone_billing = strip_tags(mysql_real_escape_string($_POST['phone_billing']));
		}

		//get the billing mobile field
		$mobile_billing = NULL;
		if(isset($_POST['mobile_billing']) && $_POST['mobile_billing']){
			$mobile_billing = strip_tags(mysql_real_escape_string($_POST['mobile_billing']));
		}

		//get the billing email field
		if(isset($_POST['email_billing']) && $_POST['email_billing']){
			$email_billing = strip_tags(mysql_real_escape_string($_POST['email_billing']));
		} elseif($submitted){
			$update_success = FALSE;
			$message_val->fields['email_billing'] = 'Email Billing';
		}

		//Make sure that the filesize is not larger than 2MB
		if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
			$filesize = $_FILES['image']['size'];
			$max_filesize = 2;

			//Check for filesize
			if ($filesize > ($max_filesize * 1048576)){
				$message_val->other[] = 'Images must be under ' . $max_filesize . 'MB in size';
				$update_success = FALSE;
			}
		}

		$contact_type_id_billing = 1;

		//do the inserts
		if($submitted){

			//Update Successful
			if($update_success){

				//check if it is insert of update case
				if($totalRows_retClientCompany > 0){
					//first we update the client company table
					$updateSQL = sprintf("UPDATE client_companies SET client_company_type='$client_company_type', name='$name', URL='$URL', ABN='$ABN',  number_of_brands='$number_of_brands', max_number_of_observers='$max_number_of_observers',max_sessions_brand='$max_sessions_brand',start_date='$start_date', end_date='$end_date',self_moderated='$self_moderated', global_admin='$global_admin', enable_chatroom_logo='$enable_chatroom_logo', comments='$comments', updated='$created' WHERE id=$client_company_id");
					//echo $updateSQL;
					//exit;
					mysql_select_db($database_ifs, $ifs);
					$Result1 = mysql_query($updateSQL, $ifs) or die(mysql_error());

					//now we update addresses
					//first the trading

					$updateSQL2 = sprintf("UPDATE addresses SET street= '$trading_street', suburb='$trading_suburb', state='$trading_state', post_code='$trading_post_code', country_id='$trading_country_id', address_type='Trading', updated='$created' WHERE id=$trading_address_id");
					mysql_select_db($database_ifs, $ifs);
					$Result2 = mysql_query($updateSQL2, $ifs) or die(mysql_error());
				} else {
					//first we insert in addresses
					$insertSQL = sprintf("INSERT INTO addresses (street, suburb, state, post_code, country_id, address_type, created) VALUES ('$trading_street', '$trading_suburb','$trading_state','$trading_post_code','$trading_country_id','Trading', '" . $created . "')");

					mysql_select_db($database_ifs, $ifs);
					$Result1 = mysql_query($insertSQL, $ifs) or die(mysql_error());
					$address_id = mysql_insert_id($ifs);

					//insert into client_company
					$insert2SQL = sprintf("INSERT INTO client_companies (client_company_type, address_id, name, URL, ABN,  number_of_brands, max_sessions_brand ,max_number_of_observers, self_moderated, global_admin, enable_chatroom_logo, start_date, end_date, comments, created) VALUES
					('$client_company_type', $address_id, '$name', '$URL', '$ABN',  '$number_of_brands','$max_sessions_brand','$max_number_of_observers','$self_moderated' ,'$global_admin', '$enable_chatroom_logo' ,'$start_date','$end_date', '$comments'  , '" . $created . "')");

					mysql_select_db($database_ifs, $ifs);
					$Result2 = mysql_query($insert2SQL, $ifs) or die(mysql_error());

					$client_company_id = mysql_insert_id($ifs);
				}

				//check if insert or update case if billing address edit
				if($billing_address_id > 0){
					//now we update addresses

					//billing address
					$updateSQL2=sprintf("UPDATE addresses SET street= '$billing_street', suburb='$billing_suburb', state='$billing_state', post_code='$billing_post_code', country_id='$billing_country_id', address_type='Billing', updated='$created' WHERE id=$billing_address_id");
					mysql_select_db($database_ifs, $ifs);
					$Result2 = mysql_query($updateSQL2, $ifs) or die(mysql_error());
				} else {
		      //first we insert in addresses
					$insertSQL = sprintf("INSERT INTO addresses (street, suburb, state, post_code, country_id, address_type, created) VALUES ('$billing_street', '$billing_suburb','$billing_state','$billing_post_code','$billing_country_id','Billing', '" . $created . "')");

					mysql_select_db($database_ifs, $ifs);
					$Result1 = mysql_query($insertSQL, $ifs) or die(mysql_error());
					$billing_address_id = mysql_insert_id($ifs);

		      //then we update the client company table
					$updateSQL = sprintf("UPDATE client_companies SET billing_address_id='$billing_address_id' , updated='$created'  WHERE id=$client_company_id");

					mysql_select_db($database_ifs, $ifs);
					$Result1 = mysql_query($updateSQL, $ifs) or die(mysql_error());
				}

				//insert or update the client company contacts
				//first the primary contact
				if($totalRows_retPrimaryContact > 0){
					$updateSQL2=sprintf("UPDATE client_company_contacts SET name_first= '$name_first_primary', name_last='$name_last_primary', phone='$phone_primary', mobile='$mobile_primary', email='$email_primary', contact_type_id='$contact_type_id_primary', client_company_id=$client_company_id ,updated='$created' WHERE id=" . $row_retPrimaryContact['id'] . "");
					mysql_select_db($database_ifs, $ifs);
					$Result2 = mysql_query($updateSQL2, $ifs) or die(mysql_error());
				} else {
					$insertSQL = sprintf("INSERT INTO client_company_contacts (name_first, name_last, phone, mobile, email, contact_type_id, client_company_id,created) VALUES ('$name_first_primary', '$name_last_primary','$phone_primary','$mobile_primary','$email_primary','$contact_type_id_primary', $client_company_id,'" . $created . "')");

					mysql_select_db($database_ifs, $ifs);
					$Result1 = mysql_query($insertSQL, $ifs) or die(mysql_error());
				}

				//next the billing contact
				if($totalRows_retBillingContact > 0){
					$updateSQL2 = sprintf("UPDATE client_company_contacts SET name_first= '$name_first_billing', name_last='$name_last_billing', phone='$phone_billing', mobile='$mobile_billing', email='$email_billing', contact_type_id='$contact_type_id_billing', updated='$created' WHERE id=" . $row_retBillingContact['id'] . "");
					mysql_select_db($database_ifs, $ifs);
					$Result2 = mysql_query($updateSQL2, $ifs) or die(mysql_error());

				} else {
					$insertSQL = sprintf("INSERT INTO client_company_contacts (name_first, name_last, phone, mobile, email, contact_type_id, client_company_id ,created) VALUES ('$name_first_billing', '$name_last_billing','$phone_billing','$mobile_billing','$email_billing','$contact_type_id_billing', $client_company_id,'" . $created . "')");

					mysql_select_db($database_ifs, $ifs);
					$Result1 = mysql_query($insertSQL, $ifs) or die(mysql_error());

				}

				if($client_company_id){
					if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
						$_GET['client_company_id'] = $client_company_id;
						$_GET['client_company_name'] = $name;

						$default_content = $other_content; //save other content value
						include($import_page);

						//If message set
						if($message){
							$message_val->other[] = $message;

							unset($message);
							$update_success = FALSE;
							$other_content = $default_content; //use the value again
						}
					}
				}

				mysql_close($ifs);

				$updateGoTo = "signup.php?client_company_id=".$client_company_id;
				header(sprintf("Location: %s", $updateGoTo));
				die();

			} else { //Update not successful
				$fields = array_keys($message_val->fields);
				$message = process_messages($message_val);
			}
		}

		require_once('views/home.php');
} else {
	$_SESSION['notification'] = 'You are logged out, please login again.';

	if(!admin($database_ifs, $ifs)){
		$_SESSION['current_location'] = $form_action;
	}

	mysql_close($ifs);

	header('Location: index.php');
	die();
}

mysql_close($ifs);
