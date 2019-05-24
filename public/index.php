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
		<title>Amp Warz</title>
		<link rel="stylesheet" type="text/css" href="css/menu.css" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	
	<body>
	
		<?php require_once LAYOUT_PATH.DS. 'header.php'; ?>
		<?php require_once LAYOUT_PATH.DS. 'nav.php'; ?>
		
		<section id="center_pane"><div id="container">
			<h1>Welcome to Amp Warz!</h1>
		
			<p>This is an experimental site designed to test tone headz' preferences in a scientific way. We pit amp against amp and let you <a href="vote.php">vote</a> for which one you think is best. The catch is that you know nothing about the amp when you cast your ballot. All you get is two audio samples, and you have to pick which one sounds better. Once you've voted, you can check out the current ladder to see which amp most people think is the King of Tone!</p>
			
		</div></section>
		
	</body>
	
</html>