<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_Vote.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'validation.php';

$log->create_entry("Visited vote thanks page.");

$error = 0;

$username    = 'newuser';
$password    = '';
$pass_conf   = '';
$email       = '';
$settings    = 0;
$permissions = 0;
$active      = md5(uniqid());

if ( isset($_POST['submit']) && ("Create Account" == $_POST['submit']) ) {

	// $username  = $_POST['username'];
	$password  = $_POST['password'];
	$pass_conf = $_POST['pass_conf'];
	$email     = $_POST['email'];

	// $error |= validateUser($username);
	$error |= validatePass($password, $pass_conf);
	$error |= validateEmail($email);
	
	if ( $error == 0 ) {
	
		User::create_new_user($username, $password, $email, $settings, $permissions, $active);
		
		$log->create_entry("Created new user. ID: " . $db->insert_id);
		
		$new_user = User::get_user_by_id($db->insert_id);
		
		$new_user->send_activation_email();
		
		// header("Location: manage_admins.php");
		// exit(); // prevents further html data from this page from being sent.
		
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
			<?php if ($output = displayErrors($error)) { echo $output; ?>
				<p><a href="new_user.php">Go back to account creation page.</a></p>
			<?php } else { ?>
				<p>Thanks for creating an account! Check your email for an activation link.</p>
			<?php } ?>
		</div></section>		

	</body>
	
</html>