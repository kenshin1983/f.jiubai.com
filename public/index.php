<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

define("ROOT_PATH",  dirname(dirname(__FILE__)));
define("DS", DIRECTORY_SEPARATOR);
define("APP_PATH",  ROOT_PATH . DS . 'application');
define("LIB_PATH",  APP_PATH . DS . 'library');
define("CONF_PATH",  ROOT_PATH . DS . 'conf');
define("PUBLIC_PATH", ROOT_PATH . DS . 'public');
set_include_path(LIB_PATH);
$app  = new Yaf_Application(CONF_PATH . DS . 'application.ini');
$app->bootstrap()
	->run();