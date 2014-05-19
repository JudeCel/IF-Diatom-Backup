<?php
	require_once('Connections/ifs.php');	

	$help_content = new StdClass;
	//Settings
	$user_type = null;
	if(isset($_SESSION['MM_UserTypeId'])){
		$user_type = $_SESSION['MM_UserTypeId'];
	}

	//Different pages
	$help_content->front = array(
		"This page gives you access to the different sections that are available, which are
		presented in tables. If one of the tables are collapsed, you open it by clicking on the
		top-right button positioned in the header of the tables visible to you.",

		"Once you've opened a table and you would like to explore that particular section further,
		click the configure button. If you don't notice the button, then you might be on a mobile device.
		You can still access the data by scrolling or dragging the tablet content either to the left or the
		right.",

		"The buttons in the footer of the table allows you to search the for a specific item, to collapse
		any fields that you dont want to see or need and to browse any additional content that may remain."
	);

	$help_content->companies = array(
		"The Companies page allows you to manage any Companies that you might be involved in and provides
		you with any additonal information that you might need.",

		"The pages in the section allows you to" . ($user_type <= 1 ? " create new companies," : "") . 
		" assign Global Administrators, and view" . ($user_type <= 1 ? ' and create new' : ' your') . 
		" brand projects."
	);

	$help_content->brand_projects = array(
		"The Brand Projects page displays the list of current brand projects that you are involved
		in and that you have the privilige to manage.",

		"This section allows you to create new and edit existing sessions, import or export participants,
		view analytics concerning the participants of the bramnd project, and create and manage
		observers."
	);

	$help_content->sessions = array(
		"The Session page presents the sessions you're involved in and allows you to access the Green
		Room, view the Chatroom, manage session emails and Green Room content, assign Observers to particular
		sessions and create intersting topics for people to think about and discuss."
	);

	$help_content->admin = array(
		'This page displays all the different administrators, including Global Admin and Facilitators.',
		'Here you can activate/deactivate, delete and edit users, as well as change their passwords'
	);	

	$page_help = 'front';
	if(isset($_GET['page'])){
		$page_help = strip_tags(mysql_real_escape_string($_GET['page']));

		//Ensure that content is available
		if(!isset($help_content->$page_help)){
			$page_help = 'front';
		}
	}

	//Page properties
	$page = 'Help';
	$heading = ucwords(str_replace('_', ' ', $page_help));
	$title = $page . ' | ' . $heading;
	$main_script = false;
	$other_content = 'help';
	$validate = false;
	$inline_scripting = false;	

	$content = $help_content->$page_help;

	include('views/popup.php');

