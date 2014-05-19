 	
<?php
	
	function uploadCsv($URL, $brand_project_id, $database, $ifs){
		require_once('models/participant-email-model.php');

		ini_set('auto_detect_line_endings',true);
		$file = $_FILES['csvFile'];
		$error = $file['error']; 
		$type = $_FILES ['csvFile']['type'];
		$size = $_FILES['csvFile']['size'];
		$fileName = $file['name'];  	// get File name
		$actualFile=$_FILES['csvFile']['tmp_name'];
		$fileURL=$URL.$fileName;
		// extention name test
		
		$extention = strtolower(substr($fileName,strpos($fileName,'.')+1));
		$country_id = NULL;
		$countryID = array();		
		
		if($extention=='csv'){
			//  uplodat the file to the server
			if(move_uploaded_file($actualFile,$fileURL)){
					//echo "file uploaded";	
			} else {
				$message="Fail to upload the file";
				echo "<script language=\"JavaScript\">\n";
				echo "alert(\" $message \");\n";
				echo "</script>";

			}

			// read the csv data from local disk
			$handle = fopen("$fileURL","r");

			if ($handle){
				$counter1=0;
					
				// do the data validation 
				// at this stage, we only do the Email address validation,name validation and participant address validataion
				
				// declare a array to keep track fo the suburb id
				$suburbID=array();

				while(($data = fgetcsv($handle,1000,',')) !== FALSE){
					if(!$counter1){
					
					} else {
						// first name and last name validation
						if(!$data[0] || !$data[1]){
							$message = "Missing information  in line $counter1, Fail to upload the file";	
						    echo "<script language=\"JavaScript\">\n";
						    echo "alert(\" $message \");\n";
						    echo "</script>";
							return;

						} elseif(!(filter_var($data[3], FILTER_VALIDATE_EMAIL))){ // email address validation
							$message = "Invaild Email address in line $counter1, Fail to upload the file";
							echo "<script language=\"JavaScript\">\n";
								    echo "alert(\" $message \");\n";
							    echo "</script>";
							return;

						} elseif($data[10]) {
							// country validation
							mysql_select_db($database, $ifs);
							$query_retCountry = "
							SELECT 
							country_lookup.country_name,
							country_lookup.id
							FROM
							country_lookup
							WHERE 
							country_lookup.country_name='$data[10]'
							ORDER BY
							country_lookup.country_name
							";
							$retCountry = mysql_query($query_retCountry, $ifs) or die(mysql_error());
							$row_retCountry = mysql_fetch_assoc($retCountry);
							$totalRows_retCountry = mysql_num_rows($retCountry);
							
							if($totalRows_retCountry > 0){
								// get the country id
								$countryID[]=$row_retCountry['id'];

							} else {
								$message = "Invaild Country Name: ".$data[10];
								echo "<script language=\"JavaScript\">\n";
								echo "alert(\" $message \");\n";
								echo "</script>";
								return;

							}							
						}								
					}

					$counter1++;		
				}// while loop for the data validation
			
				// reset the file pointer			
				rewind($handle);
				$counter2 = 0;
				// start read the data from csv file and store into the database
				
				while (($data = fgetcsv($handle, 1000, "," )) !== FALSE){	
					if($counter2 !== 0){
						
						$name_first = (isset($data[0]) ? $data[0] : NULL);
						$name_last = (isset($data[1]) ? $data[1] : NULL);
						$gender = (isset($data[2]) ? strtolower($data[2]) : NULL);
						$email = (isset($data[3]) ? $data[3] : NULL);

						$username = $email;							
						$password= create_unique_password();
						$created = date('Y-m-d H:i:s');
						
						$mobile = (isset($data[4]) ? $data[4] : 0);
						$phone = (isset($data[5]) ? $data[5] : 0);							

						$street = (isset($data[6]) ? $data[6] : NULL);
						$suburb = (isset($data[7]) ? $data[7] : NULL);
						$postcode = (isset($data[8]) ? $data[8] : NULL);
						$state = (isset($data[9]) ? $data[9] : NULL);							
						
						$invite_again = (isset($data[11]) ? $data[11] : NULL);
						$dob = (isset($data[12]) ? $data[12] : NULL);
						$ethnicity = (isset($data[13]) ? $data[13] : NULL);
						$occupation = (isset($data[14]) ? $data[14] : NULL);
						$brand_segment = (isset($data[15]) ? $data[15] : NULL);
						$optional1 = (isset($data[16]) ? $data[16] : NULL);
						$optional2 = (isset($data[17]) ? $data[17] : NULL);
						$optional3 = (isset($data[18]) ? $data[18] : NULL);
						$optional4 = (isset($data[19]) ? $data[19] : NULL);
						$optional5 = (isset($data[20]) ? $data[20] : NULL);

						//Go onoto next row if either of these values are false
						if(!$name_first || !$name_last || !$email){
							continue;
						}

						if(!empty($countryID)){
							$country_id = $countryID[$counter2-1];
						}							

						if($gender){
							/* Check gender and set the appropriate variable */
							switch($gender){
								case 'male':
									$avatar_gender = '0:4:0:0:0:0';
								break;
								case 'female':
									$avatar_gender = '0:4:6:6:5:0';
								break;
							}

							$gender = ucwords($gender);
						}

						/* Make sure invite has the correct value */
						if($invite_again){
							$invite_again_value = strtolower($invite_again);
							if($invite_again_value != 'no' && $invite_again_value != 'yes'){
								$invite_again_value = 'yes';
							}

							$invite_again = ucwords($invite_again_value);
						} else {
							$invite_again = 'Yes'; //set invite agin as default
						}
						
						if(($invite_again && $invite_again == 'Yes') || ($interest && $interest == 1)){
							// insert into address
							$insert2SQL = sprintf("INSERT INTO addresses(street, suburb, state, post_code," . ($country_id ? " country_id, " : '') . "created) VALUES ('$street', '$suburb','$state','$postcode'," . ($country_id ? " '$country_id', " : '') . "'$created')");
							mysql_select_db($database, $ifs);
							$Result2 = mysql_query($insert2SQL, $ifs) or die(mysql_error());
							$address_id = mysql_insert_id($ifs);

							// insert into the  users
							$insert3SQL = "INSERT INTO users(name_first, name_last, avatar_info, email, Gender, mobile, phone, address_id, created) 
								VALUES ('$name_first','$name_last', '$avatar_gender', '$email','$gender','$mobile','$phone', '$address_id','$created')";
							
							mysql_select_db($database, $ifs);
							$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
							$user_id = mysql_insert_id($ifs);

							// insert into participant
							$insert4SQL = sprintf("INSERT INTO participants(user_id, brand_project_id, dob, ethnicity, occupation, brand_segment, optional1, optional2,optional3, optional4, optional5, created, invite_again) VALUES ($user_id, $brand_project_id, '$dob', '$ethnicity', '$occupation', '$brand_segment', '$optional1','$optional2','$optional3','$optional4','$optional5', '$created', '$invite_again')");
							mysql_select_db($database, $ifs);
							$Result4 = mysql_query($insert4SQL, $ifs) or die(mysql_error());
							$participant_id= mysql_insert_id($ifs);
						}
					}
					$counter2++;
				} // while loop for the insertion

				if (!feof($handle)){
					$message= "Error: unexpected fgets() fail\n";
					echo "<script language=\"JavaScript\">\n";
					echo "alert(\" $message \");\n";
					echo "</script>";
				}

				fclose($handle);	
			}// if statement			
		} else { // if-- extention ==csv
			$message="Wrong File Type!";
			echo "<script language=\"JavaScript\">\n";
			echo "alert(\" $message \");\n";
			echo "</script>";
		}

		mysql_close($ifs);				
	}// function
