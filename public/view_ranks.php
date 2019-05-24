<?php session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php'; // require_once '' is actually preferred over require_once(''). require_once is a statement, not a function.
require_once INCLUDE_PATH.DS. 'C_Sound.php';
require_once INCLUDE_PATH.DS. 'C_Genre.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'functions.php';

if ( isset( $_GET['gen_id']) ) {

	$gen_id = $_GET['gen_id']; // GET would come from links on select_cat.php
	
	$ranks = get_ranks($gen_id);
	$selected_genre = Genre::get_genre_by_id($gen_id);
	
} else { // if cat_id isn't set in GET someone is trying to access this page in an odd way and we'd like to redirect them

	header("Location: select_cat.php");
	exit(); // prevents further html data from this page from being sent.	
}

$log->create_entry("Visited contribute page.");

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
			<h1><?php echo "{$selected_genre->name} {$selected_genre->category}"; ?> Rankings</h1>
		
			<table id="tbl_ranks">
			<?php foreach ($ranks as $rank) { ?>
			<tr>
				<td><a href="sound_profile.php?sound_id=<?php echo $rank['sound_id']; ?>"><?php echo $rank['short_desc']; ?></a></td><td>Win %: <?php echo round($rank['win_percent'], 1); ?> (<?php echo $rank['num_votes']; ?> votes)</td>
			</tr>
			<?php } ?>
			</table>
		</div></section>
	
		
		
	</body>
	
</html>