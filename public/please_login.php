<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';

if ( $logged_in_user ) { 
	header("Location: user_area.php");
	exit(); // prevents further html data from this page from being sent.
}

$log->create_entry('Visited index page.');

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Amp Warz - Please Log In</title>
		<link rel="stylesheet" type="text/css" href="css/menu.css" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	
	<body>
	
		<?php require_once LAYOUT_PATH.DS. 'header.php'; ?>
		<?php require_once LAYOUT_PATH.DS. 'nav.php'; ?>
		
		<section id="center_pane"><div id="container">
		
			<h1>You need an account to access this page!</h1>
			
			<p>If you have an account already, simply log in <a href="login.php">here</a>. Otherwise, <a href="new_user.php">sign up</a>!</p>

		</div></section>

	</body>
	
</html>