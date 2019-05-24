<?php

defined("DB_HOST")	?	null	:	define("DB_HOST", "localhost");
defined("DB_USER")	?	null	:	define("DB_USER", "roballso_rca06d");
defined("DB_PASS")	?	null	:	define("DB_PASS", "#Q*T]WavG]u&");
defined("DB_NAME")	?	null	:	define("DB_NAME", "roballso_ampwarz");

class mysqliEx extends mysqli {

	public function __construct($host, $user, $pass, $name) {
        parent::__construct($host, $user, $pass, $name);
    }
	
	public function __destruct() {
		$this->close();
	}
	
	public function confirm_qry($result) {
		if (!$result) die("Query failed: " . $this->error . ". Error No. " . $this->errno);
	}

}

$db = new mysqliEx(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($db->connect_error)  die("Connect Error (" . $db->connect_errno . ") " . $db->connect_error);

?>