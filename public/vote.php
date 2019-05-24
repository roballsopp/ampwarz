<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Sound.php';
require_once INCLUDE_PATH.DS. 'C_Vote.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';

$log->create_entry("Visited compare page.");

$sounds = Sound::get_sounds_rand();

$user_id = 0;
if (isset($_SESSION['user_id'])) $user_id = $_SESSION['user_id'];

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
			<audio id="audio1">
				<source src="files/user/<?php echo $sounds[0]->file; ?>" type="audio/wav">
				Your browser does not support the audio tag.
			</audio>	
			
			<audio id="audio2">
				<source src="files/user/<?php echo $sounds[1]->file; ?>" type="audio/wav">
				Your browser does not support the audio tag.
			</audio>
			
			<h1>Which one sounds better?</h1>
			
			<form id="frm_vote" name="frm_vote" action="vote_thanks.php" method="POST">
			
				<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="sound_A" value="<?php echo $sounds[0]->id; ?>" />
				<input type="hidden" name="sound_B" value="<?php echo $sounds[1]->id; ?>" />
			
				<table>
					<tr>
						<td><button type="button" id="0">Play Sound</button></td>
						<td><input type="radio" name="vote" value="0"></td>
						<td><button type="button" id="1">Play Sound</button></td>
						<td><input type="radio" name="vote" value="1"></td>
						<td><input type="submit" name="submit" value="Vote!"></td>
					</tr>	
				</table>
			</form>
		
		<p><strong>Note:</strong> Internet Explorer, in all of its greatness, does not support the playing of wav files. If you aren't getting sound here, please switch to a <a href="http://www.google.com/chrome/" target="_blank">real browser</a>.</p>
		</div></section>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="js/controls.js"></script>
	</body>
	
</html>