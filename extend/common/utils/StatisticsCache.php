<?php

namespace common\utils;

use think\Cache;
use think\Env;
class StatisticsCache {
	
	private $handler=null;
	private static $instance=null;
	public static function instance(){
		if(is_null(self::$instance)){
			self::$instance=new static();
		}
		return self::$instance;
	}
	private function __construct(){
		$env = Env::get("app_status");
		$prefix = $env == 'debug' ? 'dev_aimscrm_' : 'aimscrm_';
		$this->handler=Cache::connect([	
			// 驱动方式
			'type' => 'redis',
			// 前缀
			'prefix' => $prefix,
			// 缓存有效期 0表示永久缓存
			'expire' => 3600,
			// 主机地址
			'host' => '127.0.0.1',
			// 端口
			'port' => '6379',
			'password'=>'ooyyeeaimscrm'
		]);
	}
	
	public static function set($name, $value, $expire = null){
		return self::instance()->handler->set($name, $value, $expire);
	}
	public static function get($name, $default = false){
		return self::instance()->handler->get($name, $default);
	}
}

?>