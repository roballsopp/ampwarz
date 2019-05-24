<?php session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/paths.php';
require_once INCLUDE_PATH.DS. 'C_mysqliEx.php';
require_once INCLUDE_PATH.DS. 'C_User.php';
require_once INCLUDE_PATH.DS. 'C_Log.php';
require_once INCLUDE_PATH.DS. 'validation.php';

// User::area_requires_login();
// User::area_requires_permission($_SESSION['user_id'], CAN_ACCESS_ADMIN);

$log->create_entry('Visited new user page.');

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
		
			<h1>Create an account</h1>
			
			<p>So you want to enter some gear into the contest, eh? Awesome! Before you can dive in, we will require a little info from you. Hopefully this annoyance will help keep spam to a minimum on this site.</p>
			
			<form id="frm_new_user" name="frm_new_user" action="new_user_thanks.php" method="POST">
				<table>
					<tr>
						<td class="label">Email:</td><td><input required type="text" name="email" value="" maxlength="50" /></td><td class="description">Must be a valid email address</td>
					</tr>
					<tr>
						<td class="label">Password:</td><td><input required type="password" name="password" value=""  maxlength="60" /></td><td class="description">Must be 8-60 chars</td>
					</tr>
					<tr>
						<td class="label">Re-enter password:</td><td colspan="2"><input required type="password" name="pass_conf" value=""  maxlength="60" /></td>
					</tr>
					<tr>
						<td class="submit_btn" colspan="2"><input type="submit" name="submit" value="Create Account"> | <a href="index.php">Cancel</a></td><td></td>
					</tr>
				</table>
			</form>	
		</div></section>
				
			

	</body>
	
</html>