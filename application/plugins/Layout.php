<?php
class LayoutPlugin extends Yaf_Plugin_Abstract {

    private $_layoutDir;
    private $_layoutFile;
    private $_layoutVars =array();
    private $_disable = false;

    public function __construct($layoutFile = null, $layoutDir = null){
        $this->_layoutFile = $layoutFile ? $layoutFile : 'layout.phtml';
        $this->_layoutDir = $layoutDir ? $layoutDir : APP_PATH . DS . 'views' . DS . '_layout';
        $this->_layoutVars['seo_title'] = '';
        $this->_layoutVars['seo_keywords'] = '';
        $this->_layoutVars['seo_description'] = '';
    }

    public function __set($name, $value) {
        $this->_layoutVars[$name] = $value;
    }

    public function postDispatch ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        if($this->_disable === true){
            return false;
        }
        $body = $response->getBody();
        $response->clearBody();

        $layout = new Yaf_View_Simple($this->_layoutDir);
        $layout->content = $body;
        $layout->assign('layout', $this->_layoutVars);

        $response->setBody($layout->render($this->_layoutFile));
    }

    public function disableLayout(){
        $this->_disable = true;
    }

    public function setLayoutFile($fileName){
        $this->_layoutFile = $fileName;
    }

    public function setLayoutDir($dir){
        $this->_layoutDir = $dir;
    }
}