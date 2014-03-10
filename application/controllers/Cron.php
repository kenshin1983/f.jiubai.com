<?php
class CronController extends Yaf_Controller_Abstract {
	public function init(){
		Yaf_Registry::get('layout')->disableLayout();
	}

	public function crawlAction() {
		$res = '[FAIL]数据获取失败！';
		$date = date('Ymd');
		$taobao = new Gq_Report_Taobao();
		$fanli = $taobao->getReport($date);
		$num = 0;

		if($fanli){
			$money = array();
			$num = count($fanli);
			$conn = Gq_Db_Connection::getInstance()->getConn();
			$fanliDao = new FanliModel($conn);
			foreach($fanli as $item){
				$item['type'] = 'taobao';
				$item['down_time'] = date('Y-m-d H:i:s');
				//检测数据是否已存在
				$flag = $fanliDao->isExist($item['order_num'], 'taobao');
				if($flag === false){
					$fanliDao->insert($item);
					//统计用户待返
					if($item['user_id'] > 0){
						if(!isset($money[$item['user_id']]))
							$money[$item['user_id']] = 0;
						$money[$item['user_id']] += $item['commission'];
					}
					//记录日志
					Services_User_Fanli::log("每日自动脚本导入，待返：{$item['commission']}", 0, $item['user_id']);
				}
			}
			if(!empty($money)){
				Services_User_Fanli::incrWaitMoney($money);
			}
			$res = '[OK]成功获取' . $num . '条数据！[DATE]' . $date;
		}else{
			$res = '[FAIL]未获取到任何数据！';
		}

		//记录
		Services_User_Fanli::record($date, $num);
		echo $res . "[DATE]{$date}[TIME]" . date('Y-m-d H:i:s') . "\n";
		exit;
	}

	public function crawlallAction() {
		exit("Over!");
		$request = $this->getRequest();
		$startD = $request->getQuery('d');
		if($startD > date('Ymd')) exit('非法日期');

		$res = '[FAIL]数据获取失败！';
		$taobao = new Gq_Report_Taobao();
		$fanli = $taobao->getReport($startD);
		$num = 0;

		if($fanli){
			$num = count($fanli);
			$conn = Gq_Db_Connection::getInstance()->getConn();
			$fanliDao = new FanliModel($conn);
			foreach($fanli as $item){
				$item['type'] = 'taobao';
				$item['down_time'] = date('Y-m-d H:i:s');
				//检测数据是否已存在
				$flag = $fanliDao->isExist($item['order_num'], 'taobao');
				if($flag === false)
					$fanliDao->insert($item);
			}
			$res = '[OK]成功获取' . $num . '条数据！[DATE]' . $date;
		}else{
			$res = '[FAIL]未获取到任何数据！';
		}

		//记录
		Services_User_Fanli::record($startD, $num);
		echo $res . "[DATE]{$startD}[TIME]" . date('Y-m-d H:i:s') . "\n";

		$y = substr($startD, 0, 4);
		$m = substr($startD, 4,2);
		$d = substr($startD, 6,2);
		$nextD = date("Ymd", strtotime("+1 day", mktime(0,0,0,$m, $d, $y)));

		echo $this->redirect("/cron/crawlall?d={$nextD}");
		exit;
	}
}
?>