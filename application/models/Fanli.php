<?php 
class FanliModel extends Gq_Models {
	const TABLE = 'fanli_user_money';
	const RECORD_TABLE = 'fanli_crawl_record';
	const LOG_TABLE = 'fanli_log';
	
	public function getById($id, $field = '*'){
		$select = $this->_db->select()
					   ->from(self::TABLE, $field)
					   ->where('fanli_id = ?', $id);
		return $this->_db->fetchRow($select);
	}

	public function find(array $exp = array(), array $order = array(), $field = '*', $limit = 0, $offset = 0){
		$select = $this->_db->select()->from(self::TABLE, $field);
		if(!empty($exp)){
			if(isset($exp['user_id']) && $exp['user_id'] > 0){
				$select->where('user_id = ?', (int)$exp['user_id']);
			}
			if(!empty($exp['order_num'])){
				$select->where('order_num = ?', $exp['order_num']);
			}
			if(isset($exp['status'])){
				$select->where("status IN (?)", (array)$exp['status']);
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
			if(!empty($exp['order_num'])){
				$select->where('order_num = ?', $exp['order_num']);
			}
			if(isset($exp['status']) && $exp['status'] >= 0){
				$select->where("status IN (?)", (array)$exp['status']);
			}
		}
		return $this->_db->fetchOne($select);
	}

	public function insert($data){
		$this->_db->insert(self::TABLE, $data);
		return $this->_db->lastInsertId();
	}

	public function update($data, $where){
		return $this->_db->update(self::TABLE, $data, $where);
	}

	public function delete($id){
		return $this->_db->delete(self::TABLE, array('user_id = ?' => $id));
	}

	public function isExist($order_num, $type){
		$select = $this->_db->select()
					   ->from(self::TABLE, 'COUNT(1)')
					   ->where('`order_num` = ?', $order_num)
					   ->where('`type` = ?', $type);
		return $this->_db->fetchOne($select) > 0 ? true : false;
	}

	public function getRecord($date){
		$select = $this->_db->select()
					   ->from(self::RECORD_TABLE, '*');
		if(!empty($date)){
			$select->where('date = ?', $date);
		}
		$select->order('time DESC');
		$select->limit(10);
		return $this->_db->fetchAll($select);
	}

	public function record($date, $num){
		$select = $this->_db->select()
					   ->from(self::RECORD_TABLE, 'COUNT(1)')
					   ->where('date = ?', $date);
		$n = $this->_db->fetchOne($select);
		$time = date('Y-m-d H:i:s');
		if($n > 0){
			$this->_db->update(self::RECORD_TABLE, array('num' => $num, 'time' => $time), array('date = ?' => $date));
		}else{
			$this->_db->insert(self::RECORD_TABLE, array('date' => $date, 'num' => $num, 'time' => $time));
		}
	}

	public function addlog($data){
		$this->_db->insert(self::LOG_TABLE, $data);
		return $this->_db->lastInsertId();
	}
}
?>