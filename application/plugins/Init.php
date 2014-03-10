<?php
class InitPlugin extends Yaf_Plugin_Abstract {

	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		//init const;
		$config = Yaf_Registry::get("config");
		define("BASE_URL", $config->web->base_url);
        define("STATIC_URL", $config->web->static_url);
       	define("CURR_MODULE", $request->getModuleName());
       	define("CURR_CONTROLLER", $request->getControllerName());
       	define("CURR_ACTION", $request->getActionName());

		if($request->getModuleName() === 'Index'){			
			//前台
			//taobao jssdk
	        $fanli = $config->fanli->taobao;
	        $app_key = $fanli->app_key;/*填写appkey */
	        $secret=$fanli->app_secret;/*填入Appsecret'*/
	        $timestamp=time()."000";
	        $message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
	        $mysign=strtoupper(hash_hmac("md5",$message,$secret));
	        setcookie("timestamp",$timestamp);
	        setcookie("sign",$mysign);
		}else{
			//后台
			//设置布局
			define("APP_ADMIN_LAYOUT_DIR", APP_PATH . DS . 'modules' . DS . 'Core' . DS . 'views' . DS . '_layout');
			$layout = Yaf_Registry::get('layout');
			$layout->setLayoutFile('admin.phtml');
			$layout->setLayoutDir(APP_ADMIN_LAYOUT_DIR);
			//注册菜单
			$menu = new Yaf_Config_Ini(CONF_PATH . DS . 'menu.ini');
			Yaf_Registry::set("menu", $menu);
		}
	}


}