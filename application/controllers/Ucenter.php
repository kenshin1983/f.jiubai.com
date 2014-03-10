<?php
class UcenterController extends Yaf_Controller_Abstract {
	private $user;

	public function init(){
		$user = isset($_COOKIE['user']) ? $_COOKIE['user'] : null;
		if(!$user){
			$this->redirect('/index/login');
		}
		$this->user = $user;
		Yaf_Registry::get('layout')->seo_title = '用户中心';
	}

	public function indexAction() {
		$conn = Gq_Db_Connection::getInstance()->getConn();
		$userDao = new UserModel($conn);
		$user = $userDao->getById($this->user['user_id'], array('wait_money','has_money'));
		$this->_view->assign('user', $user);

		$request = $this->getRequest();		
		$base_uri = $request->getRequestUri();
		//分页
		$page = $request->getQuery('p', 1);
		$per = 20;
		$offset = ($page - 1) * $per;

		//条件
		$exp = array('user_id' => $this->user['user_id']);
		$status = $request->getQuery('status');
		if($status == -1){
			$exp['status'] = array(0,1);
		}else{
			$exp['status'] = array($status);
		}
		$exp['order_num'] = $request->getQuery('order_num');

		//排序
		$orderArr = array();
		$ordername = $request->getQuery('ordername');
		$order =  $request->getQuery('order');
		if(!empty($ordername) && !empty($order)){
			$orderArr[$ordername] = $order;
		}

		//字段
		$field = '*';

		$fanliDao = new FanliModel($conn);
		$data = $fanliDao->find($exp, $orderArr, $field, $per, $offset);
		$count = $fanliDao->count($exp);

		//分页
		$paginator = new Gq_Paginator($count, $per);
		$paginator->setCurrentPageNumber($page);

		$this->_view->assign('fanliList', $data);
		$this->_view->assign('order', $orderArr);
		$this->_view->assign('params', $request->getQuery());
		$this->_view->assign('base_uri', $base_uri);
		$this->_view->assign('paginator', $paginator);
	}

	public function infoAction(){
		$conn = Gq_Db_Connection::getInstance()->getConn();
		$userDao = new UserModel($conn);

		$request = $this->getRequest();
		if($request->isPost()){
			$res = array('error' => 1, 'message' => '');
			$email = trim($request->getPost('email'));
			if(!empty($email) && !preg_match('/^[a-z0-9-_]+(\.[a-z0-9-_]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i', $email)){
				$res['message'] = "[ERR]邮箱不合法！";
				echo json_encode($res);
				exit;
			}
			$tel = trim($request->getPost('tel'));
			if(!empty($tel) && !preg_match('/^1[0-9]{10}$/', $tel)){
				$res['message'] = "[ERR]手机不合法！";
				echo json_encode($res);
				exit;
			}
			$alipay = trim($request->getPost('alipay'));
			if(!empty($alipay) && !preg_match('/^[a-z0-9-_]+(\.[a-z0-9-_]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i', $alipay)){
				$res['message'] = "[ERR]支付宝账号不正确！";
				echo json_encode($res);
				exit;
			}
			$real_name = addslashes(trim($request->getPost('real_name', '')));		
			$qq = addslashes(trim($request->getPost('qq', '')));
			$user = array(
				'email'			=> $email,
				'tel'			=> $tel,
				'real_name'		=> $real_name,
				'alipay'		=> $alipay,
				'qq'			=> $qq
			);
			$userDao->update($user, array('user_id = ?' => $this->user['user_id']));
			$res = array('error' => 0, 'message' => 'successful');
			echo json_encode($res);
			exit;
		}
		$user = $userDao->getById($this->user['user_id']);
		$this->_view->assign('user', $user);
		
	}
}
?>