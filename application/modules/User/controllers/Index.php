<?php
class IndexController extends Yaf_Controller_Abstract {
	public function indexAction() {
		$request = $this->getRequest();		
		$base_uri = $request->getRequestUri();
		//分页
		$page = $request->getQuery('p', 1);
		$per = 20;
		$offset = ($page - 1) * $per;

		//条件
		$exp = array();
		$exp['user_id'] = $request->getQuery('user_id');
		$exp['user_name'] = $request->getQuery('user_name');
		$exp['real_name'] = $request->getQuery('real_name');
		$exp['alipay'] = $request->getQuery('alipay');
		$exp['status'] = $request->getQuery('status');

		//排序
		$orderArr = array();
		$ordername = $request->getQuery('ordername');
		$order =  $request->getQuery('order');
		if(!empty($ordername) && !empty($order)){
			$orderArr[$ordername] = $order;
		}

		//字段
		$field = array('user_id', 'user_name', 'real_name', 'alipay', 'status', 'wait_money', 'has_money', 'reg_time');

		$conn = Gq_Db_Connection::getInstance()->getConn();
		$userDao = new UserModel($conn);
		$data = $userDao->find($exp, $orderArr, $field, $per, $offset);
		$count = $userDao->count($exp);

		//分页
		$paginator = new Gq_Paginator($count, $per);
		$paginator->setCurrentPageNumber($page);

		$this->_view->assign('userList', $data);
		$this->_view->assign('order', $orderArr);
		$this->_view->assign('params', $request->getQuery());
		$this->_view->assign('base_uri', $base_uri);
		$this->_view->assign('paginator', $paginator);
	}

	public function addAction(){
		Yaf_Registry::get('layout')->disableLayout();
		$request = $this->getRequest();
		if($request->isPost()){
			$conn = Gq_Db_Connection::getInstance()->getConn();
			$userDao = new UserModel($conn);
			$user = array();
			$user['user_name'] = $request->getPost('user_name');
			$user['password'] = $request->getPost('password');
			$user['real_name'] = $request->getPost('real_name');
			$user['alipay'] = $request->getPost('alipay');
			$user['tel'] = $request->getPost('tel');
			$user['status'] = $request->getPost('status');
			$id = $userDao->insert($user);
			if($id > 0){
				$res = array('error' => 0, 'message' => '添加成功！');
			}else{
				$res = array('error' => 1, 'message' => '添加失败！');
			}			
			echo json_encode($res);
			exit;
		}
	}

	public function editAction(){
		Yaf_Registry::get('layout')->disableLayout();
		$request = $this->getRequest();
		$id = $request->getQuery('id');
		if($id > 0){
			$conn = Gq_Db_Connection::getInstance()->getConn();
			$userDao = new UserModel($conn);
			$user = $userDao->getById($id, array('user_id', 'user_name', 'real_name', 'alipay', 'tel', 'status'));
			if(!$user) exit('用户不存在！');
		}else{
			exit('ID非法！');
		}
		if($request->isPost()){
			$user['real_name'] = $request->getPost('real_name');
			$password = $request->getPost('password');
			$user['alipay'] = $request->getPost('alipay');
			$user['tel'] = $request->getPost('tel');
			$user['status'] = $request->getPost('status');

			if(!empty($password)){
				$user['password'] = $password;
			}
			$where = array('user_id = ?' => $id);
			$userDao->update($user, $where);
			$res = array('error' => 0, 'message' => '修改成功！');
			echo json_encode($res);
			exit;
		}
		$this->_view->assign('user', $user);
	}

	public function removeAction(){
		$request = $this->getRequest();
		$res = array('error' => 1, 'message' => '未知错误！');
		$id = $request->getQuery('id');

		if($id > 0){
			$conn = Gq_Db_Connection::getInstance()->getConn();
			$userDao = new UserModel($conn);
			$userDao->delete($id);
			$res = array('error' => 0, 'message' => '删除成功');
		}		
		echo json_encode($res);
		exit;
	}
}
?>