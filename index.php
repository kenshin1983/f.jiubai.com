<?php 
//do
//mysqlpdo();
//phpmemcache();
//phpredis();
//phpmongo();
//mysql pdo test
function mysqlpdo(){
	$dsn = "mysql:host=192.168.0.98;port=3306;dbname=test";
	$db = new PDO($dsn, 'kenshin', 'gq1983');
	$rs = $db->query("select * from `kenshin` where 1");
	$data = $rs->fetchAll();
	var_dump($data);
	$db = null;
	exit;
}

//redis test
function phpredis(){
	$redis = new Redis;
	$redis->connect('127.0.0.1', 6379);
	$redis->set('aaa', 'ggg');
	echo $redis->get('aaa');
	exit;
}

//memcache test
function phpmemcache(){
	$mem = new Memcached;
	$mem->addServer('localhost', 11211);
	$mem->set('aaa', 'kkk');
	echo $mem->get('aaa');
	exit;
}
//mongodb test
function phpmongo(){
	$mongo = new Mongo();
	$userDB = $mongo->test->user;
	$userDB->insert(array('user_name' => 'kenshin', 'sex' => 'body'));
	$data = $userDB->findOne(array('user_name' => 'kenshin'));
	var_dump($data);
	
	$mongo->close();
	exit;
}

phpinfo();
