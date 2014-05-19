<?php

ob_start();
$filename = basename($_SERVER['PHP_SELF']);

function admin($database, $ifs){				
	if(isset($_SESSION['MM_Username']) && isset($_SESSION['MM_FirstName']) && isset($_SESSION['MM_UserTypeId'])){
		if(!empty($_SESSION['MM_Username']) && !empty($_SESSION['MM_FirstName']) && !empty($_SESSION['MM_UserTypeId'])){			
				return true;			
		}else{
			return false;
		}
	}	
}


function menu($database, $ifs)
{
	//The Pages that are in specific menus
	$menu_pages = new stdClass;
	$menu_pages->companies = array(
		'signup.php',
		'clientCompanyUsers.php',
		'newBrandProject.php'
	);
	$menu_pages->brand_projects = array(
		'newSession.php',
		'participantPanel.php',
		'participantPanel-reports.php',
		'bp_observers.php'
	);
	$menu_pages->sessions = array(
		'session-emails.php',
		'session-greenroom.php',
		'session-participants-jqgrid.php',
		'session-edit.php',
		'newTopic.php'
	);

	if($_SESSION['MM_UserTypeId'] == 1 || $_SESSION['MM_UserTypeId']  == 2 || $_SESSION['MM_UserTypeId'] == 3 || $_SESSION['MM_UserTypeId'] == 4 ||  $_SESSION['MM_UserTypeId'] == 5){
		$session = $_SESSION['MM_UserTypeId'];			
		mysql_select_db($database, $ifs);

		//Specify which menu items to exclude
		$disallowed = array(
			1 => array(
				5 //Participants
			),
			2 => array(
				5 //Participants
			),
			3 => array(
				5 //Participants
			)
		);

		$menu_query = sprintf(
			"SELECT 
				user_menu.id,
				menu_lookup.menu_name,
				menu_lookup.URL
			FROM
			  menu_lookup
			INNER JOIN user_menu ON (menu_lookup.id = user_menu.menu_id)
			INNER JOIN user_type_lookup ON (user_menu.user_type_id = user_type_lookup.id)
			WHERE
				user_type_lookup.id = '%d'%s",
			$session,
			(!empty($disallowed) && isset($disallowed[$session]) ? " AND menu_lookup.id NOT IN(" . implode(', ', $disallowed[$session]) . ")" : "") //specify which items not to show
		);
	} elseif($_SESSION['MM_UserTypeId'] == -1){				
		mysql_select_db($database, $ifs);

		//Specify which items should not be included
		$disallowed = array(
			5, //Participants
			8 //Reporting
		);

		$menu_query = sprintf(
			"SELECT 
				menu_lookup.menu_name,
				menu_lookup.URL
			FROM
		  	menu_lookup%s",
		  (!empty($disallowed) ? " WHERE id NOT IN(" . implode(', ', $disallowed) . ")" : "") //specify which items not to show
		);		
	}
	
	$retCompany = mysql_query($menu_query, $ifs) or die(mysql_error());
	$totalRows_retCompany = 0;	

	//If menu was found
	if($retCompany){
		$totalRows_retCompany = mysql_num_rows($retCompany);
		$num = 1;
		
		$root = $_SERVER['REQUEST_URI'];
		//Check if root includes ifs-dev
		if(preg_match('/^\/ifs-test/', $root)){
			$root = str_replace('/ifs-test/', '', $root);
		} else {
			$root = str_replace('/', '', $root);
		}
		
		//Set the path of the page
		$path = $root;
		if(!$path){ //Since there are no arguments, it is logical to assume that this is index.php
			$path = 'index.php';
		}		
			
		echo'<ul class="cf">' . "\n";
	
		//Loop through menu and create buttons
		while($row_retCompany = mysql_fetch_assoc($retCompany)){				
			$menu_class = '';
			
			//If the number of menu items rendered matches the amount of menus available
			if($num == $totalRows_retCompany){
				$menu_class .= 'last';
			}

			//If the path of the page matches the menu url
			if($row_retCompany['URL'] == $path){
				$menu_class .= ($menu_class ? ' ' : '') . 'active';
			} else {
				/* Go through the menu pages to see if this is one of the children */
				$page_reference = str_replace(' ', '_', strtolower($row_retCompany['menu_name']));

				if(isset($menu_pages->$page_reference)){
					$menu_page = $menu_pages->$page_reference;

					//Check if the path matches
					foreach($menu_page as $page){
						if(preg_match('/' . $page . '/', $path)){
							$menu_class .= ($menu_class ? ' ' : '') . 'active';

							break; //stop looping
						}
					}
				}

			}

			//Match url
			$url_name = 'home_menu';
			if($row_retCompany['URL']){ //remove extension from url to find path name
				$url_name = strtolower(preg_replace('/.php|.html/', '', $row_retCompany['URL']));

				//set name of front page to home
				if($url_name == 'index'){
					$url_name = 'home';
				}

				//Add the menu suffix
				$url_name .= '_menu';
			}

			echo "<li" . ($menu_class ? ' class="' . $menu_class . '"' : '') . '>' . "\n";
			echo '<a id="' . $url_name . '" href = "' . $row_retCompany['URL'] . '">' . $row_retCompany['menu_name'] . '</a>' . "\n";
			echo '</li>' . "\n";

			$num++;				
		}
		echo '</ul>';
	}
}
			
function logToFile($filename,$msg, $log = false) 
{
   if (!isset($msg)) {
	   $msg = "<<msg wasn't set>>";
   }

   if ($log == true) {
	  // open file
	  $dir = "logs/";
	  $filename = $filename.".txt";	   
	   
	  $fd = fopen($dir.$filename, "a");
		
		$msg="---------".date("d-m-Y H:i:s")."---------"."\n".$msg."---------"."\n\n";
		
	  // write string
	  fwrite($fd, $msg."\n"); 

	  // close file
	  fclose($fd);
   }
}		
			
function sendMail($database, $ifs, $to, $name, $subject, $message, $from, $user_id)
{
	$filename=basename($_SERVER['PHP_SELF']);

	//imp to send from whatever email account we want
	$headers  = 'MIME-Version: 1.0' . "\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
	$headers .= "From: $from" . "\n";
	$headers .= "Reply-To: $from" . "\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();
	
	if(mail($to, $subject,$message,$headers)){		
		if($user_id){
			$sent = date('Y-m-d H:i:s'); 
			
			$receiver = $to;
			$sender = $from;
			$sub = mysql_real_escape_string($subject);
			
			$escape =strip_tags($message);
			$body =  mysql_real_escape_string($escape); 
			
			$save_email = sprintf("INSERT INTO email_log (user_id, sent_on, receiver, subject, message, sender) VALUES ($user_id, '$sent', '$receiver', '$sub','$body', '$sender')");
			
			mysql_select_db($database, $ifs);
			$Result3 = mysql_query($save_email, $ifs) or die(mysql_error());
		}

		return TRUE;			
	} 
	else{ 
		echo "there's some errors to send the mail, verify your server options";

		return FALSE;
	} 
}
	
	
	
	
	///// validation function for the brand project id which has been sent by the get method
	
function brandproject($database, $ifs, $brand_project_id){
	/////// creating query for the Gloabal Admin
	if($_SESSION['MM_UserTypeId'] == 1){
		$query_modid = " ";
	}else{
		$query_modid = "brand_projects.moderator_user_id = '".mysql_real_escape_string($_SESSION['MM_UserId'])."'   AND";
	}

	//retrieve the BP id authorisation
	mysql_select_db($database, $ifs);

	$query_retBPid = "SELECT 
	  brand_projects.id,
	  brand_projects.client_company_id
	FROM
	  brand_projects
	  INNER JOIN users ON (brand_projects.moderator_user_id = users.id)
	WHERE
		".$query_modid."
	  brand_projects.id = '".@mysql_real_escape_string($brand_project_id)."'   AND
	  brand_projects.client_company_id = '".mysql_real_escape_string($_SESSION['MM_CompanyId'])."'";

	$retBPid = mysql_query($query_retBPid, $ifs) or die(mysql_error());
	$row_retBPid = mysql_fetch_assoc($retBPid);
	$totalRows_retBPid = mysql_num_rows($retBPid);

	//// function which check number of the row from query
	if($totalRows_retBPid >0){
		return true;
	}else{
		return false;
	}
}


//function to clean the output of the nicEdit textarea
function cleanFromEditor($text) { 
  //try to decode html before we clean it then we submit to database
  $text = stripslashes(html_entity_decode($text));

  //clean out tags that we don't want in the text
  $text = strip_tags($text,'<p><div><strong><em><ul><ol><li><u><blockquote><br><sub><img><a><h1><h2><h3><span><b>');

  //conversion elements
  $conversion = array(
      '<br>'=>'<br />',
      '<b>'=>'<strong>',
      '</b>'=>'</strong>',
      '<i>'=>'<em>',
      '</i>'=>'</em>'
  );

  //clean up the old html with new
  foreach($conversion as $old=>$new){
      $text = str_replace($old, $new, $text);
  }   

  return htmlentities(mysql_real_escape_string($text));
} 



function sessionid($database, $ifs, $session_id){
		/////// creating query for the Gloabal Admin
	if($_SESSION['MM_UserTypeId'] == 1){
		$query_sessmodid = " ";
	}else if($_SESSION['MM_UserTypeId'] == 2){
		$query_sessmodid = "(brand_projects.moderator_user_id = '".mysql_real_escape_string($_SESSION['MM_UserId'])."' || session_staff.user_id = '".mysql_real_escape_string($_SESSION['MM_UserId'])."' AND 
						  session_staff.type_id IN (2,3))   AND";
	}else if($_SESSION['MM_UserTypeId'] == 3){
		$query_sessmodid = "session_staff.user_id = '".mysql_real_escape_string($_SESSION['MM_UserId'])."' AND 
						  session_staff.type_id IN (2,3) AND ";
	}

	//retrieve the BP id authorisation
	mysql_select_db($database, $ifs);
	$query_retSessionid = "SELECT 
	  session_staff.session_id,
	  sessions.brand_project_id,
	  brand_projects.client_company_id,
	  brand_projects.moderator_user_id,
	  session_staff.user_id
	FROM
	  sessions
	  INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
	  INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
	WHERE
	  session_staff.session_id = '".mysql_real_escape_string($session_id)."' AND 
	  ".@$query_sessmodid."
	  brand_projects.client_company_id = '".mysql_real_escape_string($_SESSION['MM_CompanyId'])."'";

	$retSessionid = mysql_query($query_retSessionid, $ifs) or die(mysql_error());
	$row_retSessionid = mysql_fetch_assoc($retSessionid);
	$totalRows_retSessionid = mysql_num_rows($retSessionid);

	//// function which check number of the row from query
	if($totalRows_retSessionid >0){
		return true;
	} else {
		return false;
	}
}