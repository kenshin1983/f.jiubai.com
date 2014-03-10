<?php 
Class Gq_Cache{
	protected static $_instance = null;

	public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    public function getCache(){
    	if(!$cache = Yaf_Registry::get('cache')){
    		$config = Yaf_Registry::get("config")->cache->toArray();
			$frontendOptions = array(
			   'lifeTime' => $config['front']['life_time'], 
			   'automatic_serialization' => true
			);

			$backendOptions = array(
			    'cache_dir' => ROOT_PATH . DS . $config['back']['cache_dir']
			);

			$cache = Zend_Cache::factory($config['front']['module'],
			                             $config['back']['module'],
			                             $frontendOptions,
			                             $backendOptions);
			Yaf_Registry::set('cache', $cache);
	    }    	
		return $cache;
	}

}