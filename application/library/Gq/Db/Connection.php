<?php 
class Gq_Db_Connection{
	protected static $_instance = null;

	public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getConn($server = 'master'){
    	$config = Yaf_Registry::get("config")->db->$server->toArray();
		$db = Zend_Db::factory('PDO_MYSQL', $config);
		return $db;
	}
}