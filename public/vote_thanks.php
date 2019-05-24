<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Vote.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';

$log->create_entry("Visited vote thanks page.");

$error = 0;

if ( isset($_POST['submit']) && "Vote!" == $_POST['submit'] ) {

	// get the form information from POST
	$voter   = $_POST['user_id'];
	$sound_A = $_POST['sound_A'];
	$sound_B = $_POST['sound_B'];
	$vote    = $_POST['vote'];
	
	if ($vote) $vote = $sound_B;
	else $vote = $sound_A;
	
	// if no errors, then run update query and redirect back to admins main page
	if ( $error == 0 ) {

		Vote::create_new_vote($voter, $sound_A, $sound_B, $vote);
		
		$log->create_entry("Created new vote. ID: " . $db->insert_id);
		
	}
		
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Ampwars - Vote</title>
		<link rel="stylesheet" type="text/css" href="css/menu.css" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	
	<body>
	
		<?php require_once LAYOUT_PATH.DS. 'header.php'; ?>
		<?php require_once LAYOUT_PATH.DS. 'nav.php'; ?>
		
		<section id="center_pane"><div id="container">
			<p>Thanks for your vote! If you aren't given another match up in a few seconds click <a href="vote.php">here</a>:</p>
		</div></section>		
		
		<script type="text/javascript">
			setTimeout( function(){
				window.location = "vote.php";
			}, 3000);
		</script>

	</body>
	
</html>