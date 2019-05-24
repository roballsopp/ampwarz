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

$log->create_entry("Visited recieve page.");

$error = 0;

$short_desc = '';
$long_desc = '';

if ( isset($_POST['submit']) && "Upload File" == $_POST['submit'] ) {

		// get the form information from POST
		$user_id     = $_POST['user_id'];
		$gen_id      = $_POST['gen_id'];
		$short_desc  = $_POST['short_desc'];
		$long_desc   = $_POST['long_desc'];
		
		$selected_genre = Genre::get_genre_by_id($gen_id);
		
		// if no errors, then run update query and redirect back to admins main page
		if ( $file = get_file_upload($_FILES['audio_file'])  ) {
			
			Sound::create_new_sound($user_id, $gen_id, $short_desc, $long_desc, $file);
			
			$log->create_entry('Created new sound. ID: ' . $db->insert_id);
			
			// header("Location: manage_store.php");
			// exit(); // prevents further html data from this page from being sent.
			
		} else {
		
			$error |= ERR_FILE_UPLOAD;
			
			$log->create_entry('File upload failed. Error: ' . $_FILES['file_input']['error']);
			
			// if for some reason the file upload fails, we have to tell the user, so we must send error info back to "contribute.php".
			// the following code makes a post request so we can include variables, in this case, the error and the user's original input
			$url = PUB_PATH.DS;
			
			if ($selected_genre->category == 'Amp') {
				$url .= 'amp_upload.php';
			} elseif ($selected_genre->category == 'Cab') {
				$url .= 'cab_upload.php';
			} else {
				die('Category not recognized.');
			}
			
			$data = array('error' => $error, 'gen_id' => $gen_id, 'short_desc' => $short_desc, 'long_desc' => $long_desc);

			// use key 'http' even if you send the request to https://...
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data),
				),
			);
			$context = stream_context_create($options);
			$result  = file_get_contents($url, false, $context); // get contents of "contribute.php" and dump them back to the browser

			var_dump($result);
			
		}
		
}

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
			<p>Thank you for your submission!</p>
		</div></section>		
		
	</body>
	
</html>