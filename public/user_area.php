<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Genre.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';

User::area_requires_login();

$log->create_entry('Visited user area page.');



?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Ampwarz</title>
		<link rel="stylesheet" type="text/css" href="css/menu.css" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	
	<body>
	
		<?php require_once LAYOUT_PATH.DS. 'header.php'; ?>
		<?php require_once LAYOUT_PATH.DS. 'nav.php'; ?>
		
		<section id="center_pane"><div id="container">
		
			<h1>Welcome!</h1>

			<p>Greetings, fellow audio nerd! The easiest way to start is just to go <a href="vote.php">vote</a> a few times. You can do this as many times as you like. Then go check out the ladder for whatever category you are interested in under "View Ranks" to the left. If you want to find out what the top metal amp tones are, just go to "View Ranks" > "Amps" > "Metal". If you want to get involved and add your own amp to the runnings, head to the corresponding "Contribute" page and follow the instructions.</p>

		</div></section>

	</body>
	
</html>