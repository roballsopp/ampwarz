<?php session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'validation.php';

$log->create_entry('Visited login page.');

if ( $logged_in_user ) { 
	header("Location: user_area.php");
	exit(); // prevents further html data from this page from being sent.
}

$error = 0;

$email = '';

if ( isset($_POST['submit']) && ('Log In' == $_POST['submit']) ) {

	$email = trim($_POST['email']);
	$password = trim($_POST['password']);	
	
	if ($user = User::authenticate($email, $password)) {
	
		$_SESSION['user_id'] = $user->id;
		
		$log->create_entry('Logged in.');
		
		header("Location: user_area.php");
		exit();
		
	} else $error |= ERR_LOGIN;
	
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Ampwarz - Login</title>
		<link rel="stylesheet" type="text/css" href="css/menu.css" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
	</head>
	
	<body>
	
		<?php require_once LAYOUT_PATH.DS. 'header.php'; ?>
		<?php require_once LAYOUT_PATH.DS. 'nav.php'; ?>
		
		<section id="center_pane"><div id="container">
			<?php if ($output = displayErrors($error)) echo $output; ?>
		
			<h1>Welcome! Login below.</h1>
			
			<p>Don't have an account? Sign up <a href="new_user.php">here</a>.</p>
			
			<form id="frm_login" action="login.php" method="POST">
				<table>
					<tr>
						<td class="label">Email:</td><td><input required type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"></td>
					</tr>
					<tr>
						<td class="label">Password:</td><td><input required type="password" name="password" value=""></td>
					</tr>
					<tr>
						<td class="submit_btn" colspan="2"><input type="submit" name="submit" value="Log In"></td><td></td>
					</tr>
				</table>
			</form>
		</div></section>
		
		

	</body>
	
</html>