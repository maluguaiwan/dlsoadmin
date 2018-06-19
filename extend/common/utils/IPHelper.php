<?php

namespace common\utils;

class IPHelper {
	public static function getLocation($ip){
		$area = array();
		$url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
		$data = Http::get($url);
		$ipdata = !empty($data['content'])?json_decode($data['content']):array();
		if($ipdata && isset($ipdata['code']) && $ipdata['code'] == 0){
			$data=$ipdata['data'];
			$area['country']= $data['country']?:'未知';
			$area['prov']=$data['region']?:'未知';
			$area['city']=$data['city']?:'未知';
			$area['status'] = 1;
		}
		return empty($area)?array('country'=>'未知','prov'=>'未知','city'=>'未知'):$area;
	}
}

?>