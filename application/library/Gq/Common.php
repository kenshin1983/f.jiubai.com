<?php 
/**
 * kenshin PUBLIC FUNCTION 
 */

class Gq_Common {
	/**
	 * 通过条件生成链接
	 * @param $base_uri 基本链接
	 * @param $params 当前页面的条件
	 * @param $exp 增加的条件
	 * @return string 链接
	 * @author kenshin.gao
	 **/
	static function buildUrl($base_uri, $params, $exp){
		$uri = $base_uri;
		$params = array_merge($params, $exp);
		if(!empty($params)){
			$uri .= '?';
			$arr = array();
			foreach ($params as $key => $value) {
				$arr[] = $key . '=' . $value;
			}
			$uri .= implode('&', $arr);
		}
		return $uri;
	}

	/**
	 * 切换排序，生成切换后的排序数组，主要用于buildurl
	 * @param $order 当前页的排序数组
	 * @param $field 需要切换的排序字段（只支持一种排序）
	 * @return array 切换后的排序字段
	 * @author kenshin.gao
	 **/
	static function toggleOrder($order, $field){
		$res = array('ordername' => '', 'order' => '');
		if(!empty($order) && array_key_exists($field, $order)){
			$res['ordername'] = $field;
			$res['order'] = strtoupper($order[$field]) == 'ASC' ? 'DESC' : 'ASC';
		}else{
			$res['ordername'] = $field;
			$res['order'] = 'ASC';
		}
		return $res;
	}
}