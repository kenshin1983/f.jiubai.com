<?php
class FanliController extends Yaf_Controller_Abstract {
	public function indexAction(){
		$request = $this->getRequest();		
		$base_uri = $request->getRequestUri();
		//分页
		$page = $request->getQuery('p', 1);
		$per = 20;
		$offset = ($page - 1) * $per;

		//条件
		$exp = array('status' => array(0,1));
		$status = $request->getQuery('status');
		if($status && $status != '-1'){
			$exp['status'] = array($status);
		}
		$exp['user_id'] = $request->getQuery('user_id');
		$exp['order_num'] = $request->getQuery('order_num');

		//排序
		$orderArr = array();
		$ordername = $request->getQuery('ordername');
		$order =  $request->getQuery('order');
		if(!empty($ordername) && !empty($order)){
			$orderArr[$ordername] = $order;
		}

		//字段
		$field = '*';

		$conn = Gq_Db_Connection::getInstance()->getConn();
		$fanliDao = new FanliModel($conn);
		$data = $fanliDao->find($exp, $orderArr, $field, $per, $offset);
		$count = $fanliDao->count($exp);

		//分页
		$paginator = new Gq_Paginator($count, $per);
		$paginator->setCurrentPageNumber($page);

		$this->_view->assign('fanliList', $data);
		$this->_view->assign('order', $orderArr);
		$this->_view->assign('params', $request->getQuery());
		$this->_view->assign('base_uri', $base_uri);
		$this->_view->assign('paginator', $paginator);
	}

	public function reloadAction(){
		Yaf_Registry::get('layout')->disableLayout();
		$request = $this->getRequest();

		if($request->isPost()){
			$res = array('error'=>1, 'message'=>'失败！');
			$date = $request->getPost('date');
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
						Services_User_Fanli::log("后台手动获取，待返：{$item['commission']}", 0, $item['user_id']);
					}
				}
				if(!empty($money)){
					Services_User_Fanli::incrWaitMoney($money);
				}
				$res = array('error'=>0, 'message'=>'sucessful！');
			}
			//记录
			Services_User_Fanli::record($date, $num);
			echo json_encode($res);
			exit;
		}

		//获取抓取记录
		$records = Services_User_Fanli::getRecord();
		$this->_view->assign('records', $records);
	}

	public function statusAction(){
		$request = $this->getRequest();
		$res = array('error'=>1, 'message'=>'失败！');
		$id = $request->getQuery('id');
		$status = $request->getQuery('t');
		if($id > 0){
			$conn = Gq_Db_Connection::getInstance()->getConn();
			$fanliDao = new FanliModel($conn);
			$fanliDao->update(array('status' => $status), array('fanli_id = ?' => $id));			
			if($status == 1){
				//待返 转 已返
				$fanli = $fanliDao->getById($id, array('user_id', 'commission'));
				if($fanli && $fanli['user_id'] > 0){
					$userDao = new UserModel($conn);
					$n = $userDao->switchMoney($fanli['user_id'], $fanli['commission']);
					if($n > 0){
						//记录日志
						Services_User_Fanli::log("后台管理员手动确认待返转入已返，已返：{$fanli['commission']}", 1, $fanli['user_id']);
					}

				}
			}
			$res = array('error'=>0, 'message'=>'sucessful！');
		}
		echo json_encode($res);
		exit;
	}
}
?>