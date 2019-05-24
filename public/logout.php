<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';

$log->create_entry('Logged out.');

$_SESSION = array(); // set session equal to empty array

if (isset($_COOKIE[session_name()]) ) {
	setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();

header("Location: index.php");
exit();
?>