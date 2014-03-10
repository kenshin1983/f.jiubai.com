<?php
class UserPlugin extends Yaf_Plugin_Abstract {

	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		if($request->getModuleName() !== 'Index'){
			//后台
			if($request->getModuleName() === 'Core'
			&& $request->getControllerName() === 'Index' 
			&& in_array($request->getActionName(), array('login', 'logout'))){
				return true;
			}

			$user = Zend_Auth::getInstance()->getIdentity();
			if(!$user){
				header("Location: /Core/Index/login");
				exit;
			}
		}else{
			//前台			
		}
	}


}