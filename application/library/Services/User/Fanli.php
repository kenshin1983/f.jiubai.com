<?php 
Class Services_User_Fanli{
	static public function record($date, $num){
		$conn = Gq_Db_Connection::getInstance()->getConn();
		$fanliDao = new FanliModel($conn);
		$fanliDao->record($date, $num);
	}

	static public function getRecord($date = ''){
		$conn = Gq_Db_Connection::getInstance()->getConn();
		$fanliDao = new FanliModel($conn);
		return $fanliDao->getRecord($date);
	}

	static public function incrWaitMoney(array $waitMoney){
		$conn = Gq_Db_Connection::getInstance()->getConn();
		$userDao = new UserModel($conn);
		foreach ($waitMoney as $user_id => $money) {
			$userDao->update(array('wait_money' => new Zend_Db_Expr('wait_money + ' . $money)), array('user_id = ?' => $user_id));
		}
	}

	/**
	 * 记录返利日志
	 * @param string $message 详细描述
	 * @param int $type (0:转待返 1:转已返 2:其他)
	 * @param int $user_id 操作账户ID,0表示没有针对任何账户
	 * @return boolean 
	 */
	static public function log($message, $type, $user_id = 0){
		if(empty($message)){
			return false;
		}
		$conn = Gq_Db_Connection::getInstance()->getConn();
		$fanliDao = new FanliModel($conn);
		
		$data = array(
			'type' => (int)$type,
			'message' => $message,
			'user_id' => $user_id > 0 ? $user_id : 0,
			'time'	=> date('Y-m-d H:i:s')
		);
		$log_id = $fanliDao->addlog($data);
		return $log_id > 0 ? true : false;
	}
}