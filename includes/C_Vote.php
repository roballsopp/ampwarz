<?php require_once 'C_mysqliEx.php';

class Vote {

	public $id;
	public $voter;
	public $sound_A;
	public $sound_B;
	public $vote;
	
	private static function create_from_record($record) {
		$object = new self;
		
		$object->id      = $record['id'];
		$object->voter = $record['voter'];
		$object->sound_A  = $record['sound_A'];
		$object->sound_B  = $record['sound_B'];
		$object->vote    = $record['vote'];
		
		return $object;
	}

	public static function get_votes() {

		global $db;
		
		$object_arr = array();
		
		$qry  = "SELECT * ";
		$qry .= "FROM votes ";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		while($record = $result->fetch_assoc()) {
			$object_arr[] = self::create_from_record($record);
		}
		
		$result->free();
		
		return $object_arr;

	}

	public static function get_vote_by_id($vote_id) {
		
		global $db;
		
		$vote_id = $db->real_escape_string($vote_id);
		
		$qry  = "SELECT * ";
		$qry .= "FROM votes ";
		$qry .= "WHERE id={$vote_id} ";
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

	public static function create_new_vote($voter, $sound_A, $sound_B, $vote) {

		global $db;
	
		$voter   = $db->real_escape_string($voter);
		$sound_A = $db->real_escape_string($sound_A);
		$sound_B = $db->real_escape_string($sound_B);
		$vote    = $db->real_escape_string($vote);
		
		$qry  = "INSERT INTO votes (voter, sound_A, sound_B, vote) ";
		$qry .= "VALUES ({$voter}, {$sound_A}, {$sound_B}, {$vote}) ";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this function would die() before returning
			
	}

	public static function update_vote() {

		global $db;
		
		$voter   = $db->real_escape_string($this->voter);
		$sound_A = $db->real_escape_string($this->sound_A);
		$sound_B = $db->real_escape_string($this->sound_B);
		$vote    = $db->real_escape_string($this->vote);
		
		$qry  = "UPDATE votes SET ";
		$qry .= "vote = {$voter}, ";
		$qry .= "sound_A = {$sound_A}, ";
		$qry .= "sound_B = {$sound_B}, ";
		$qry .= "vote = {$vote} ";
		$qry .= "WHERE id = {$this->id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this function would die() before returning
		
	}

	public static function delete_vote($vote_id) {
		
		global $db;
		
		$vote = self::get_vote_by_id($vote_id);
		
		if (!$vote) return false;
		
		$vote_id = $db->real_escape_string($vote_id);

		$qry  = "DELETE ";
		$qry .= "FROM votes ";
		$qry .= "WHERE id={$vote_id} ";
		$qry .= "LIMIT 1";
		$result = $db->query($qry);
		$db->confirm_qry($result);
		
		return $result; // just returns true. if false this function would die() before returning

	}

}

?>