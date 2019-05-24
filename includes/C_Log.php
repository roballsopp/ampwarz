<?php 
require_once 'C_User.php';
require_once 'paths.php';

class Log {

	private $file = false;

	public function __construct() {
	
		if(!is_dir(LOG_PATH)) {
			if(!mkdir(LOG_PATH)) die('Could not create log directory');
		} 
		
		$file_name = LOG_PATH.DS. date("Y_m_d") . '.log';
		$this->file = new SplFileObject($file_name, "a");
		if (!$this->file->isFile()) die('Something went wrong when creating or opening todays log file.');
	
	}
	
	public function create_entry($message) {
	
		global $logged_in_user;
	
		$entry = date("H:i:s");
		
		if ($logged_in_user) $entry .= ' ' . $logged_in_user->username;
		else $entry .= ' Guest';
		
		$entry .= ' ' . $message;
		$entry .= "\n";
		
		$bytes_written = $this->file->fwrite($entry);
		return $bytes_written;
	}

}

$log = new Log();

?>