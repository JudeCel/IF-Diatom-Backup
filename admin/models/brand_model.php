 <?php
	/**
	* Find brand projects for seesion
	**/
	function retrieve_brand_project($database, $ifs, $session_id = null){
		if(isset($_GET['session_id']) || $session_id){
			if(!$session_id){
				$session_id = strip_tags(mysql_real_escape_string($_GET['session_id']));
			}

			mysql_select_db($database, $ifs);
			
			/* Query for brand project */
			$query_retBrandProject = sprintf(
				"SELECT 
				  sessions.start_time,
				  sessions.end_time,
				  sessions.name AS `Session_Name`,
				  sessions.colours_used,
				  brand_projects.id,
				  brand_projects.name,
				  brand_projects.logo_url,
				  brand_projects.chatroom_logo_url,
				  brand_projects.client_company_id,
				  brand_projects.name AS `brand_project_name`,
				  sessions.incentive_details,
				  sessions.status_id,
				  session_staff.user_id,	
				  client_companies.name AS `client_company_name`,
				  client_companies.enable_chatroom_logo AS `enable_chatroom_logo`,
				  client_companies.id AS `client_company_id`			  
				FROM
				  sessions
				  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
				  INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
				  INNER JOIN client_companies ON(brand_projects.client_company_id = client_companies.id)
				WHERE
				  sessions.id = %d", $session_id);

			$retBrandProject = mysql_query($query_retBrandProject, $ifs);
			
			/* Find Brand projects */
			if($retBrandProject){
				if(mysql_num_rows($retBrandProject) > 0){
					return $retBrandProject;
				} else {
					return FALSE;
				}
			} else {
				return mysql_error();
			}
		}
	}

	/**
	* Retrieve List of Brand Projects for specific client company id
	**/
	function retrieve_brand_project_company($database, $ifs, $client_company_id = null, $where_cond = null, $properties = null, $sql = null){
		mysql_select_db($database, $ifs);

		$query_bp = get_bp_query($client_company_id, $where_cond);

		//Order and limit by properties
		if($properties){
			$query_bp = set_order_for_brand_query($query_bp, $properties);
		}

		if($sql){
			return $query_bp;
		}

		$result = mysql_query($query_bp, $ifs);

		if($result){
			//Return result if rows are found
			if(mysql_num_rows($result) > 0){
				return $result;
			} else {
				return FALSE;
			}
		} else {
			//Return mysql error if there are any
			$mysql_error = mysql_error();
			if($mysql_error){
				return $mysql_error;
			} else {
				return FALSE;
			}
		}
	}

	/**
	* Retrieve list of brand projects being used in sessions
	**/
	function retrieve_brand_project_sessions($database, $ifs, $client_company_id = null, $where_cond = null, $properties = null){
		mysql_select_db($database, $ifs);

		$query_bp = get_bp_query($client_company_id, $where_cond); //get base query
		$subquery = set_session_brand_project_subquery($client_company_id); //get subquery based on user id
		
		//Use subsquery item
		if(!$subquery){
			return FALSE;
		} else {
			$altered_query = sprintf(
				"%s
				AND brand_projects.id IN(%s)",
				$query_bp,
				$subquery
			);
		}

		//Order and limit by properties
		if($properties){
			$altered_query = set_order_for_brand_query($query_bp, $properties);
		}

		$result = mysql_query($altered_query, $ifs);

		if($result){
			//Return result if rows are found
			if(mysql_num_rows($result) > 0){
				return $result;
			} else {
				return FALSE;
			}
		} else {
			//Return mysql error if there are any
			$mysql_error = mysql_error();
			if($mysql_error){
				return $mysql_error;
			} else {
				return FALSE;
			}
		}
	}

	/**
	* Set order by for brand query
	**/
	function set_order_for_brand_query($bp_query, $properties){
		if(!$bp_query){
			return FALSE;
		}

		if(!$properties){
			return $bp_query;
		}

		$altered_query = sprintf(
			"%s
			%s
			%s",
			$bp_query,
			($properties && isset($properties['order']) ? 'ORDER BY ' . implode(' ', $properties['order']) : ''),
			($properties && isset($properties['limit']) ? 'LIMIT ' . implode(', ', $properties['limit']) : '')
		);

		return $altered_query;
	}

	/**
	* Subquery for brand projects query based on sessions
	**/
	function set_session_brand_project_subquery($client_company_id){
		//Return empty string if it is not a valid user_id
		if(!$client_company_id){
			return '';
		}

		$subquery = sprintf(
			"SELECT
				bp.id
			FROM
				brand_projects AS `bp`
				INNER JOIN sessions ON (bp.id = sessions.brand_project_id)
				INNER JOIN session_staff AS `staff` ON (staff.session_id = sessions.id)
			WHERE
				bp.client_company_id = %d",
			$client_company_id
		);

		return $subquery;
	}

	/**
	* Return query for brand projects for moderator user id
	**/
	function get_bp_query($client_company_id = null, $where_cond = null){
		/* Query for retrieving the brand project */
		$query_bp = sprintf(
			"SELECT
			  brand_projects.max_sessions,
			  brand_projects.name,
			  brand_projects.start_date,
 				brand_projects.end_date,
			  brand_projects.id as brand_project_id,
			  client_companies.name AS CompanyName,
			  brand_projects.created,
			  brand_projects.updated,
			  brand_projects.client_company_id
			FROM
				brand_projects
  			INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
  			%s
			",
			($client_company_id && $where_cond ? $where_cond : '') //check for client company id			
		);

		return $query_bp;
	}

	/**
	* Find Specific Brand Project without Sessions
	**/
	function get_only_brand_project($database, $ifs, $client_company_id, $brand_project_id){
		if($client_company_id && $brand_project_id){
			$where_cond = 'WHERE brand_project_id = ' . $brand_project_id;

			return retrieve_brand_project_company($database, $ifs, $client_company_id, $where_cond);
		} else {
			return FALSE;
		}
	}


	/**
	* Find brand prject addresses
	**/
	function retrieve_brand_projects_addresses($database, $ifs, $brand_project_id = null){
		$filename=basename($_SERVER['PHP_SELF']);

		if($brand_project_id){
			//// Address details based on brand project
			mysql_select_db($database, $ifs);
			$query_retAddress = sprintf(
				"SELECT 
				  brand_projects.name AS Brand_Project_Name,
				  addresses.street,
				  addresses.suburb,
				  addresses.state,
				  addresses.post_code,
				  country_lookup.country_name
				FROM
				  brand_projects
				  INNER JOIN client_companies ON (brand_projects.client_company_id = client_companies.id)
				  INNER JOIN addresses ON (client_companies.address_id = addresses.id)
				  INNER JOIN country_lookup ON (addresses.country_id = country_lookup.id) 
				WHERE
				  brand_projects.id = %d", $brand_project_id);

			$retAddress = mysql_query($query_retAddress, $ifs);

			if($retAddress && mysql_num_rows($retAddress) > 0){
				return $retAddress;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	* Get Company Name
	**/
	function get_company_name($database, $ifs, $company_id){
		if($company_id){
			mysql_select_db($database, $ifs); //select database

			//Find conpany name for a specific company id
			$company_name_sql = sprintf(
				"SELECT 
					name
				FROM 
					client_companies
				WHERE
					id = %d",
					$company_id
			);

			$result_company = mysql_query($company_name_sql, $ifs);

			//Check if company was found
			if($result_company){
				if(mysql_num_rows($result_company) > 0){
					return $result_company;
				} else {
					return FALSE;
				}
			} else {
				//If there was an error, return it
				$error = mysql_error();

				return $error;
			}
		} else { //No company id set
			return FALSE;
		}
	}