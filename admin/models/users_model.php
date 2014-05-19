<?php
	function retrieve_users($database, $ifs, $session = true, $facilitator = null, $id = null, $new = false, $staff = null, $active = null, $type_id = null, $return_sql = false){
		//retrieve user types
		mysql_select_db($database, $ifs);

		//Find company id
		$company_id = NULL;
		if(isset($_SESSION['MM_CompanyId'])){
			$company_id = $_SESSION['MM_CompanyId'];
		}

		//Set as IFS Admin
		if($company_id == -1 && $staff !== FALSE && $session && !$new){
			$staff = true;
		} elseif($staff && (!$session || $new)){//make sure function is still usable to retrieve standard users
			$staff = false;
		}

		//Check if Observer
		$observer = false;
		if($type_id && $type_id == 4 && $staff && !$facilitator){
			$observer = true;
		}

		if($company_id > 0 && $session){ //if company id is set and session staff should be included
			$query_retUserLogin = "SELECT 				  
			  users.user_login_id,
			  users.name_first,
			  users.name_last,
			  users.email,
			  users.phone,
			  users.mobile,
			  users.fax,
			  users.invites,
			  users.invites_accepted,
			  users.invites_not_now,
			  users.invites_no_reply,
			  users.green_room_visit,
			  users.avatar_info,
			  users.job_title,			  
			  users.Gender,
			  users.uses_landline,
			  client_users.id AS `client_user_id`,
			  client_users.user_id,
			  client_users.active,
			  client_users.client_company_id,
			  client_users.type_id" .
			  (!$new ? ", user_logins.username, user_logins.id " : " ") .
			"FROM
			  client_users
				INNER JOIN users ON (client_users.user_id = users.id) " .
				(!$new ? "INNER JOIN user_logins ON (users.user_login_id = user_logins.id) " : "") .
				($facilitator ? "INNER JOIN session_staff ON (client_users.user_id = session_staff.user_id AND session_staff.type_id IN(2, 3)) " : "") .
				($observer ? "LEFT JOIN (SELECT id AS `ssid`, session_id, user_id AS `suid`, type_id FROM session_staff) AS `session_staff` ON (users.id = session_staff.suid AND session_staff.type_id IN(4))" : "") .
				"WHERE " .
				  ($active ? 'client_users.active = 1 AND ' : '') .
				  "client_users.client_company_id = '".$_SESSION['MM_CompanyId'] . "'" . " AND client_users.deleted is null " .
				 	($id ? " AND client_users.user_id = " . $id : "") .
				 	($type_id ? " AND client_users.type_id = " . $type_id : "");
		}	else 	{
			$query_retUserLogin = "SELECT 
			  users.id AS `user_id`,
			  users.user_login_id,
			  users.name_first,
			  users.name_last,
			  users.Gender,
			  users.email,
			  users.job_title,
			  users.phone,
			  users.mobile,
			  users.fax,
			  users.green_room_visit,
			  users.avatar_info,
			  users.invites,
			  users.invites_accepted,
			  users.invites_not_now,
			  users.invites_no_reply" .
			  ($staff ? ", client_users.type_id, client_users.id AS `client_user_id`, client_users.active, client_users.client_company_id" : "") .
			  (!$new ? ", user_logins.username, user_logins.id" : "") .
			  ($observer ? ", session_staff.suid, session_staff.ssid, session_staff.session_id " : " ") . 
			"FROM
			  users " .
				(!$new ? "INNER JOIN user_logins ON (users.user_login_id = user_logins.id) " : "") .
				($staff ? "INNER JOIN client_users ON (users.id = client_users.user_id and client_users.deleted is null ) " : "") .
				($facilitator ? "INNER JOIN session_staff ON (users.id = session_staff.user_id AND session_staff.type_id IN(2, 3))" : "") .
				($observer ? "LEFT JOIN (SELECT id AS `ssid`, session_id, user_id AS `suid`, type_id FROM session_staff) AS `session_staff` ON (users.id = session_staff.suid AND session_staff.type_id IN(4))" : "") .
			($id ? " WHERE users.id = " . $id : "") .
			($type_id && $staff ? ($id ? ' AND' : ' WHERE') . " client_users.type_id = " . $type_id : "") .
			(!$return_sql ? " ORDER BY users.name_first" : '');		
		}

		/* Return SQL */
		if($return_sql){
			return $query_retUserLogin;
		}

		$retUserLogin = mysql_query($query_retUserLogin, $ifs);

		if($retUserLogin && mysql_num_rows($retUserLogin) > 0){
			return $retUserLogin;
		} else {
			return mysql_error();
		}
	}

	/**
	* Retrieve Users for the Administrators Page
	**/
	function retrieve_users_admin_page($database, $ifs, $sidx = 'users.email', $sord = 'asc', $start = null, $limit = null){
		$users_sql = retrieve_users($database, $ifs, true, null, null, false, true, null, null, true);

		if(is_string($users_sql)){
			/* Group By user_login_id */
			$users_sql .= "\nORDER BY $sidx $sord";

			if($start !== NULL && $limit !== NULL){
				$users_sql .= "\nLIMIT $start, $limit";
			}

			$retUserLogin = mysql_query($users_sql, $ifs);

			if($retUserLogin && mysql_num_rows($retUserLogin) > 0){
				return $retUserLogin;
			} else {
				return mysql_error();
			}
		} else {
			return FALSE;
		}
	}

	/**
	* Retrieve Potential Observers
	**/
	function retrieve_potential_observers($database, $ifs, $brand_project_id, $session_id = null){
		//Get user information
		$user_sql = retrieve_users($database, $ifs, true, null, null, false, true, true, 4, true);

		/* Complete SQL Query */
		$user_sql .= ' AND client_users.bpid = ' . $brand_project_id . "\n";		
		
		/* If session_id is set */
		if($session_id){			
			$user_sql .= sprintf('AND (session_staff.suid IS NULL OR (session_staff.suid IS NOT NULL AND session_staff.session_id != %d))', $session_id) . "\n";
		}

		$user_sql .= 'ORDER BY users.name_first';

		$retUserLogin = mysql_query($user_sql, $ifs);

		if($retUserLogin && mysql_num_rows($retUserLogin) > 0){
			return $retUserLogin;
		} else {
			return mysql_error();
		}
	}

	/**
	* Find Client User
	**/
	function retrieve_client_user($database, $ifs, $client_user_id = null, $user_id = null, $type_id = null){
		//Check that user id was supplied
		if(!$user_id && !$client_user_id){
			return FALSE;
		}

		mysql_select_db($database, $ifs); //select db

		$client_user_sql = sprintf(
			"SELECT
				*
			FROM
				client_users
			WHERE
				%s = %d
				%s",
			($user_id ? 'user_id' : 'id'), //use user id or the client user id depending on what is available
			($user_id ? $user_id : $client_user_id),
			($type_id ? 'AND type_id = ' . $type_id : '')
		);

		$results = mysql_query($client_user_sql, $ifs);

		if($results){ //if query was successful
			if(mysql_num_rows($results) > 0){ //if any rows found
				return $results;
			} else {
				$mysql_error = mysql_error();

				if($mysql_error){
					return $mysql_error;
				} else {
					return FALSE;
				}
			}
		}
	}

	/**
	* Retrieve Session Staff
	**/
	function retrieve_session_staff($database, $ifs, $session_staff_id = null, $user_id = null){
		//Check that user id was supplied
		if(!$user_id && !$session_staff_id){
			return FALSE;
		}

		mysql_select_db($database, $ifs); //select db

		$session_staff_sql = sprintf(
			"SELECT
				*
			FROM
				session_staff
			WHERE
				%s = %d",
			($user_id ? 'user_id' : 'id'), //use user id or the client user id depending on what is available
			($user_id ? $user_id : $session_staff_id)
		);

		$results = mysql_query($session_staff_sql, $ifs);

		if($results){ //if query was successful
			if(mysql_num_rows($results) > 0){ //if any rows found
				return $results;
			} else {
				$mysql_error = mysql_error();

				if($mysql_error){
					return $mysql_error;
				} else {
					return FALSE;
				}
			}
		}
	}

	/**
	* Delete user
	**/
	function delete_user($database, $ifs, $user_id, $client_user_id = null){
		if(!$user_id){
			return FALSE;
		}

		$deleted = false; //allows to check if user is fully deleted
		$notification = array();
		
		//Prepare the result that will be returned
		$returned_result = new StdClass;
		$returned_result->message = new StdClass;

		//get name of user
		$name = null;
		$user_info = get_user_id_and_name($database, $ifs, null, $user_id);
		if($user_info && !is_string($user_info)){
			$user_row = mysql_fetch_assoc($user_info); //get row

			$name = $user_row['name_first'] . ' ' . $user_row['name_last'];
		}

		$client_user_found = retrieve_client_user($database, $ifs, null, $user_id);

		//check if should delete from client user
		if($client_user_found && !is_string($client_user_found)){
			$result = delete_staff($database, $ifs, 'administator', $user_id, $client_user_id);

			if($result && !is_string($result)){
				//Check again
				if($client_user_id){					
					$client_user_found = retrieve_client_user($database, $ifs, null, $user_id);
				}

				$deleted = true;
			} else {
				//Set notification for unsuccessful deletion
				$notification[] = sprintf(
					'The user%s could not be removed as a client user',
					($name ? ', ' . $name : '')
				);

				$deleted = false;
			}
		}

		//If client user set, make sure that it does not delete all instances of the user
		//if(!$client_user_id || ($client_user_id && $deleted && !$client_user_found)){
        if(!$client_user_id || ($client_user_id && $deleted)){//update instead delete
			$session_staff_found = retrieve_session_staff($database, $ifs, null, $user_id);

			//check if should delete from session staff
			if($session_staff_found && !is_string($session_staff_found)){
				$result = delete_staff($database, $ifs, 'session_staff', $user_id);

				if($result && !is_string($result)){
					$deleted = true;
				} else {
					//Set notification for unsuccessful deletion
					$notification[] = sprintf(
						'The user%s could not be removed as a session staff',
						($name ? ', ' . $name : '')
					);

					$deleted = false;
				}
			}

			$participant_found = find_participant($database, $ifs, null, false, false, $user_id);

			//check if should delete from participants
			if($participant_found && !is_string($participant_found)){
				$result = delete_participant($database, $ifs, null, $user_id);

				if($result && !is_string($result)){
					$deleted = true;
				} else {
					//Set notification for unsuccessful deletion
					$notification[] = sprintf(
						'The user%s could not be removed as a participant',
						($name ? ', ' . $name : '')
					);

					$deleted = false;
				}
			}

			$participant_list_found = find_participant($database, $ifs, null, false, true, $user_id);

			//check if should delete from participant_lists
			if($participant_list_found && !is_string($participant_list_found)){
				$result = delete_participant($database, $ifs, null, $user_id, true);

				if($result && !is_string($result)){
					$deleted = true;
				} else {
					//Set notification for unsuccessful deletion
					$notification[] = sprintf(
						'The user%s could not be removed as an invited participant',
						($name ? ', ' . $name : '')
					);

					$deleted = false;
				}
			}

			//Clear user login ids
			$cleared_users = clear_user_login_ids($database, $ifs, $user_id);

			if($cleared_users && !is_string($cleared_users)){
				$deleted = true;

				$result = delete_user_login_ids($database, $ifs, $user_id);

				if($result && !is_string($result)){
					$deleted = true;
				} else {
					//Set notification for unsuccessful deletion
					$notification[] = sprintf(
						'The user\'s (%s) login data could not be deleted',
						($name ? ', ' . $name : '')
					);

					$deleted = false;
				}
			} else {
				//Set notification for unsuccessful clearing
				$notification[] = sprintf(
					'The user\'s (%s) login data could not be cleared',
					($name ? $name : '')
				);

				$deleted = false;
			}
		}

		if($deleted){
			$notification[] = sprintf(
				'The user%s was deleted from the system',
				($name ? ', ' . $name . ',' : '')
			);						
		}

		//Prepare reuturned result;
		$returned_result->status = $deleted;
		$returned_result->message = $notification;

		return $returned_result;	
	}

	/**
	* Delete Participants
	**/
	function delete_participant($database, $ifs, $participant_id = null, $user_id = null, $lists = false){
		if(!$participant_id && !$user_id){
			return FALSE;
		}

		mysql_select_db($database, $ifs);

		//Select the correct field query for using a where condition
		$field = 'id';
		//if participant id is provided and is deleting from lists
		if($participant_id && $lists){ 
			$field = 'participant_id';
		} elseif($user_id){
			$field = 'user_id';
		}

		//Query for deleting participant
        /*
		$delete_part_sql = sprintf(
			"DELETE FROM
				%s
			WHERE
				%s = %d",
			($lists ? 'participant_lists' : 'participants'),
			$field,
			($user_id ? $user_id : $participant_id)
		);
        */
        $delete_part_sql = sprintf(
            "UPDATE
                %s set deleted = Now()
            WHERE
                %s = %d",
            ($lists ? 'participant_lists' : 'participants'),
            $field,
            ($user_id ? $user_id : $participant_id)
        );

		$result = mysql_query($delete_part_sql, $ifs);

		if($result){ //deletion was successful
			return TRUE;
		} else {
			$mysql_error = mysql_error();
			if($mysql_error){ //if there was an error
				return $mysql_error;
			} else {
				return FALSE;
			}
		}
	}

	/**
	* Delete user logins
	**/
	function delete_user_login_ids($database, $ifs, $user_id){
		if(!$user_id){
			return FALSE;
		}

		//store all user ids in user logins
		$user_ids = array();
		$user_logins = get_user_id_and_name($database, $ifs, null, $user_id);

		if($user_logins && !is_string($user_logins)){
			//Find all the user ids stored in user logins
			while($row = mysql_fetch_assoc($user_logins)){
				$user_ids[] = $row['user_id'];
			}
		}

		if(!empty($user_ids)){
			mysql_select_db($database, $ifs);

			//Clear user login ids for certain users
            /*
			$delete_ulid_sql = sprintf(
				"DELETE FROM
					user_logins				
				WHERE
					user_id IN(%s)",
				implode(',', $user_ids)
			);
            */
            $delete_ulid_sql = sprintf(
                "UPDATE
                    user_logins	set deleted = Now()
                WHERE
                    user_id IN(%s)",
                implode(',', $user_ids)
            );

            //Perform action for query
			$result = mysql_query($delete_ulid_sql, $ifs);

			if($result){ //deletion was successful
				return TRUE;
			} else {
				$mysql_error = mysql_error();
				if($mysql_error){ //if there was an error
					return $mysql_error;
				} else {
					return FALSE;
				}
			}
		} else {
			return FALSE;
		}
	}

	/**
	* Clear user login ids
	**/
	function clear_user_login_ids($database, $ifs, $user_id){
		if(!$user_id){
			return FALSE;
		}

		//store all user ids in user logins
		$user_ids = array();
		$user_logins = get_user_id_and_name($database, $ifs, null, $user_id);

		if($user_logins && !is_string($user_logins)){
			//Find all the user ids stored in user logins
			while($row = mysql_fetch_assoc($user_logins)){
				$user_ids[] = $row['user_id'];
			}
		}

		if(!empty($user_ids)){
			mysql_select_db($database, $ifs);

			//Clear user login ids for certain users
            /*
			$clear_ulid_sql = sprintf(
				"UPDATE
					users
				SET
					user_login_id = NULL
				WHERE
					id IN(%s)",
				implode(',', $user_ids)
			);
            */
            $clear_ulid_sql = sprintf(
                "UPDATE
                    users
                SET
                    deleted = Now()
                WHERE
                    id IN(%s)",
                implode(',', $user_ids)
            );

			//Perform action for query
			$result = mysql_query($clear_ulid_sql, $ifs);

			if($result){ //deletion was successful
				return TRUE;
			} else {
				$mysql_error = mysql_error();
				if($mysql_error){ //if there was an error
					return $mysql_error;
				} else {
					return FALSE;
				}
			}
		} else {
			return FALSE;
		}
	}

	/**
	* Retrieve users by email and/or session
	**/
	function retrieve_user_by_email($database, $ifs, $email){
		if(!$email){
			return FALSE;
		}

		//Allow all the emails to be searched
		if(is_array($email)){
			$email = implode(', ', $email);
		}

		mysql_select_db($database, $ifs); //select DB

		//Retrieve user information for users
		$user_query = sprintf(
			"SELECT
				users.*,
				sess.name AS `session_name`,
				staff.type_id,
				clients.client_user_id,
				clients.type_id AS `client_type_id`,
				companies.client_company_name,
				staff.session_id,
				staff.active,
				staff_sessions.name AS `staff_session_name`
			FROM users
				INNER JOIN user_logins ON(user_logins.user_id = users.id)
				LEFT JOIN (SELECT id AS `client_user_id`, client_company_id, type_id, user_id FROM client_users) AS `clients` ON(users.id = clients.user_id)
				LEFT JOIN (SELECT id, name AS `client_company_name` FROM client_companies) AS `companies` ON(clients.client_company_id = companies.id)
				LEFT JOIN (SELECT id, user_id FROM participants) AS `part` ON (users.id = part.user_id)
				LEFT JOIN (SELECT id, participant_id, session_id FROM participant_lists) AS `p_list` 
					ON(part.id = p_list.participant_id)
				LEFT JOIN (SELECT id, name FROM sessions) AS `sess` ON (p_list.session_id = sess.id)
				LEFT JOIN (SELECT id, user_id, type_id, session_id, active FROM session_staff) AS `staff` ON(users.id = staff.user_id)
				LEFT JOIN (SELECT id, name FROM sessions) AS `staff_sessions` ON(staff.session_id = staff_sessions.id)
			WHERE
				users.email IN('%s')",
			$email
		);

		$results = mysql_query($user_query, $ifs); //get reults

		if($results && mysql_num_rows($results) > 0){
			return $results;
		} else {
			return mysql_error();
		}
	}

	/**
	* Get user id and name user login id
	**/
	function get_user_id_and_name($database, $ifs, $user_login_id = null, $user_id = null){
		//Check if either user login id or user id is avaialble
		if(!$user_login_id && !$user_id){
			return FALSE;
		}

		mysql_select_db($database, $ifs); //select db

		//Set query
		$user_id_query = sprintf(
			"SELECT
				user_logins.id,
				user_logins.user_id,
				users.name_first,
				users.name_last,
				users.email
			FROM
				user_logins
				INNER JOIN users ON(user_logins.user_id = users.id)
			WHERE
				%s = %d",
				($user_login_id ? 'user_logins.id' : 'users.id'),
				($user_login_id ? $user_login_id : $user_id)
		);

		$results = mysql_query($user_id_query, $ifs); //run query

		//If the query was successful and rows were found
		if($results && mysql_num_rows($results) > 0){
			return $results;
		} else {
			return mysql_error();
		}		
	}

	/**
	* Process User Accounts
	**/
	function process_user_accounts($user){
		$found_users = new stdClass;
		$found_users->participants = array();
		$found_users->observers = array();
		$found_users->staff = array();

		//If no users are found, return empty stdClass
		if(!$user){
			return $found_users;
		}

		/**
		* loop through the found users to find out what kind of user it is
		* and how the system should act
		**/				

		while($found_user = mysql_fetch_assoc($user)){
			$role_found = false;

			//A participant found
			if(isset($found_user['session_name']) && $found_user['session_name']){
				$found_users->participants[$found_user['user_login_id']] = $found_user['session_name'];

				$role_found = true; //role set
			}

			//Staff found
			if($found_user['type_id'] || $found_user['client_type_id']){
				$type_id = $found_user['type_id']; //set staff type id
				$client_type_id = $found_user['client_type_id']; //set client type id

				$role_found = true; //role set

				$final_type_id = ($client_type_id ? $client_type_id : $type_id);

				//Set name
				$name = ($found_user['staff_session_name'] ? $found_user['staff_session_name'] : null);
				
				//If Global Admin, set client_company name
				if($final_type_id == 1){
					if($found_user['client_company_name']){
						$name = $found_user['client_company_name'];

					} elseif($name){ //if no company name, but session name is available, define it as a session
						$name .= ' (Session)';

					}
				}

				//An observer found
				if($type_id == 4){
					$found_users->observers[$found_user['user_login_id']] = ($name ? array($final_type_id => $name) : $final_type_id);
				} elseif($final_type_id != 4) { //other staff found
					$found_users->staff[$found_user['user_login_id']] = ($name ? array($final_type_id => $name) : $final_type_id); //just allow one login
				}
			}

			//Set IFS admin
			if(!$role_found && $found_user['ifs_admin']){
				$found_users->staff[$found_user['user_login_id']] = -1;
			}
		}

		return $found_users;
	}

	/**
	* Validate User Email
	**/
	function validate_user_email($email){
		//make sure that the email is not empty
		if(!$email){
			return FALSE;
		}

		//Make sure that email is valid an taht it contains the necessary characters
		return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
	}

	/**
	* Create user_logins
	**/
	function create_user_logins($database, $ifs, $username, $password, $user_id, $user_login_id = null){
		/* Make sure it is an actual password and the user can be referenced */
		if(!$password){
			return FALSE;
		}

		if(!$user_id && !$user_login_id){
			$user_id = NULL;
		}

		$created = date('Y-m-d H:i:s');

		/* Check if this is an update */
		$update = FALSE;
		if($user_login_id){
			$update = TRUE;
		}

		if(!$update){
			// create a user login entry
			$insertSQL = sprintf(
				"INSERT INTO 
					user_logins (username, password, created, user_id) 
				VALUES ('%s', '%s', '%s', %d)", $username, $password, $created, $user_id);
		} else {
			//update the user login id
			$insertSQL = sprintf(
				"UPDATE
					user_logins
				SET
					password = '%s',
					updated = '%s'
				WHERE
					id = %d",
				$password,
				$created,
				$user_login_id	
			);
		}

		mysql_select_db($database, $ifs);
		$Result1 = mysql_query($insertSQL, $ifs);

		if($Result1){
			/* Update is successful */
			if($update){
				return TRUE;
			}

			$user_login_id = mysql_insert_id($ifs);
			$updated = date('Y-m-d H:i:s');

			// upadte the users table
			$update2SQL = sprintf(
				"UPDATE 
					users 
				SET 
					user_login_id = '%s', 
					updated = '%s'
				WHERE 
					id = '%s'", $user_login_id, $updated, $user_id);			


			mysql_select_db($database, $ifs);
			$Result2 = mysql_query($update2SQL, $ifs);

			if($Result2){
				return $user_login_id;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}	

	/**
	* Delete Administator, Facilitator, Observer
	**/
	function delete_staff($database, $ifs, $role = 'administator', $user_id, $client_user_id = null){
		if(!$user_id || !is_numeric($user_id)){
			return 'The user was not set';
		}

		mysql_select_db($database, $ifs);

		/* Select table according to role */
		$table = 'client_users';
		if($role != 'administator'){
			$table = 'session_staff';

            $sql = sprintf(
                "DELETE FROM %s WHERE user_id = %d%s",
                $table,
                $user_id,
                ($client_user_id ? " AND id = " . $client_user_id : '')
            );

		} else
            $sql = sprintf(
                "UPDATE %s set deleted = Now(), active = 0 WHERE user_id = %d%s",
                $table,
                $user_id,
                ($client_user_id ? " AND id = " . $client_user_id : '')
            );

		/* Delete id */
		$delete_query = mysql_query($sql, $ifs);
		if($delete_query){
			return TRUE;
		} else {
			return mysql_error();
		}
	}

	/**
	* Check password
	**/
	function check_password($database, $ifs, $password, $user_login_id){
		if(!$password && $user_login_id){
			return FALSE;
		}

		$pass_hashed = md5($password);

		$pass_query = sprintf(
			"SELECT
				*
			FROM user_logins
			WHERE
				id = %d AND password = '%s'",
			$user_login_id,
			$pass_hashed
		);

		$result = mysql_query($pass_query, $ifs);
		
		if($result){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	* Request Change to Password
	**/
	function request_change_to_password($database, $ifs, $username, $user_login_JSON){
		require_once('models/participant-email-model.php'); //import for use in sending e-mail
        //$user_login_JSON='{"id":65932809,"name":"This session has \"quotes"}';
		$user_login_data=json_decode($user_login_JSON);
        $user_login_id=$user_login_data->id;

        $replacements = array();

		$key = generate_key_for_password_change($database, $ifs, $username, $user_login_id); //generate key

		/* Url for e-amil */
		$root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
		//Check if root includes ifs-test
		if(preg_match('/^\/ifs-test/', $_SERVER['REQUEST_URI'])){
			$root .= '/ifs-test';
		}

		$url = $root . '/forgot_password.php?email=' . $username . '&key=' . $key. '&activity=' . $user_login_data->name;
		$replacements['Link'] = $url; //set url to be displayed

		//Get the user info
		$user_info = get_user_id_and_name($database, $ifs, $user_login_id); //get user id
		$user_id = null;
		$full_name = '';

		//Set name information
		if($user_info && !is_string($user_info)){
			$user_row = mysql_fetch_assoc($user_info);
			$replacements['First Name'] = $user_row['name_first'];
			$replacements['Last Name'] = $user_row['name_last'];
            $replacements['Activity Name'] = $user_login_data->name;
			$full_name = $user_row['name_first'] . ' ' . $user_row['name_last']; //full name

			$user_id = $user_row['user_id']; //user id
		}

		//Send e-mail
		if(send_admin_email_to_user($database, $ifs, $username, $user_id, $full_name, 'A Password Change Has Been Requested', 'request_password_change', $replacements)){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	* Generate Key for Password Change request
	**/
	function generate_key_for_password_change($database, $ifs, $email, $user_login_id){
		//Key Generation		
		$time = time();
		$string_hash = sha1('This Is a Password Change');

		//Final Keys
		$raw_key = $string_hash . $time . $email . $user_login_id; //Create a key

		$key = sha1($raw_key);

		update_key($database, $ifs, $key, $user_login_id); //Update ket

		$key = htmlentities($key); //prepare for HTML output

		return $key;
	}

	/**
	* Update key time for use in forgot password
	**/
	function update_key($database, $ifs, $key, $user_login_id){
		$update_key_sql = sprintf(
			"UPDATE
				user_logins
			SET
				key_value = %s
			WHERE
				id = %d",
				(!$key ? 'NULL' : "'" . $key . "'"),
				$user_login_id
		);

		//Perform query
		mysql_select_db($database, $ifs);
		$results = mysql_query($update_key_sql, $ifs);
	}

	/**
	* Check key for rest password
	**/
	function check_key_for_reset_password($database, $ifs, $key, $email){
		$key = html_entity_decode($key);

		$string_hash = sha1('This Is a Password Change');

		//If time was found
		if($user_login_id = get_key_for_user($database, $ifs, $key, $email)){
			update_key($database, $ifs, null, $user_login_id); //reset key

			return $user_login_id;			
		} else {
			return FALSE;
		}
	}

	/**
	* Get time for key
	**/
	function get_key_for_user($database, $ifs, $key, $email){
		mysql_select_db($database, $ifs);

		//query for finding key generation time
		$key_time_sql = sprintf(
			"SELECT
				id
			FROM
				user_logins
			WHERE
				username = '%s'
				AND key_value = '%s'",
			$email,
			$key
		);

		$results = mysql_query($key_time_sql, $ifs);

		//Check if any rows were found
		if($results){
			if(mysql_num_rows($results) > 0){
				$row = mysql_fetch_assoc($results);

				return $row['id'];
			} else {
				return FALSE;
			}
		}
	}

	/**
	* Reset Password
	**/
	function reset_password($database, $ifs, $username, $user_login_id, $new_password, $message, $staff = false){
		$reset = new stdClass;
		$replacements = array();
		$staff_message = 'You can change your password using the backend system';

		//Get the user info
		$user_info = get_user_id_and_name($database, $ifs, $user_login_id); //get user id
		$user_id = null;
		$full_name = '';

		$reset->message = $message;
		
		//Set name information
		if($user_info && !is_string($user_info)){
			$user_row = mysql_fetch_assoc($user_info);
			$replacements['First Name'] = $user_row['name_first'];
			$replacements['Last Name'] = $user_row['name_last'];
			$full_name = $user_row['name_first'] . ' ' . $user_row['name_last']; //full name
			$user_id = $user_row['user_id']; //user id
		}

        if(isset($_GET['activity']))
            $replacements['Activity Name']=strip_tags($_GET['activity']);
        else
            $replacements['Activity Name']="requested activity"; //in case we don't know for some reason.

		
		$password = md5($new_password);
		$replacements['Password'] = $new_password;
		
		$replacements['Staff'] = '';
		if($staff){
			$replacements['Staff'] = $staff_message;
		}

		//Set properties to be returned
		$reset->replacements = $replacements;
		$reset->user_id = $user_id;
		$reset->full_name = $full_name;

		//Update password
		if(create_user_logins($database, $ifs, $username, $password, null, $user_login_id)){
			$reset->message->other[] = 'The account has been reset. An e-mail be sent to you to notify you of your new password';
			$reset->status = true;
		} else {
			$reset->message->other[] = 'The account could not be reset. Please contact the adminitrator';
			$reset->status = false;
		}

		return $reset;
	}

	/**
	* Update User Profile
	**/
	function update_user_profile($database, $ifs, $user_id = null, $user_login_id = null, 
															 $client_company_id = null, $pass_hashed = null, $address_id = null,
															 $type_id = null, $brand_project_id = null){
		$update_success = TRUE;
		$message = new StdClass;

		$message->other = array();
		$message->fields = array();

		$update_fields = TRUE;
		if((!$user_id && !$user_login_id) || $client_company_id){
			$update_fields = FALSE;
		}		
		
		/* Validate First Name */
		if(isset($_POST['name_first']) && $_POST['name_first']){
			$name_first = strip_tags(mysql_real_escape_string($_POST['name_first']));
		} else {
			//Display message that this field needs to be filled in
			$message->fields['name_first'] = 'First Name';
			$update_success = FALSE;
		}
		
		/* Validate Last Name */
		if(isset($_POST['name_last']) && $_POST['name_last']){
			$name_last = strip_tags(mysql_real_escape_string($_POST['name_last']));
		} else {
			//Display message that this field needs to be filled in
			$message->fields['name_last'] = 'Last Name';
			$update_success = FALSE;
		}

		/* Validate Email */
		if(isset($_POST['email']) && $_POST['email']){
			$email = strip_tags(mysql_real_escape_string($_POST['email']));
		} else {
			//Display message that this field needs to be filled in
			$message->fields['email'] = 'Email';
			$update_success = FALSE;
		}

        $uses_landline=NULL;
        if(isset($_POST['uses_landline']))
            $uses_landline = htmlentities(mysql_real_escape_string($_POST['uses_landline']));

		/* Validate Mobile */
        $mobile=null;
		if(isset($_POST['mobile']) && $_POST['mobile']){
			$mobile = strip_tags(mysql_real_escape_string($_POST['mobile']));
		} else if(!($uses_landline)){
			//Display message that this field needs to be filled in
			$message->fields['mobile'] = 'Mobile';
			$update_success = FALSE;
		}

        /* Validate Landline */
        $phone=null;
        if(isset($_POST['phone']) && $_POST['phone']){
            $phone = strip_tags(mysql_real_escape_string($_POST['phone']));
        } else if($uses_landline){
            //Display message that this field needs to be filled in
            $message->fields['phone'] = 'Phone';
            $update_success = FALSE;
        }

		$gender = NULL;
		$avatar_gender = NULL;

		/* Set Gender */
		if(isset($_POST['gender'])){
			$gender_value = strtolower($_POST['gender']);
			$gender = strip_tags(mysql_real_escape_string(ucwords($gender_value)));

			/* Check gender and set the appropriate variable */			
			switch($gender_value){
				case 'male':
					$avatar_gender = '0:4:0:0:0:0';
				break;
				case 'female':
					$avatar_gender = '0:4:6:6:5:0';
				break;
			}
		}

		/* Set job title */
		$job_title = '';
		if(isset($_POST['job_title']) && $_POST['job_title']){
			$job_title = strip_tags(mysql_real_escape_string($_POST['job_title'])); //job title

			//Test if job_title should not be set
			if($job_title == 'N/A'){
				$job_title = '';
			}
		} else {
			//Display message that this field needs to be filled in
			$message->fields['job_title'] = 'Job Title';
			$update_success = FALSE;
		}

		//Set Fax
		$fax = NULL;
		if(isset($_POST['fax'])){
			$fax = strip_tags(mysql_real_escape_string($_POST['fax']));	//fax	
		}

		//Set Role
		$role = NULL;
		if(isset($_POST['role'])){
			$role = strip_tags(mysql_real_escape_string($_POST['role']));	//role	
		}


		$created = date('Y-m-d H:i:s'); //created		

		/* Update or insert user data */		
		$updated_password = false;
		if($update_success){
			$username = $email; //username

			/* Update password */
			if((isset($_POST['oldpassword']) || isset($_POST['newpassword']) || isset($_POST['confirmpassword']))){
				$old_password = strip_tags(mysql_real_escape_string($_POST['oldpassword']));
				$new_password = strip_tags(mysql_real_escape_string($_POST['newpassword']));
				$confirm_password = strip_tags(mysql_real_escape_string($_POST['confirmpassword']));

				$updated_password = update_profile_password($database, $ifs, $username, $user_login_id, $old_password, $new_password, $confirm_password, $message);
				
				if($updated_password){
					/* Either update the error messages or the password */
					if(!is_string($updated_password)){
						$update_success = FALSE;
						$message = $updated_password;
					}	else {
						$pass_hashed = $updated_password;
					}
				}	
			}

			if($update_success){
				mysql_select_db($database, $ifs);

				if($update_fields){
					//we update users info (users table)					
					$updateSQL4 = sprintf(
						"UPDATE users SET name_first='$name_first', name_last='$name_last', email='$email',%s%s phone='$phone', fax='$fax', mobile= '$mobile', updated='$created' WHERE id= $user_id", 
						($gender ? ' Gender="' . $gender . '", avatar_info="' . $avatar_gender . '",' : ''),
                        (is_null($uses_landline) ? '': ' uses_landline=' . $uses_landline.',' ),
						($job_title ? ' job_title="' . $job_title . '",' : '')
					);
				} else {
					/* Insert User Login Details */
					$updateSQL4 = sprintf(
						"INSERT INTO users(name_first, name_last, email,%s%s%s phone, mobile, fax, created) VALUES ('".$name_first."','".$name_last."','".$email."',%s%s%s '".$phone."', '".$mobile."', '".$fax."','".$created."')",
						($gender ? ' Gender, avatar_info,' : ''),
						($job_title ? ' job_title,' : ''),
                        (is_null($uses_landline) ? '': ' uses_landline,'),
						($gender ? "'" . $gender . "', '" . $avatar_gender . "'," : ''),
						($job_title ? "'" . $job_title . "'," : ''),
                        (is_null($uses_landline) ? '':"'" . $uses_landline . "',")
					);
				}
				
				$Result4 = mysql_query($updateSQL4, $ifs);

				if(!$user_id){
					/* Get user id and user login id */
					$user_id = mysql_insert_id($ifs);
				}

				$password = 'Your Password';
				//Update password if pass hashed argument is set
				if($pass_hashed && !$updated_password){
					/* If pass_hashed contains both the clear and hashed password */
					if(is_array($pass_hashed)){
						//Clear Password
						if(isset($pass_hashed['password'])){
							$password = $pass_hashed['password'];
						}

						//Hashed Password
						if(isset($pass_hashed['hashed'])){
							$pass_hashed = $pass_hashed['hashed'];
						}
					}

					if(is_string($pass_hashed)){
						$user_login_id = create_user_logins($database, $ifs, $username, $pass_hashed, $user_id, $user_login_id);

						/* Make sure the user was created correctly */
						if(!$user_login_id || !is_numeric($user_login_id)){
							$message->other[] = 'The user login information could not be saved';
							$update_success = FALSE;
						}
					}
				}

				if($client_company_id){ 					
					if(!$user_login_id || !is_numeric($user_login_id)){
						//Do nothing
					} else {
						/* Insert client user */
						$insert3SQL = sprintf(
							"INSERT INTO 
								client_users 
								(client_company_id, user_id, created, active%s%s) 
							VALUES (%d, %d, '%s', 1%s%s)",
							($type_id ? ', type_id' : ''),
							($brand_project_id && $type_id ? ', bpid' : ''),
							$client_company_id,
							$user_id,
							$created,
							($type_id ? ', ' . $type_id : ''),
							($brand_project_id && $type_id ? ', ' . $brand_project_id : '')
						);

						$client_insert = mysql_query($insert3SQL, $ifs);

						if(!$client_insert){
							$message->other[] = 'The client information could not be saved';
							$update_success = FALSE; //update not successful
						}
					}
				}	

				if(!$Result4 || !$update_success){
					$message->other[] = 'The update was not successful';
					$update_success = FALSE;
				} else {
					//Return user id if available
					if($user_id){
						return $user_id;
					} else {
						return TRUE;
					}
				}
			}	
		}

		/* If messages are not empty process message */
		$message_return = array();
		$message_return['fields'] = array_keys($message->fields);
		$message_return['html'] = process_messages($message);

		return $message_return;
	}

	/**
	* Get Client Company Name
	**/
	function get_client_company_details($database, $ifs, $client_company_id){
		if(!$client_company_id){
			return FALSE;
		}

		//retrieve company name
		mysql_select_db($database, $ifs);
		$query_retcompany_name = "
		SELECT 
		  client_companies.name AS company_name,
		  client_companies.start_date,
		  client_companies.end_date,
		  addresses.post_code,
		  addresses.suburb,
		  addresses.state,
		  addresses.street,
		  country_lookup.country_name
		FROM
		  client_companies
		  INNER JOIN addresses ON (client_companies.address_id = addresses.id)
		  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id)
		WHERE
		  client_companies.id=$client_company_id";

		$retcompany_name = mysql_query($query_retcompany_name, $ifs) or die(mysql_error());
		
		//initialise total num of results
		$totalRows_retcompany_name = 0;

		//If query was successful
		if($retcompany_name){
			$totalRows_retcompany_name = mysql_num_rows($retcompany_name);
		}

		if($totalRows_retcompany_name){
			return $retcompany_name;
		} else {
			return FALSE;
		}
	}

	/**
	* Update User Address id
	**/
	function update_user_address_id($database, $ifs, $user_id, $address_id){
		//Ensure that both the user id and address id is included
		if(!$user_id || !$address_id){
			return FALSE;
		}

		mysql_select_db($database, $ifs);

		//Update address id for user through sql
		$address_query = sprintf(
			"UPDATE
				users
			SET 
				address_id = %d
			WHERE
				id = %d",
			$address_id,
			$user_id
		);

		$results = mysql_query($address_query, $ifs); //run query

		return $results; //return boolean
	}

	/**
	* Update profile password
	**/
	function update_profile_password($database, $ifs, $username, $user_login_id, $old_password, $new_password, $confirm_password, $message = null){
		$pass_hashed = NULL;
		$update_success = TRUE;

		/* Initialise message variables */
		if(!$message){
			$message = new StdClass;
			$message->other = array();
			$message->fields = array();
		}

		/* Make sure the password matches */			
		if($old_password && $new_password && $confirm_password){
			if($new_password != $confirm_password){				
				$message->other[] = 'The new password and the password confirmation didn\'t match';
				$update_success = FALSE;
			} else {				
				/* Make sure the password features more than 6 characters */
				if(str_word_count($new_password) > 6){
					$message->other[] = 'The password featured more than 6 characters';
					$update_success = FALSE;

				} elseif(check_password($database, $ifs, $old_password, $user_login_id)){ //validate password
					$pass_hashed = md5($new_password); //hash password

					//update passowrd
					$updated_password = create_user_logins($database, $ifs, $username, $pass_hashed, null, $user_login_id);
				}
			}
		} else {
			//If New Password is one of the missing fields
			if(!$new_password && ($confirm_password || $old_password)){
				$message->fields['newpassword'] = 'New Password';
				$update_success = FALSE;				
			}

			//If Confirm Password is one of the missing fields
			if(!$confirm_password && ($new_password || $old_password)) {
				$message->fields['confirmpassword'] = 'Confirm Password';
				$update_success = FALSE;
			}

			//If Old Password is one of the missing fields
			if(!$old_password && ($confirm_password || $new_password)) {
				$message->fields['oldpassword'] = 'Old Password';
				$update_success = FALSE;
			}
		}

		/* Either return an error message or the new password */
		if(!$update_success){
			return $message;
		} elseif($pass_hashed) {
			return $pass_hashed;
		} else {
			return FALSE; //nothing is wrong, just no password was set 
		}
	}

	/**
	* Process Messages
	**/
	function process_messages($message){
		$output = '';

		/* Neither fields or other messages are set */
		if(!isset($message->fields) && !isset($message->other)){
			return $output;
		}

		/* Other messages */
		if(isset($message->other) && !empty($message->other)){
			$other = $message->other; //other messages

			//Set as array if not array
			if(!is_array($other)){
				$other = array($other);
			}
			
			foreach($other as $mes){
				$output .= '<p>' . nl2br($mes) . '</p>' . "\n";
			}

		}

		/* Fields */
		if(isset($message->fields) && !empty($message->fields)){
			/* Emphasize fields */
			$field_val = array();
			foreach($message->fields as $key=>$field){
				if(isset($message->error_types[$key])){
					
					//Display other errors
					switch($message->error_types[$key]){
						case 'less':
							$output .= '<p><em>' . $field . '</em> is less than the <em>Company Agreement Start Date</em> and needs to be set between the Company Agreement Start Date and Company Agreement End Date</p>'; 
						break;
						case 'exceed':
							$output .= '<p><em>' . $field . '</em> exceeds the <em>Company Agreement End Date</em> and needs to be set between the Company Agreement Start Date and Company Agreement End Date</p>'; 
						break;
					}					
				} else {
					$field_val[] = '<em>' . $field . '</em>';
				}
			}

			$field_val_num = count($field_val);

			//If there are fields remaining to be processed
			if($field_val_num){
				if($field_val_num > 1){
					$field_val[$field_val_num - 1] = 'and ' . $field_val[$field_val_num - 1]; //include and in last one
				}

				$all_fields = implode(($field_val_num == 2 ? ' ' : ', '), $field_val);

				$output .= '<p>' . $all_fields  . ($field_val_num == 1 ? ' is' : ' are') . ' required and needs to be filled in</p>' . "\n";
			}			
		}

		return $output;	
	}

	/**
	* Prepare notification and revert to page
	**/
	function prepare_messages_and_revert_to_previous($notification, $goto){
		$_SESSION['notification'] = $notification;

		header(sprintf("Location: %s", $goto));
	}

	/**
	* Find specific participant
	**/
	function find_participant($database, $ifs, $id, $first_login = false, $lists = false, $user_id = NULL, $brand_project_id = NULL){
		//retrieve receiver details	
		mysql_select_db($database, $ifs);

		$query_value = '';		

		//If neither participant id or user id is available
		if(!$id && !$user_id && !$brand_project_id){
			return FALSE;
		} elseif($id) {
			/* Check if not array */
			if(!is_array($id)){
				$id = array($id);	
			}

			$query_value = implode(', ', $id);
		}

		/* Query */
		$sql = 
			"SELECT 
			  users.*,
			  addresses.*,
			  participants.id AS `part_id`,
			  participants.*" .
			  (!$first_login ? ", user_logins.username" : "") .
			  ($lists ? ", lists.id AS `list_id`" : "") .
			" FROM
			  users
			  INNER JOIN addresses ON (users.address_id = addresses.id)
			  INNER JOIN participants ON (users.id = participants.user_id)" .
			  (!$first_login ? " INNER JOIN user_logins ON (users.user_login_id = user_logins.id)" : "") .
			  ($lists ? " INNER JOIN participant_lists AS `lists` ON (participants.id = lists.participant_id)" : "");
				
		//If looking for specific user
		if(!$brand_project_id){				  
			$sql .= " WHERE " . (!$user_id ? "participants.id IN(%s)" : "user_id = %d");
			
			$query_participant = sprintf($sql, (!$user_id ? $query_value : $user_id));
		} else { //filter by brand project
			$sql .= " WHERE participants.brand_project_id = " . $brand_project_id;	
			
			$query_participant = $sql;
		}		

		/* Check result */
		$result_participant = mysql_query($query_participant, $ifs);

		if($result_participant && mysql_num_rows($result_participant) > 0){
			return $result_participant;
		} else {
			$mysql_error = mysql_error();
			return ($mysql_error ? $mysql_error : FALSE);
		}
	}

	/**
	* Find Comments of participant
	**/
	function find_participant_comments($database, $ifs, $participant_lists_id){
		if(!$participant_lists_id){
			return FALSE;			
		}

		mysql_select_db($database, $ifs); //set database

		$comments_query = sprintf(
			"SELECT 
				comments
			FROM
				participant_lists
			WHERE
				id = %d 
			",
			$participant_lists_id
		);

		$result = mysql_query($comments_query, $ifs);

		/* Return Comments */
		if($result){
			if(!mysql_num_rows($result)){
				return FALSE;
			}

			return $result;
		} else {
			$mysql_error = mysql_error();
			return ($mysql_error ? $mysql_error : FALSE);
		}
	}

	/**
	* Determine Role information
	**/
	function determine_role_information($database, $ifs, $staff_result){
		require_once('models/array_repository_model.php');

		$staff_info = array();

		//Go through the staff information to gather what roles the staff has
		while($row = mysql_fetch_array($staff_result, MYSQL_ASSOC)) {
			//Make sure that client user id was set
			if(!isset($row['client_user_id'])){
				continue;
			}

			$client_user_id = $row['client_user_id'];
			$client_company_id = $row['client_company_id'];
			$type_id = $row['type_id'];
			$active = $row['active'];

			if(!isset($staff_info[$client_user_id])){
				$staff_info[$client_user_id] = new StdClass;
			}

			$staff_info[$client_user_id]->company_id = $client_company_id;

			//Check if the user information has been set
			if(!isset($staff_info[$client_user_id]->user)){
				$staff_info[$client_user_id]->user = $row;
			}		

			//Check if user has the global admin role
			if($type_id == 1){
				$staff_info[$client_user_id]->global_admin = $active;
			}

			//Check the session staff information
			$sessionMod = retrieve_session_moderator($database, $ifs, $row['user_id']);
			if(!empty($sessionMod)){
				$sessionMod_results = prepare_foreach($sessionMod);
				
				//Go through the list and find which roles where set for the user
				if($sessionMod_results){
					foreach($sessionMod_results as $staff){
						$staff_type_id = $staff['type_id'];
						$staff_active = $staff['active'];
						$staff_session = $staff['session_id'];

						if(!$staff_info[$client_user_id]->user['type_id']){
							$staff_info[$client_user_id]->user['type_id'] = $staff_type_id;
						}

						//Check if the type array has been set
						if(!isset($staff_info[$client_user_id]->types)){
							$staff_info[$client_user_id]->types = array();
						}

						//Check if the particular type information has been set
						if(!isset($staff_info[$client_user_id]->types[$staff_type_id])){
							$staff_info[$client_user_id]->types[$staff_type_id] = array();
						}

						//set role and active status for the session
						$staff_info[$client_user_id]->types[$staff_type_id][$staff_session] = $staff_active;
					}
				}
			}
		} //end while

		return $staff_info;
	}

	/**
	* Update that green room has been visited before
	**/
	function update_green_room_visit_status($database, $ifs, $user_id){
		if(!$user_id){
			return FALSE;			
		}

		mysql_select_db($database, $ifs); //set database

		$visited_update = sprintf(
			"UPDATE
				users
			SET
				green_room_visit = 1
			WHERE
				id = %d
			",
			$user_id
		);

		$result = mysql_query($visited_update, $ifs);

		/* Return Comments */
		if($result){
			return $result;
		} else {
			$mysql_error = mysql_error();
			return ($mysql_error ? $mysql_error : FALSE);
		}
	}

	function retrieve_brand_projects_moderator($database, $ifs, $id){
		mysql_select_db($database, $ifs);

		$sql = 
			"SELECT 
			 *
			FROM
			  brand_projects
			WHERE
			  moderator_user_id = %d";

		$query_brand_projects = sprintf($sql, $id);
	  
		$result_brand_projects = mysql_query($query_brand_projects, $ifs);
		if($result_brand_projects && mysql_num_rows($result_brand_projects) > 0){
			return $result_brand_projects;
		} else {
			return mysql_error();
		}
	}

	function retrieve_session_moderator($database, $ifs, $id){
		mysql_select_db($database, $ifs);

		$sql = 
			"SELECT 
			  *
			FROM
			  session_staff
			WHERE
			  user_id = %d";

		$query_session_staff = sprintf($sql, $id);
	  
		$result_session_staff = mysql_query($query_session_staff);
		if($result_session_staff && mysql_num_rows($result_session_staff) > 0){
			return $result_session_staff;
		} else {
			return mysql_error();
		}
	}

	/**
	* Update Active status
	**/
	function set_client_user_active_deactivated($database, $ifs, $user_id, $type_id, $active_status = 1, $session_id = null){
		mysql_select_db($database, $ifs);

		/* Set default values */
		$table = 'client_users';
		$active_column = 'active';
		$user_id_column = 'user_id';
		$session_cond = null;
		
		/* Find the correct table to update */
		if($type_id > 1 && $type_id < 5){ //session_staff
			$table = 'session_staff';
			$session_cond = 'session_id = ' . $session_id;
		} elseif($type_id == 5){ //moderator
			$table = 'brand_projects';
			$active_column = 'moderator_active';
			$user_id_column = 'moderator_user_id';
		}

		$active_update = sprintf(
			"UPDATE
				%s
			SET
				%s = %d
			WHERE
				%s = %d%s",
			$table,
			$active_column,
			$active_status,
			$user_id_column,
			$user_id,
			($session_cond ? ' AND ' . $session_cond : '')
		);

		$result = mysql_query($active_update, $ifs); //update

		/* Return Comments */
		if($result){
			return $result;
		} else {
			$mysql_error = mysql_error();
			return ($mysql_error ? $mysql_error : FALSE);
		}
	}
	