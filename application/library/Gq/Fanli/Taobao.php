<?php
$taobaosdkspath = ROOT_PATH . DS . 'sdks' . DS . 'taobao' . DS;
Yaf_Loader::import($taobaosdkspath . 'TopClient.php');
Yaf_Loader::import($taobaosdkspath . 'RequestCheckUtil.php');
Yaf_Loader::import($taobaosdkspath . 'request' . DS . 'ItemGetRequest.php');
class Gq_Fanli_Taobao implements Gq_Fanli_Interface
{	
	public function fetch($url)
	{
		$id = $this->getID($url);

		if($id == 0)
			return false;

		$key = 'taobao_'.$id;

		$client = new TopClient;
		$config = Yaf_Registry::get("config")->fanli->taobao;
		$client->appkey = $config->app_key; 
		$client->secretKey = $config->app_secret;

		$req = new ItemGetRequest;
		$req->setFields("detail_url,title,pic_url,price");
		$req->setNumIid($id);
		$resp = $client->execute($req);
		if(!isset($resp->item))
			return false;

		$result = array();
		$goods = (array)$resp->item;

		if(empty($goods['detail_url']) || empty($goods['pic_url']))
			return false;

		$result['item']['key'] = $key;
		$result['item']['name'] = $goods['title'];
		$result['item']['price'] = $goods['price'];
		$result['item']['img'] = $goods['pic_url'];
		$result['item']['pic_url'] = $goods['pic_url'].'_200x200.jpg';
		$result['item']['url'] = $goods['detail_url'];

		return $result;
	}

	public function getID($url)
	{
		$id = 0;
		$parse = parse_url($url);
		if(isset($parse['query']))
		{
            parse_str($parse['query'],$params);
			if(isset($params['id']))
				$id = $params['id'];
            elseif(isset($params['item_id']))
                $id = $params['item_id'];
			elseif(isset($params['default_item_id']))
                $id = $params['default_item_id'];
        }
		return $id;
	}

	public function getKey($url)
	{
		$id = $this->getID($url);
		return 'taobao_'.$id;
	}

}
?>