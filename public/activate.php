<?php session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'validation.php';

// User::area_requires_login();
// User::area_requires_permission($_SESSION['user_id'], CAN_ACCESS_ADMIN);

$log->create_entry('Visited activate page.');

$error = false;

if ( isset( $_GET['email']) && isset( $_GET['hash']) ) {

	$selected_user = User::get_user_by_email($_GET['email']); // GET would come from links on select_cat.php
	
	if ( $selected_user->active == $_GET['hash'] ) {
		$selected_user->active = 'active';
		$selected_user->update_user();
	} else $error = true;
	
} else $error = true;

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
			<?php if ($error) { ?>
			<p>Something went wrong with your activation. Please contact us to resolve this problem.</p>	
			<?php } else { ?>
			<p>Your account has been activated! If you aren't redirected to the login page in a few seconds, please click <a href="index.php">here</a>.</p>			
			
			<script type="text/javascript">
				setTimeout( function(){
					window.location = "index.php";
				}, 3000);
			</script>
			<?php } ?>
		</div></section>
		
		
	</body>
	
</html>