<?php
require_once 'paths.php';
require_once 'C_mysqliEx.php';

function br2nl($string) {
    return preg_replace('#<br\s*/?>#i', "\n", $string);
}

// takes a file from the $_FILES[] global array and moves it from the servers temp directory to the directory you specify in paths.php for USER_FILES_PATH. Returns false if there was an error during upload
function get_file_upload($file) {
		
	if ($file['error']) return false;
	
	$tmp_name  = $file['tmp_name'];
	$ext = 'wav';
	$target_name = uniqid('', true) . '.' . $ext;
	
	if (!is_file($tmp_name)) return false;
	
	if (move_uploaded_file($tmp_name, USER_FILES_PATH.DS.$target_name)) return $target_name;
	else return false;
			
}

function get_ranks($gen_id) {

	global $db;
	
	$gen_id    = $db->real_escape_string($gen_id);
	
	$record_arr = array();
	
	$qry  = "SELECT sounds.id AS sound_id, sounds.short_desc AS short_desc, sounds.file AS file, ";
	$qry .= "COUNT(*) AS num_matches, ";
	$qry .= "SUM( CASE WHEN votes.vote = sounds.id THEN 1 ELSE 0 END ) AS num_votes, ";
	$qry .= "SUM( CASE WHEN votes.vote = sounds.id THEN 1 ELSE 0 END ) / COUNT(*) * 100 AS win_percent ";
	$qry .= "FROM sounds ";
	$qry .= "JOIN votes ";
	$qry .= "ON sounds.id IN( votes.sound_A, votes.sound_B ) ";
	$qry .= "WHERE sounds.gen_id = '{$gen_id}' ";
	$qry .= "GROUP BY sounds.id ";
	$qry .= "ORDER BY win_percent DESC";

	$result = $db->query($qry);
	$db->confirm_qry($result);
	
	while($record = $result->fetch_assoc()) {
		$record_arr[] = $record;
	}
	
	$result->free();
	
	return $record_arr;

}

function get_sound_profile($sound_id) {

	global $db;
	
	$qry  = "SELECT ";
	$qry .= "sounds.id         AS sound_id, ";
	$qry .= "sounds.short_desc AS short_desc, ";
	$qry .= "sounds.long_desc  AS long_desc, ";
	$qry .= "sounds.file       AS sound_file, ";
	$qry .= "genres.id         AS gen_id, ";
	$qry .= "genres.name       AS gen_name, ";
	// $qry .= "users.username    AS username, ";
	$qry .= "COUNT(votes.vote) AS num_votes ";
	$qry .= "FROM sounds ";
	$qry .= "LEFT JOIN votes      ON votes.vote = sounds.id ";
	$qry .= "LEFT JOIN genres     ON genres.id = sounds.gen_id ";
	// $qry .= "LEFT JOIN users      ON users.id = sounds.uploaded_by ";
	$qry .= "WHERE sounds.id = {$sound_id} ";
	$qry .= "GROUP BY votes.vote ";
	$qry .= "LIMIT 1";
	$result = $db->query($qry);
	$db->confirm_qry($result);
	
	if ($record = $result->fetch_assoc()) {
		$result->free();
		return $record;
	}
	
	return false;

}

?>