<?php session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php'; // require_once '' is actually preferred over require_once(''). require_once is a statement, not a function.
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'functions.php';

if     ( isset( $_GET['sound_id']) ) $selected_sound = get_sound_profile($_GET['sound_id']); // GET would come from links on select_cat.php
else { // if sound_id isn't set in GET someone is trying to access this page in an odd way and we'd like to redirect them
	header("Location: view_ranks.php");
	exit(); // prevents further html data from this page from being sent.	
}

$log->create_entry("Visited sound profile page.");

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
			<h1><?php echo $selected_sound['short_desc']; ?></h1>
			
			<?php if ($selected_sound['long_desc'] != '') echo "<p>{$selected_sound['long_desc']}</p>"; ?>
			
			<p>Genre: <?php echo $selected_sound['gen_name']; ?></p>
			
			<p>Votes Received: <?php echo $selected_sound['num_votes']; ?></p>
			
			<audio id="audio" controls>
				<source src="files/user/<?php echo $selected_sound['sound_file']; ?>" type="audio/wav">
				Your browser does not support the audio tag.
			</audio>

		</div></section>		
		
	</body>
	
</html>