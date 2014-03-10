<?php 
class UserModel extends Gq_Models {
	const TABLE = 'fanli_user';
	const SALT = 'KENSHIN';
	
	public function authenticate($username, $password){
		$select = $this->_db->select()->from(self::TABLE)
									  ->where('user_name=?', $username)
									  ->where('password = ?', $this->_switchPw($password))
									  ->where('status IN (1,2)');
		return $this->_db->fetchRow($select);
	}

	public function getById($id, $field = '*'){
		$select = $this->_db->select()
					   ->from(self::TABLE, $field)
					   ->where('user_id = ?', $id);
		return $this->_db->fetchRow($select);
	}

	public function find(array $exp = array(), array $order = array(), $field = '*', $limit = 0, $offset = 0){
		$select = $this->_db->select()->from(self::TABLE, $field);
		if(!empty($exp)){
			if(isset($exp['user_id']) && $exp['user_id'] > 0){
				$select->where('user_id = ?', (int)$exp['user_id']);
			}
			if(!empty($exp['user_name'])){
				$select->where("user_name LIKE '%" . addslashes($exp['user_name']) . "%'");
			}
			if(!empty($exp['real_name'])){
				$select->where("real_name = ?", $exp['real_name']);
			}
			if(!empty($exp['alipay'])){
				$select->where("alipay = ?", $exp['alipay']);
			}
			if(isset($exp['status']) && $exp['status'] >= 0){
				$select->where("status = ?", (int)$exp['status']);
			}
		}
		if(!empty($order)){
			foreach ($order as $key => $value) {
				$select->order($key . ' ' . $value);
			}
		}
		if($limit > 0 && $offset >= 0){
			$select->limit($limit, $offset);
		}
		return $this->_db->fetchAll($select);
	}

	public function count(array $exp = array()){
		$select = $this->_db->select()->from(self::TABLE, 'count(1)');
		if(!empty($exp)){
			if(isset($exp['user_id']) && $exp['user_id'] > 0){
				$select->where('user_id = ?', (int)$exp['user_id']);
			}
			if(!empty($exp['user_name'])){
				$select->where("user_name LIKE '%" . addslashes($exp['user_name']) . "%'");
			}
			if(!empty($exp['real_name'])){
				$select->where("real_name = ?", $exp['real_name']);
			}
			if(!empty($exp['alipay'])){
				$select->where("alipay = ?", $exp['alipay']);
			}
			if(isset($exp['status']) && $exp['status'] >= 0){
				$select->where("status = ?", (int)$exp['status']);
			}
		}
		return $this->_db->fetchOne($select);
	}

	public function insert($data){
		if(array_key_exists('password', $data)){
			$data['password'] = $this->_switchPw($data['password']);
		}
		$this->_db->insert(self::TABLE, $data);
		return $this->_db->lastInsertId();
	}

	public function update($data, $where){
		if(array_key_exists('password', $data)){
			$data['password'] = $this->_switchPw($data['password']);
		}
		return $this->_db->update(self::TABLE, $data, $where);
	}

	public function delete($id){
		return $this->_db->delete(self::TABLE, array('user_id = ?' => $id));
	}

	/**
	 * 把用户的待返转入已返
	 */
	public function switchMoney($user_id, $money){
		$data = array(
			'wait_money'	=> new Zend_Db_Expr('wait_money - ' . $money),
			'has_money'		=> new Zend_Db_Expr('has_money + ' . $money)
		);
		$where = array(
			'user_id'	=> $user_id
		);
		return $this->_db->update(self::TABLE, $data, $where);
	}

	public function getAssoc(){
		$select = $this->_db->select()->from(self::TABLE, array('user_id', 'user_name'));
		return $this->_db->fetchAssoc($select);
	}

	private function _switchPw($password){
		return sha1(md5($password . self::SALT));
	}
}
?>