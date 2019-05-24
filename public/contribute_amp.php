<?php session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php'; // require_once '' is actually preferred over require_once(''). require_once is a statement, not a function.
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Genre.php';
require_once INCLUDE_PATH.DS. 'C_Sound.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'validation.php';
require_once INCLUDE_PATH.DS. 'functions.php';

$log->create_entry("Visited contribute page.");

if     ( isset( $_GET['gen_id']) ) $selected_genre = Genre::get_genre_by_id($_GET['gen_id']); // GET would come from links on select_cat.php
elseif ( isset($_POST['gen_id']) ) $selected_genre = Genre::get_genre_by_id($_POST['gen_id']); // POST would come from this page when user submits form
else { // if gen_id isn't set in either GET or POST someone is trying to access this page in an odd way and we'd like to redirect them
	header("Location: select_genre.php");
	exit(); // prevents further html data from this page from being sent.	
}

$error = 0;

$short_desc = '';
$long_desc = '';

if ( isset($_POST['error']) ) {

		// get the form information from POST
		$error       = $_POST['error'];
		$short_desc  = $_POST['short_desc'];
		$long_desc   = $_POST['long_desc'];
		
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Ampwarz</title>
		<link rel="stylesheet" type="text/css" href="css/menu.css" />
		<link rel="stylesheet" type="text/css" href="css/progress.css" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	
	<body>
	
		<?php require_once LAYOUT_PATH.DS. 'header.php'; ?>
		<?php require_once LAYOUT_PATH.DS. 'nav.php'; ?>
		
		<section id="center_pane"><div id="container">
			<h1>How This Works</h1>
			
			<p><strong>WARNING: You must be using <a href="http://www.google.com/chrome/" target="_blank">Google Chrome</a> ver 17+ or <a href="http://www.mozilla.org/firefox/" target="_blank">Mozilla Firefox</a> ver 18+ for this part of the site to work for you.</strong></p>
			
			<p>The link below contains guitar DIs recorded at a sample rate of 44.1k and a bit depth of 24. Your mission, should you choose to accept it, is to reamp these DIs through your <strong>AMP ONLY</strong>. <strong>This means you must bypass your cab and record the direct signal from your amp</strong>. This can safely be accomplished by <strong>leaving your cab connected to your amp</strong> and recording from the effects send of your amp. <strong>A boost has been applied to the DIs. Do not apply any additional boost/tube screamer/overdrive</strong>. When you are done, come back to this page and upload your file by clicking the second link below. Amp Warz will then apply a cabinet impulse to your signal so it sounds like it should. In this way, Amp Warz is able to eliminate differences in playing, cabinet selection, and mic technique so that all voting is based solely on the sound of the AMP.</p>
			
			<h1>Upload Requirements:</h1>
			
			<ul>
				<li>You must upload one <strong>stereo</strong> wav file.</li>
				<li>You must pan the left guitar 100% left, and the right guitar 100% right.</li>
				<li>The sample rate of your file must be 44.1k.</li>				
			</ul>
			
			<ul id="contribute_links" class="menu">
				<li><a href="files/site/<?php echo $selected_genre->fileset; ?>">Download the DIs</a></li>
				<li><a href="amp_upload.php?gen_id=<?php echo $selected_genre->id; ?>">I Am Ready To Upload</a></li>
			</ul>
		
		</div></section>
		
	</body>
	
</html>