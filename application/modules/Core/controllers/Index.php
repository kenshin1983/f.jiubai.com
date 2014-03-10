<?php
class IndexController extends Yaf_Controller_Abstract {

	public function indexAction(){
		
	}

	public function loginAction() {
		Yaf_Registry::get('layout')->disableLayout();
		$request = $this->getRequest();
		if($request->isPost()){
			$username = $request->getPost('username');
			$password = $request->getPost('password');

			//todo 过滤无效用户名和密码

			if($username && $password){
				$auth = Zend_Auth::getInstance();
				$result = $auth->authenticate(new Gq_Auth($username, $password));
				switch ($result->getCode()) {
				    case Zend_Auth_Result::SUCCESS:
				        /** do stuff for successful authentication */
				    	$this->redirect('/Core/Index/index');
				        break;

				    default:
				        /** do stuff for other failure **/
						$error = $result->getMessages();
				    	$this->getView()->assign('message', $error[0]);
				        break;
				}
			}
		}
	}

	public function logoutAction(){
		Zend_Auth::getInstance()->clearIdentity();
		$this->redirect('/Core/Index/login');
		exit;
	}
}
?>