<?php 
class Gq_Auth implements Zend_Auth_Adapter_Interface
{
    private $_username;
    private $_password;
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password)
    {       
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $config = Yaf_Registry::get("config")->admin;
        if($this->_username === $config->user && $this->_password === $config->pw){
            $code = Zend_Auth_Result::SUCCESS;
            $identity = $config;
            $message = array();
        }else{
            $code = Zend_Auth_Result::FAILURE;
            $identity = null;
            $message = array('用户名密码错误！');
        }
        return new Zend_Auth_Result($code, $identity, $message);
    }


}
