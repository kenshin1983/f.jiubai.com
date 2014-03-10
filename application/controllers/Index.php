<?php
class IndexController extends Yaf_Controller_Abstract {
	const COOKIE_LIFE = 604800;			//cookie有效期一周

	public function init(){
		Yaf_Registry::get('layout')->seo_title = '返利查询';
	}

	public function indexAction() {
		$request = $this->getRequest();
		if($request->isPost()){
			$url = $request->getPost('url');
			if($url){
				$taobao = new Gq_Fanli_Taobao();
				$goods = $taobao->fetch($url);
				$id = $taobao->getId($url);				
				$this->getView()->assign('goods', $goods); 
				$this->getView()->assign('num_id', $id);
			}
		}		
	}

	public function loginAction() {
		$request = $this->getRequest();
		if($request->isPost()){
			$username = $request->getPost('username');
			$password = $request->getPost('password');

			//todo 过滤无效用户名和密码

			if($username && $password){
				$conn = Gq_Db_Connection::getInstance()->getConn();
		        $userDao = new UserModel($conn);
		        $user = $userDao->authenticate($username, $password);
		        if($user){
		        	setcookie('user[user_id]', $user['user_id'], time() + self::COOKIE_LIFE, '/');
		        	setcookie('user[user_name]', $user['user_name'], time() + self::COOKIE_LIFE, '/');
		        	$this->redirect('/');
		        }else{
		        	$this->getView()->assign('message', '用户名密码错误');
		        }
			}
		}
		
	}

	public function logoutAction() {
		setcookie('user[user_id]', '', time()-3600, '/');
		setcookie('user[user_name]', '', time()-3600, '/');
		$this->redirect('/');
	}

	public function regAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$username = trim($request->getPost('username'));
			$password = $request->getPost('password');
			$password2 = $request->getPost('password2');

			if(!preg_match("/^[a-zA-Z0-9_]{6,12}$/", $username)){
				$this->getView()->assign('message', "[ERR]用户名6-12位数字字母下划线组成");
				return;
			}
			if(!preg_match("/^[a-zA-Z0-9_]{6,18}$/", $password)){
				$this->getView()->assign('message', "[ERR]密码6-18位数字字母下划线组成");
				return;
			}
			if($password2 != $password){
				$this->getView()->assign('message', "[ERR]密码不一致");
				return;
			}

			$conn = Gq_Db_Connection::getInstance()->getConn();
	        $userDao = new UserModel($conn);
	        $user = array(
	        	'user_name'		=> $username,
	        	'password'		=> $password
	        );
	        $user_id = $userDao->insert($user);
	        if($user_id > 0){
	        	setcookie('user[user_id]', $user_id, time() + self::COOKIE_LIFE, '/');
	        	setcookie('user[user_name]', $username, time() + self::COOKIE_LIFE, '/');
	        	$this->redirect('/ucenter');
	        }else{
	        	$this->getView()->assign('message', '注册失败！');
	        }
		}
		
	}
}
?>