<?php 
Class Services_User_User{
	static public function getUserAssoc(){
		$cache = Gq_Cache::getInstance()->getCache();
		if(!($data = $cache->load('userAssoc'))){
			$conn = Gq_Db_Connection::getInstance()->getConn();
			$userDao = new UserModel($conn);
			$data = $userDao->getAssoc();
			$cache->save($data, 'userAssoc');
		}
		return $data;
	}
}