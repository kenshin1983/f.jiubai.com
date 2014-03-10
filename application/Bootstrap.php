<?php

class Bootstrap extends Yaf_Bootstrap_Abstract{
    private $_config;   

    public function _initConfig() {
        $this->_config = new Yaf_Config_Ini(CONF_PATH . DS . 'application.ini');
        Yaf_Registry::set("config", $this->_config);
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        $router = $dispatcher->getRouter();
        $router->addConfig($this->_config->common->routes);
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        $dispatcher->registerPlugin( new UserPlugin() );
        $layout = new LayoutPlugin();
        Yaf_Registry::set("layout", $layout);
        $dispatcher->registerPlugin($layout);
        $dispatcher->registerPlugin( new InitPlugin() );
    }

    public function _initFunction(Yaf_Dispatcher $dispatcher) {
        
    }
}