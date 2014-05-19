<?php
// initialization
require_once("Connections/ifs.php");
require_once('models/users_model.php');
require_once('models/brand_model.php');
require_once('models/participant-email-model.php');
require_once('models/array_repository_model.php');

if($_SESSION['MM_UserTypeId'] == 1 || $_SESSION['MM_UserTypeId'] == -1){
 	$page  = (isset($_GET['page']) ? strip_tags(mysql_real_escape_string($_GET['page'])) : NULL); // get the requested page
	$limit = (isset($_GET['rows']) ? strip_tags(mysql_real_escape_string($_GET['rows'])) : 0); // get how many rows we want to have into the grid
	$sidx  = (isset($_GET['sidx']) ? strip_tags(mysql_real_escape_string($_GET['sidx'])) : NULL); // get index row - i.e. user click to sort
	$sord  = (isset($_GET['sord']) ? strip_tags(mysql_real_escape_string($_GET['sord'])) : NULL); // get the direction

	$client_company_id = $_SESSION['MM_CompanyId']; //client company id

	if(!$sidx) {
	    $sidx = 'users.email';
	}

	$session = FALSE;
	if($_SESSION['MM_UserTypeId'] == 1){
		$session = TRUE;
	}

	$totalrows = isset($_GET['totalrows']) ? strip_tags(mysql_real_escape_string($_GET['totalrows'])): false;

	switch($sidx){
		case 'username':
			$sidx = 'users.email';
		break;
		case 'name':
			$sidx = 'users.name_first';
		break;
	}

	if ($limit <0) {
	  $limit = 0;
	}

	$start = $limit * $page - $limit;

	if ($start <0) {
	  $start = 0;
	}

	$result_count = retrieve_users_admin_page($database_ifs, $ifs);
	$result = retrieve_users_admin_page($database_ifs, $ifs, $sidx, $sord, $start, $limit);

	/* Find number of Staff */
	$count = 0;
	if($result_count && !is_string($result_count)){
		$count = mysql_num_rows($result_count);
	}

	// get the required variables
	if( $count > 0 && $limit) {
	  $total_pages = ceil($count / $limit);
	} else {
	   $total_pages = 0;
	}

	// create a response array from the obtained result
	$response = new StdClass;
	$response->page    = $page;
	$response->total   = $total_pages;
	$response->records = $count;
	$i= 0;

	//Get staff info
	$staff_info = determine_role_information($database_ifs, $ifs, $result);

	//Go through the staff info to diplay the information in the grid
	if(!empty($staff_info)){
		foreach($staff_info as $cid=>$role_info){
			$user = $role_info->user;

			//Check if the user is a global admin
			$admin = null;
			if(isset($role_info->global_admin)){
				$admin_active = $role_info->global_admin;

				$company_name = 'Company Name';

				//Ensure that there is usable client company id
				if($client_company_id == -1 || $role_info->company_id != $client_company_id){
					$client_company_id = $role_info->company_id;
				}

				//Attempt to find company name
				$company_name_result = get_company_name($database_ifs, $ifs, $client_company_id);
				if($company_name_result && !is_string($company_name_result)){
					$company_name_row = mysql_fetch_assoc($company_name_result);

					//Set Company Name
					$company_name = $company_name_row['name'];
				}

				$admin = '';

				//Set templates for the HTML
				$admin_checkbox = '<input type="checkbox" data-typeid="%4$d" name="global_admin_%1$d_%4$d" id="global_admin_%1$d_%4$d" value="%2$s"%3$s />';
				$admin_label = '<label for="global_admin_%1$d_%3$d">%2$s</label>';

				//print out the HTML
				$admin .= '<div class="checkboxes">' . "\n";
				$admin .= '<div class="checkboxes_inner">' . "\n";
				$admin .= sprintf($admin_checkbox, $user['user_id'], 1, ($admin_active ? ' checked="checked"' : ''), 1) . "\n";
				$admin .= sprintf($admin_label, $user['user_id'], $company_name, 1) . "\n";
				$admin .= '</div>';
				$admin .= '</div>';
			}

			//Set default values for different types
			$type_values = array(
				2 => null,
				4 => null
			);

			//Go through the types to find individual roles in the sessions
			if(isset($role_info->types) && !empty($role_info->types)){
				//Set the type id
				$type_names = array(
					2 => 'facilitator',
					4 => 'observer'
				);

				//Set templates for the HTML
				$session_checkbox = '<input type="checkbox" data-typeid="%6$d" data-sid="%5$d" name="%4$s_%1$d_%5$d_%6$d" id="%4$s_%1$s_%5$d_%6$d" value="%2$s"%3$s />';
				$session_label = '<label for="%3$s_%1$d_%4$d_%5$d">%2$s</label>';

				foreach($role_info->types as $type_id=>$sessions){
					$type_name = $type_names[$type_id];

					$type_values[$type_id] = '';

					foreach($sessions as $session_id=>$session_active){
						$session_name = 'Session Name';

						//Attemp to find session name
						$session_name_result = get_session_name($database_ifs, $ifs, $session_id);
						if($session_name_result && !is_string($session_name_result)){
							$session_name_row = mysql_fetch_assoc($session_name_result);

							//Set the Session Name
							$session_name = $session_name_row['name'];
						}

						$type_values[$type_id] .= '<div class="session">' . "\n";

						//print out the HTML
						$type_values[$type_id] .= '<div class="checkboxes">' . "\n";
						$type_values[$type_id] .= '<div class="checkboxes_inner">' . "\n";
						$type_values[$type_id] .= sprintf($session_checkbox, $user['user_id'], 1, ($session_active ? ' checked="checked"' : ''), $type_name, $session_id, $type_id) . "\n";
						$type_values[$type_id] .= sprintf($session_label, $user['user_id'], $session_name, $type_name, $session_id, $type_id) . "\n";
						$type_values[$type_id] .= '</div>' . "\n";
						$type_values[$type_id] .= '</div>' . "\n";

						$type_values[$type_id] .= '</div>';
					}
				}
			}

			if($user['type_id'] != 4){
				$user_roles = array(
					1 => 'Global Admin',
					2 => 'Facilitator',
					3 => 'Facilitator'
				);
				$role_name = 'User';
				if(isset($user_roles[$user['type_id']])){
					$role_name = $user_roles[$user['type_id']];
				}

				$name = $user['name_first'] . ' ' . $user['name_last'];
				$edit_user = '<a title="Edit User" class="editContact" href="clientCompanyUsers-edit.php?user_id=' . $user["user_id"] . '&staff=1"><span class="ui-icon ui-icon-pencil"></span></a>';
				$delete_user = '<a title="Delete ' . $role_name . '" href="StaffDelete.php?user_id=' . $user['user_id'] . ($user['type_id'] == 1 ? '&client_user_id=' . $user["client_user_id"] : '') . '"><span class="ui-icon delete"></span></a>';

				//Set the response value
				$example="<a title='Reset Password'  href='reset_password.php?user_login_id=".$user['id']."'><span class='ui-icon ui-icon-folder-open'></span></a>";

			  $response->rows[$i]['id']   = $user['client_user_id'];
			  $response->rows[$i]['cell'] = array($user['client_user_id'], $user['user_id'], $user['id'], $user['username'], $name, $admin , $type_values[2], $edit_user, $delete_user);
			  $i++;
			}
		}
	}

	// convert the response into JSON representation
	echo json_encode($response);
}

mysql_close($ifs);