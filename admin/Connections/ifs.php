<?php
$path = explode("/", getcwd());
$ifs_position = array_search('IFS', $path);
$numberToPop = count($path) - $ifs_position - 1;
for ($ndx = 0; $ndx < $numberToPop; $ndx++) {
    array_pop($path);
}

$ROOT = implode("/", $path) . "/";
$CONFIG_PATH = $ROOT . "config/";

// let it be for a while
$CONFIG_PATH = "C:/InsiderFocus/ifs/admin/config/";

$configAsString = file_get_contents($CONFIG_PATH . "config.json", true);

$config = json_decode($configAsString, true);

$PORT = $config["port"];
$DOMAIN = $config["domain"];
$PATHS = $config["paths"];

$FS_PATH = $PATHS["fsPath"];
$URL_PATH = $PATHS["urlPath"];
$BASE_URL = "http://" . $config["domain"] . ":$PORT/";

$SERVER_PATH = $FS_PATH . $PATHS["serverPath"];
$SERVER_URL = $URL_PATH . $PATHS["serverPath"];
$ADMIN_PATH = $FS_PATH . $PATHS["adminPath"];
$ADMIN_URL = $URL_PATH . $PATHS["adminPath"];
$VIEWS_PATH = $ADMIN_PATH . "views/";
$VIEWS_URL = $ADMIN_URL . "views/";
$CHAT_ROOM_PATH = $FS_PATH . $PATHS["chatRoomPath"];
$CHAT_ROOM_URL = $URL_PATH . $PATHS["chatRoomPath"];

$MYSQL_USER = $config["mysql"]["user"];
$MYSQL_PASS = $config["mysql"]["password"];
$MYSQL_DATABASE = $config["mysql"]["database"];
$MYSQL_HOST = $config["mysql"]["host"];

if ((isset($html) && !$html) || !isset($html)) {
    $hostname_ifs = $MYSQL_HOST;
    $database_ifs = $MYSQL_DATABASE;
    $username_ifs = $MYSQL_USER;
    $password_ifs = $MYSQL_PASS;

    $ifs = mysql_connect($hostname_ifs, $username_ifs, $password_ifs) or trigger_error(mysql_error(), E_USER_ERROR);
}

//initialize the session
if (!isset($_SESSION)) {
    session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
    $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

$form_action = $_SERVER['REQUEST_URI'];
$root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

if (preg_match('/^\/ifs-test/', $form_action)) {
    $root .= '/ifs-test';
}

/* Set current location */
$current_location = $form_action;

/**
 * If the current location session is set,
 * then use that session variable as the current location
 **/
if (isset($_SESSION['current_location'])) {
    $current_location = $_SESSION['current_location'];
}

/*Added to Fix root change*/
$_SERVER['HTTP_HOST'] = str_replace('http://', '', substr($ADMIN_URL, 0, -1));
?>