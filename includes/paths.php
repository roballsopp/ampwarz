<?php

defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);

defined("SITE_ROOT") ? null : define("SITE_ROOT", $_SERVER['DOCUMENT_ROOT']);
defined("INCLUDE_PATH") ? null : define("INCLUDE_PATH", SITE_ROOT.DS.'includes');
defined("LAYOUT_PATH") ? null : define("LAYOUT_PATH", SITE_ROOT.DS.'layout');
defined("PUB_PATH") ? null : define("PUB_PATH", SITE_ROOT.DS.'public');
defined("LOG_PATH") ? null : define("LOG_PATH", SITE_ROOT.DS.'log');

defined("USER_FILES_PATH") ? null : define("USER_FILES_PATH", SITE_ROOT.DS.'public'.DS.'files'.DS.'user');
defined("SITE_FILES_PATH") ? null : define("SITE_FILES_PATH", SITE_ROOT.DS.'public'.DS.'files'.DS.'user');

?>