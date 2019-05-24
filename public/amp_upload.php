<?php session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php'; // require_once '' is actually preferred over require_once(''). require_once is a statement, not a function.
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Genre.php';
require_once INCLUDE_PATH.DS. 'C_Sound.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'validation.php';
require_once INCLUDE_PATH.DS. 'functions.php';


User::area_requires_login();

$log->create_entry("Visited contribute page.");

if     ( isset( $_GET['gen_id']) ) $selected_genre = Genre::get_genre_by_id($_GET['gen_id']); // GET would come from links on select_cat.php
elseif ( isset($_POST['gen_id']) ) $selected_genre = Genre::get_genre_by_id($_POST['gen_id']); // POST would come from this page when user submits form
else { // if gen_id isn't set in either GET or POST someone is trying to access this page in an odd way and we'd like to redirect them
	header("Location: index.php");
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
		
			<h1>Upload your results here:</h1>
			
			<?php if ($output = displayErrors($error)) echo $output; ?>
			
			<form id="frm_contribute" enctype="multipart/form-data" action="amp_upload.php" method="POST">
		
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
				<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" />
				<input type="hidden" name="gen_id" value="<?php echo $selected_genre->id; ?>" />
				
				<p><input id="input_file" type="file" accept="audio/wav" required name="file_input" /></p>
				<p><input id="short_desc" type="text" name="short_desc" required placeholder="Make and model of amp. example: Marshall JCM 800..." value="<?php echo $short_desc; ?>" /></p>
				<p><textarea id="long_desc" name="long_desc" placeholder="Describe any mods and add any additional notes here..."><?php echo $long_desc; ?></textarea></p>
				<p><input type="submit" name="submit" value="Upload File"> | <a href="index.php">Cancel</a></p>
			</form>
			
			<div id="progress">
				<div id="messages"></div>
				<progress id="progress_bar" min="0" max="100" value="0">0% complete</progress>
			</div>
		</div></section>
	
		
		
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/signalCom.js"></script>
	<script src="js/processUpload.js"></script>
		
	</body>
	
</html>