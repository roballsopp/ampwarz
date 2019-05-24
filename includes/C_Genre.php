<?php require_once 'C_mysqliEx.php';

class Genre {

	public $id;
	public $category;
	public $name;
	public $fileset;
	
	private static function create_from_record($record) {
		$object = new self;
		
		$object->id       = $record['id'];
		$object->category = $record['category'];
		$object->name     = $record['name'];	
		$object->fileset  = $record['fileset'];
		
		return $object;
	}

	public static function get_genres() {

		global $db;
		
		$object_arr = array();
		
		$qry  = "SELECT * ";
		$qry .= "FROM genres ";
		$qry .= "ORDER BY name ASC";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		while($record = $result->fetch_assoc()) {
			$object_arr[] = self::create_from_record($record);
		}
		
		$result->free();
		
		return $object_arr;

	}
	
	public static function get_genres_by_category($category) {

		global $db;
		
		$object_arr = array();
		
		$category = $db->real_escape_string($category);
		
		$qry  = "SELECT * ";
		$qry .= "FROM genres ";
		$qry .= "WHERE category='{$category}' ";
		$qry .= "ORDER BY name ASC";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		while($record = $result->fetch_assoc()) {
			$object_arr[] = self::create_from_record($record);
		}
		
		$result->free();
		
		return $object_arr;

	}

	public static function get_genre_by_id($gen_id) {
		
		global $db;
		
		$gen_id = $db->real_escape_string($gen_id);
		
		$qry  = "SELECT * ";
		$qry .= "FROM genres ";
		$qry .= "WHERE id={$gen_id} ";
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

	public static function create_new_genre($name, $fileset) {

		global $db;
		
		$name = $db->real_escape_string($name);
		$fileset  = $db->real_escape_string($fileset);
		
		$qry  = "INSERT INTO genres (name, fileset) ";
		$qry .= "VALUES ('{$name}', '{$fileset}') ";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this public function would die() before returning
			
	}

	public function update_genre() {

		global $db;
		
		$gen_id  = $db->real_escape_string($this->id);
		$category  = $db->real_escape_string($this->category);
		$name    = $db->real_escape_string($this->name);
		$fileset = $db->real_escape_string($this->fileset);
		
		$qry  = "UPDATE genres SET ";
		$qry .= "category = '{$category}', ";
		$qry .= "name = '{$name}', ";
		$qry .= "fileset = '{$fileset}' ";
		$qry .= "WHERE id = {$gen_id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this public function would die() before returning
		
	}

	public static function delete_genre($gen_id) {
		
		global $db;
		
		if (!self::get_genre_by_id($gen_id)) return false;
		
		$gen_id = $db->real_escape_string($gen_id);

		$qry  = "DELETE ";
		$qry .= "FROM genres ";
		$qry .= "WHERE id={$gen_id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this public function would die() before returning

	}

}

?>