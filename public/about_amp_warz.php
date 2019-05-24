<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';

$log->create_entry('Visited about page.');

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Amp Warz - About</title>
		<link rel="stylesheet" type="text/css" href="css/menu.css" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	
	<body>
		
		<?php require_once LAYOUT_PATH.DS. 'header.php'; ?>
		<?php require_once LAYOUT_PATH.DS. 'nav.php'; ?>
		
		<section id="center_pane"><div id="container">
			<h1>About Amp Warz</h1>	
			<p>Think the 5150 is simply the best amp for metal? Think amp sims could never compete with the real thing? This site could change your mind about that and more. I have about 7 years of experience as an audio engineer, and in that short time I have come to realize just how fallible human ears can be. If you’ve ever spent 10 minutes tweaking an eq to perfection only to realize it was bypassed the whole time, then you’ll know just what I mean.</p>
			
			<p>Amp Warz seeks to eliminate all bias and find out what we truly think when it comes to guitar amp and cabinet tone. When deciding how an amp sounds in a normal setting, all kinds of things are present that diminish our ability to make a neutral, accurate decision. Room acoustics, the cabinet, the mic, and even seeing a certain brand name on the front of your amp can all influence your perception of how that amp sounds. With so much in the way of accurate judgment, how do we know what we’re really hearing?</p>
			
			<p>Amp Warz removes all the noise by providing a way to compare carefully balanced signals in a blind fashion. When you click the vote link to the left, you are presented with two unlabeled, unadorned sounds and asked to pick which one you think sounds best. The sounds will have been recorded using the same guitar DIs, run through the same cabinet impulse, and then RMS matched. Absolutely the only thing different between the two signals will be the amp. This ensures your vote isn’t influenced by ANYTHING but the amp. Once you’ve voted a few times, go check out the ladders for the categories you’re interested in by using the “View Ranks” menu to the left. You may be surprised to see what you and other voters have sent to the top!</p>
			
		</div></section>
			
		
	</body>
	
</html>