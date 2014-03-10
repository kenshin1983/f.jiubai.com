<?php
$taobaosdkspath = ROOT_PATH . DS . 'sdks' . DS . 'taobao' . DS;
Yaf_Loader::import($taobaosdkspath . 'TopClient.php');
Yaf_Loader::import($taobaosdkspath . 'RequestCheckUtil.php');
Yaf_Loader::import($taobaosdkspath . 'request' . DS . 'TaobaokeReportGetRequest.php');
class Gq_Report_Taobao implements Gq_Report_Interface
{	
	public function getReport($date)
	{
		$client = new TopClient;
		$config = Yaf_Registry::get("config")->fanli->taobao;
		$client->appkey = $config->app_key; 
		$client->secretKey = $config->app_secret;

		$req = new TaobaokeReportGetRequest;
		$req->setFields("trade_id,pay_time,create_time,pay_price,num_iid,commission,outer_code,item_title,shop_title");
		$req->setDate($date);
		$req->setPageNo(1);
		$req->setPageSize(40);
		$resp = $client->execute($req);
		$fanli = array();

		if(isset($resp->taobaoke_report) && (int)$resp->taobaoke_report->total_results > 0){
			$report = $resp->taobaoke_report->taobaoke_report_members->taobaoke_report_member;
			foreach ($report as $key => $value) {
				$value = (array)$value;
				$data = array(
					'user_id'		=> isset($value['outer_code']) ? $value['outer_code'] : 0,
					'order_num'		=> $value['trade_id'],
					'pay_time'		=> $value['pay_time'],
					'commission'	=> $value['commission'],
					'create_time'	=> $value['create_time'],
					'pay_price'		=> $value['pay_price'],
					'goods_key'		=> 'taobao_' . $value['num_iid'],
					'goods_title'	=> $value['item_title'],
					'seller'		=> $value['shop_title'],
					'status'		=> 0
				);
				$fanli[] = $data;
			}
		}
		return $fanli;
	}

}
?>