<?php

//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

//Set notification if needed
$notification = NULL;
if(isset($_SESSION['notification'])){
  $notification = $_SESSION['notification'];
}

//to fully log out a visitor we need to clear the session varialbles
$_SESSION['MM_Username'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
$_SESSION['PrevUrl'] = NULL;
$_SESSION['MM_FirstName'] = NULL;
$_SESSION['MM_UserTypeId'] = NULL;
$_SESSION['MM_CompanyId'] = NULL;
$_SESSION['notification'] = NULL;

unset($_SESSION['MM_Username']);
unset($_SESSION['MM_UserGroup']);
unset($_SESSION['PrevUrl']);
unset($_SESSION['MM_FirstName']);
unset($_SESSION['MM_UserTypeId']);
unset($_SESSION['MM_CompanyId']);
unset($_SESSION['notification']);

$_SESSION = array(); // reset session array
session_destroy();   // destroy session.

//If there is a notification, save that in the session
if($notification){
  session_start(); //continue session
  
  $_SESSION['notification'] = $notification; //set notification
}

$logoutGoTo = "login.php";

header("Location: ". $logoutGoTo);
