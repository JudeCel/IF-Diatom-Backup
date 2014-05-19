<?php
	/**
	* Retrieve Green Room Information
	**/
	function retrieve_green_room_information($database, $ifs, $session_id){
		//If no session id was saved
		if(!$session_id){
			return FALSE;
		}

		mysql_select_db($database, $ifs);

		//Find Session green room information
		$green_room_sql = sprintf(
			"SELECT
				*
			FROM
				green_room 
			WHERE
				session_id = %d
			",
			$session_id
		);

		$result = mysql_query($green_room_sql, $ifs);

		//Return result if rows found		
		if($result){
			if(mysql_num_rows($result) > 0){
				return $result;
			} else {
				return false;
			}
		} else {
			return (mysql_error() ? mysql_error() : FALSE); //return error - check for string
		}
	}

	/**
	* Update Green Room information
	**/
	function update_green_room($database, $ifs, $session_id, $update = true){
		//If no session id was saved
		if(!$session_id){
			return FALSE;
		}

		$created = date('Y-m-d H:i:s');
		$update_array = array();

		$update_array['session_id'] = $session_id;

		//Top Message
		if(isset($_POST['top_message'])){
			$update_array['top_message'] = "'" . cleanFromEditor($_POST['top_message']) . "'";
		}

		//Session Information
		if(isset($_POST['session_information'])){
			$update_array['session_information'] = "'" . cleanFromEditor($_POST['session_information']) . "'";
		}

		//Session Details
		if(isset($_POST['session_details'])){
			$update_array['session_details'] = "'" . cleanFromEditor($_POST['session_details']) . "'";
		}

		//Greeting
		if(isset($_POST['greeting'])){
			$update_array['greeting'] = "'" . cleanFromEditor($_POST['greeting']) . "'";
		}

		//Overview
		if(isset($_POST['overview'])){
			$update_array['overview'] = "'" . cleanFromEditor($_POST['overview']) . "'";
		}

		mysql_select_db($database, $ifs);

		if($update){
			/* Prepare update array to be used in the update query */
			$update_values = array();
			foreach($update_array as $key=>$value){
				$update_values[] = $key . '=' . $value;
			}

			/* Update query */
			$update_sql = sprintf(
				"UPDATE
					green_room
				SET
					%s
				WHERE
					session_id = %d",
				implode(', ', $update_values),
				$session_id
			);
		} else {
			/* Prepare update array to be used in insert sql */
			$update_keys = array_keys($update_array);
			$update_values = array_values($update_array);

			/* Insert query */
			$update_sql = sprintf(
				"INSERT INTO
					green_room (%s)
				VALUES (%s)",
				implode(', ', $update_keys),
				implode(', ', $update_values)
			);
		}

		$result = mysql_query($update_sql, $ifs);

		/* Return result */
		if($result){
			return TRUE;
		} else {
			return (mysql_error() ? mysql_error() : FALSE); //return error - check for string
		}
	}