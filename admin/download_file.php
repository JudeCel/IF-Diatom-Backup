<?php
	if(!isset($_GET['file'])){
		header("location: index.php");
	}

	//determine website path
	$path = $_SERVER['REQUEST_URI'];
	$root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

	//check if ifs-test is needed
	if(preg_match('/^\/ifs-test/', $path)){
		$root .= '/ifs-test';
	}

	//set the file
	$file = $root . '/' . strip_tags($_GET['file']);

 	//force download of file
 	header("Content-type: application/force-download");
 	header("Content-disposition: attachment; filename=\"".basename($file)."\"");
 	readfile($file);