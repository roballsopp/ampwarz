<?php

function pass_hash($password) {

	$format = "$2y$10$";
	
	$salt = md5(uniqid(mt_rand(), true)); // generate ALMOST unique, ALMOST random string. MD5 returns 32 characters.
	$salt = base64_encode($salt); // returns only [a-zA-Z0-9+/]
	$salt = str_replace('+', '.', $salt); // valid salt characters are [a-zA-Z0-9./], so we replace all '+' with '.'
	$salt = substr($salt, 0, 22);
	
	$formatAndSalt = $format . $salt;
	
	$hash = crypt($password, $formatAndSalt);
	
	return $hash;

}

function passCheck($password, $existingHash) {

	$hash = crypt($password, $existingHash);
	
	if ( $hash === $existingHash) return true;
	else return false;
	
}

?>