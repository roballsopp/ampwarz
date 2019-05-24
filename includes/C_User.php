<?php require_once 'C_mysqliEx.php';
require_once 'pass_encrypt.php';

// permission constants for bitwise AND/OR stacking
defined("ADMIN_MASTER")     ? null : define("ADMIN_MASTER",     0x001);
defined("CAN_ACCESS_ADMIN") ? null : define("CAN_ACCESS_ADMIN", 0x002);
defined("CAN_ACCESS_STORE") ? null : define("CAN_ACCESS_STORE", 0x004);
defined("CAN_ACCESS_POST")  ? null : define("CAN_ACCESS_POST",  0x008);

// user settings constants for bitwise AND/OR stacking

// begin User class
class User {
	
	public $id;
	public $username;
	public $password; // always stored here in hashed form
	public $email;
	public $settings;
	public $permissions;
	public $active;
	
	// returns a new User object if given a valid array/database record from the users table
	public static function create_from_record($record) {
		$object = new self;
		
		$object->id           = $record['id'];
		$object->username     = $record['username'];
		$object->password     = $record['password'];
		$object->email        = $record['email'];
		$object->settings     = $record['settings'];
		$object->permissions  = $record['permissions'];
		$object->active       = $record['active'];
		
		return $object;
	}
	
	// returns a new User object if given a valid email and password
	public static function authenticate($email, $password) {
		
		global $db;
		
		$email = $db->real_escape_string($email);
		
		$qry  = "SELECT * ";
		$qry .= "FROM users ";
		$qry .= "WHERE email='{$email}' "; // single quotes around {$email} because its a string, not integer
		$qry .= "AND active='active' ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		if ($user = $result->fetch_assoc()) {
			$result->free();			
			return passCheck($password, $user['password']) ? self::create_from_record($user) : false;
		}
		
		return false;
		
	}
	
	// call at the top of any page that requires a user to be logged in to access. It will redirect user to the login page.
	public static function area_requires_login() {
	
		if ( !isset($_SESSION['user_id']) ) {
			header("Location: please_login.php");
			exit(); // prevents further html data from this page from being sent.
		}
		
	}
	
	// call at the top of any page that requires a user to have certain permissions to access. It will redirect user to the index page.
	public static function area_requires_permission($user_id, $required_permissions) {
	
		$user = self::get_user_by_id($user_id);
		
		if ( ($required_permissions & $user->permissions) != $required_permissions ) {
		
			header("Location: index.php");
			exit();
				
		}
	
	}

	// returns an array of all User objects currently in the database
	public static function get_users() {
	
		global $db;
		
		$object_arr = array();
		
		$qry  = "SELECT * ";
		$qry .= "FROM users ";
		$qry .= "ORDER BY username ASC ";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		while($record = $result->fetch_assoc()) {
			$object_arr[] = self::create_from_record($record);
		}
		
		$result->free();
		
		return $object_arr;

	}

	// returns a single User object corresponding to the id passed in, or returns false if no such user exists
	public static function get_user_by_id($user_id) {
		
		global $db;
		
		$user_id = $db->real_escape_string($user_id);
		
		$qry  = "SELECT * ";
		$qry .= "FROM users ";
		$qry .= "WHERE id={$user_id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		if ($user = $result->fetch_assoc()) {
			$result->free();		
			return self::create_from_record($user);
		} 
		
		return false;

	}

	// returns a single User object corresponding to the username passed in, or returns false if no such user exists
	public static function get_user_by_username($username) {
		
		global $db;
		
		$username = $db->real_escape_string($username);
		
		$qry  = "SELECT * ";
		$qry .= "FROM users ";
		$qry .= "WHERE user='{$username}' "; // single quotes around {$username} because its a string, not integer
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		if ($user = $result->fetch_assoc()) {
			$result->free();		
			return self::create_from_record($user);
		} 
		
		return false;

	}
	
	// returns a single User object corresponding to the emmail passed in, or returns false if no such user exists
	public static function get_user_by_email($email) {
		
		global $db;
		
		$email = $db->real_escape_string($email);
		
		$qry  = "SELECT * ";
		$qry .= "FROM users ";
		$qry .= "WHERE email='{$email}' "; // single quotes around {$email} because its a string, not integer
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		if ($user = $result->fetch_assoc()) {
			$result->free();		
			return self::create_from_record($user);
		} 
		
		return false;

	}

	// creates a new database entry in the users table with the provided information
	public static function create_new_user($username, $password, $email, $settings, $permissions, $active) {

		global $db;
	
		$username        = $db->real_escape_string($username);
		$hashed_password = pass_hash($password);
		$email           = $db->real_escape_string($email);
		$settings        = $db->real_escape_string($settings);
		$permissions     = $db->real_escape_string($permissions);
		$active          = $db->real_escape_string($active);
		
		$qry  = "INSERT INTO users (username, password, email, settings, permissions, active) ";
		$qry .= "VALUES ('{$username}', '{$hashed_password}', '{$email}', {$settings}, {$permissions}, '{$active}') ";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this function would die() before returning
			
	}

	// updates the database entry corresponding to this User object
	public function update_user() {

		global $db;
	
		$username    = $db->real_escape_string($this->username);
		$password    = $db->real_escape_string($this->password);
		$email       = $db->real_escape_string($this->email);
		$settings    = $db->real_escape_string($this->settings);
		$permissions = $db->real_escape_string($this->permissions);
		$active      = $db->real_escape_string($this->active);
		
		$qry  = "UPDATE users SET ";
		$qry .= "username    = '{$username}', ";
		$qry .= "password    = '{$password}', ";
		$qry .= "email       = '{$email}', ";
		$qry .= "settings    = {$settings}, ";
		$qry .= "permissions = {$permissions}, ";
		$qry .= "active      = '{$active}' ";
		$qry .= "WHERE id = {$this->id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this function would die() before returning
		
	}

	// deletes the database entry corresponding to the user_id passed in
	public static function delete_user($user_id) {
		
		global $db;
		
		$user = self::get_user_by_id($user_id);
		
		if (!$user) return false;
		if ($user->permissions & ADMIN_MASTER) return false;
		
		$user_id = $db->real_escape_string($user_id);

		$qry  = "DELETE ";
		$qry .= "FROM users ";
		$qry .= "WHERE id={$user_id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this public function would die() before returning

	}
	
	public function send_activation_email() {
		
		$to = $this->email;
		$subject = 'Activation Email from Amp Warz.';
		
		$message = "Thanks for signing up for Amp Warz!\r\n"; // double quotes needed for all lines containing line breaks (\r\n) or variables {}
		$message .= "\r\n";
		$message .= "Your account has been created, but you won't be able to log in until you have activated your account by clicking the link below.\r\n";
		$message .= "----------------------------------\r\n";
		$message .= "Activate: http://www.ampwarz.roballsopp.com/public/activate.php?email={$this->email}&hash={$this->active}\r\n";
		$message .= "----------------------------------";
		
		$headers = 'From: donotreply@ampwarz.roballsopp.com';
		
		mail($to, $subject, $message, $headers);
		
	}

} // end User class

$logged_in_user = false;
if (isset($_SESSION['user_id'])) $logged_in_user = User::get_user_by_id($_SESSION['user_id']);


?>