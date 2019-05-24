<?php require_once 'C_mysqliEx.php';
require_once 'paths.php';

class Sound {

	public $id;
	public $date;
	public $time;
	public $uploaded_by;
	public $gen_id;
	public $short_desc;
	public $long_desc;
	public $file;
	
	private static function create_from_record($record) {
		$object = new self;
		
		$dateTime = date_create($record['timestamp']);
		
		$object->id           = $record['id'];
		$object->date         = $record['timestamp'];
		$object->time         = $record['timestamp'];
		$object->uploaded_by  = $record['uploaded_by'];
		$object->gen_id       = $record['gen_id'];
		$object->short_desc   = $record['short_desc'];
		$object->long_desc    = $record['long_desc'];
		$object->file         = $record['file'];
		
		return $object;
	}

	public static function get_sounds() {

		global $db;
		
		$object_arr = array();
		
		$qry  = "SELECT * ";
		$qry .= "FROM sounds ";
		$qry .= "ORDER BY timestamp DESC";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		while($record = $result->fetch_assoc()) {
			$object_arr[] = self::create_from_record($record);
		}
		
		$result->free();
		
		return $object_arr;

	}
	
	public static function get_sounds_rand() {

		global $db;

		$qry  = "SELECT gen_id "; // get a random gen_id
		$qry .= "FROM sounds ";
		$qry .= "ORDER BY RAND() ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		$record = $result->fetch_assoc();
		$gen_id = $record['gen_id'];
		
		$object_arr = array();
		
		$qry  = "SELECT * ";
		$qry .= "FROM sounds ";
		$qry .= "WHERE gen_id = {$gen_id} "; // ensure the two sounds you grab are from the same genre id. This also naturally ensures they are from the same category
		$qry .= "ORDER BY RAND() ";
		$qry .= "LIMIT 2";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		while($record = $result->fetch_assoc()) {
			$object_arr[] = self::create_from_record($record);
		}
		
		$result->free();
		
		return $object_arr;

	}

	public static function get_sound_by_id($sound_id) {
		
		global $db;
		
		$sound_id = $db->real_escape_string($sound_id);
		
		$qry  = "SELECT * ";
		$qry .= "FROM sounds ";
		$qry .= "WHERE id={$sound_id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		if ($record = $result->fetch_assoc()) {
			$result->free();
			return self::create_from_record($record);
		} else {
			return false;
		}

	}

	public static function create_new_sound($user_id, $gen_id, $short_desc, $long_desc, $file) {

		global $db;
	
		$user_id     = $db->real_escape_string($user_id);
		$gen_id      = $db->real_escape_string($gen_id);
		$short_desc  = $db->real_escape_string($short_desc);
		$long_desc   = $db->real_escape_string($long_desc);
		$file        = $db->real_escape_string($file);
		
		$qry  = "INSERT INTO sounds (uploaded_by, gen_id, short_desc, long_desc, file) ";
		$qry .= "VALUES ({$user_id}, '{$gen_id}', '{$short_desc}', '{$long_desc}', '{$file}') ";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this public function would die() before returning
			
	}

	public static function update_sound() {

		global $db;
		
		$user_id     = $db->real_escape_string($this->user_id);
		$gen_id      = $db->real_escape_string($this->gen_id);
		$short_desc  = $db->real_escape_string($this->short_desc);
		$long_desc   = $db->real_escape_string($this->long_desc);
		$file        = $db->real_escape_string($this->file);
		
		$qry  = "UPDATE sounds SET ";
		$qry .= "uploaded_by = {$user_id}, ";
		$qry .= "gen_id = {$gen_id}, ";
		$qry .= "short_desc = '{$short_desc}', ";
		$qry .= "long_desc = '{$long_desc}', ";
		$qry .= "file = '{$file}' ";
		$qry .= "WHERE id = {$this->id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this public function would die() before returning
		
	}

	public static function delete_sound($sound_id) {
		
		global $db;
		
		$sound = self::get_sound_by_id($sound_id);
		
		if (!$sound) return false;
		
		$filepath = USER_FILES_PATH.DS.$sound->file;
		
		if (is_file($filepath)) unlink($filepath);
		
		$sound_id = $db->real_escape_string($sound_id);

		$qry  = "DELETE ";
		$qry .= "FROM sounds ";
		$qry .= "WHERE id={$sound_id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this public function would die() before returning

	}

}

?>