<?php

defined("ERR_LOGIN")				?	null	:	define("ERR_LOGIN",					0x001);
defined("ERR_PASSWORD")				?	null	:	define("ERR_PASSWORD",				0x002);
defined("ERR_USER_NAME_FORMAT")		?	null	:	define("ERR_USER_NAME_FORMAT",		0x004);
defined("ERR_PASSWORD_FORMAT")		?	null	:	define("ERR_PASSWORD_FORMAT",		0x008);
defined("ERR_DISPLAY_NAME_FORMAT")	?	null	:	define("ERR_DISPLAY_NAME_FORMAT",	0x010);
defined("ERR_PASS_CONF")			?	null	:	define("ERR_PASS_CONF",				0x020);
defined("ERR_FILE_UPLOAD")			?	null	:	define("ERR_FILE_UPLOAD",			0x040);
defined("ERR_EMAIL")				?	null	:	define("ERR_EMAIL",					0x080);

function validatePass($password, $passConf) {

	$error = 0;
	
	// password must be no more than 60 chars
	if ( strlen($password) > 60 ) $error |= ERR_PASSWORD_FORMAT;
	
	// password must be no fewer than 8 chars
	if ( strlen($password) < 8 ) $error |= ERR_PASSWORD_FORMAT;
	
	// password must match password confirmation
	if ( $password !== $passConf ) $error |= ERR_PASS_CONF;
	
	return $error;
	
}

function validateUser($username) {

	$error = 0;
	
	// user name must be no more than 60 chars
	if ( strlen($username) > 50 ) $error |= ERR_USER_NAME_FORMAT;
	
	// user name must be no fewer than 6 chars
	if ( strlen($username) < 6 ) $error |= ERR_USER_NAME_FORMAT;
	
	return $error;
	
}

function validateEmail($email) {

	$error = 0;

	// display name must be no more than 60 chars
	if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) $error |= ERR_EMAIL;
	
	return $error;
	
}

function validateName($display_name) {

	$error = 0;

	// display name must be no more than 60 chars
	if ( strlen($display_name) > 50 ) $error |= ERR_DISPLAY_NAME_FORMAT;
	
	// display name must not be blank
	if ( strlen($display_name) < 1 ) $error |= ERR_DISPLAY_NAME_FORMAT;
	
	return $error;
	
}

function displayErrors($error) {
	
	if ($error) { 
				
		$output = "<p>There was a problem with your information.";
		// $output .= " Error code: {$error}."; // comment this out when in production
		$output .= "</p>";
		$output .= "<ul class=\"form_errors\">";
		
		if ( $error & ERR_LOGIN ) $output .= "<li>Incorrect user name or password.</li>";
		if ( $error & ERR_PASSWORD ) $output .= "<li>Incorrect password.</li>";
		if ( $error & ERR_EMAIL ) $output .= "<li>Invalid email address.</li>";
		if ( $error & ERR_DISPLAY_NAME_FORMAT ) $output .= "<li>Display name does not match format requirements.</li>";
		if ( $error & ERR_USER_NAME_FORMAT ) $output .= "<li>User name does not match format requirements.</li>";
		if ( $error & ERR_PASSWORD_FORMAT ) $output .= "<li>New password does not match format requirements.</li>";
		if ( $error & ERR_PASS_CONF ) $output .= "<li>New password does not match confirmation.</li>";
		if ( $error & ERR_FILE_UPLOAD ) {
			
			$error_msg = array(
				UPLOAD_ERR_OK         => "No errors.",
				UPLOAD_ERR_INI_SIZE   => "Larger than upload_max_filesize.",
				UPLOAD_ERR_FORM_SIZE  => "Larger than form MAX_FILE_SIZE.",
				UPLOAD_ERR_PARTIAL    => "Partial upload",
				UPLOAD_ERR_NO_FILE    => "No file was sent",
				UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
				UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
				UPLOAD_ERR_EXTENSION  => "Upload stopped by extension."
			);
			
			$code = $_FILES["file_input"]["error"];
			$output .= "<li>There was a problem uploading your file: {$error_msg[$code]}</li>";
		}
						
		$output .= "</ul>";
		
		return $output;
		
	} else return false;

}

?>