<?php
namespace common\utils;

class Log
{

    static $log = true;

    /* 是否开启异步处理信息 */
    const ASYNC_QUEUE_MODE = true;

    static $error_log = true;

    static function file($data , $name = '', $file_line = '', $logFile = 1)
    {
    	
    	if($data instanceof  \Exception){
    		$_data=array('line'=>$data->getLine(),'code'=>$data->getCode(),'file'=>$data->getFile(),'message'=>$data->getMessage(),'trace'=>$data->getTraceAsString());
    		$data=$_data;
    	}
        if ($data == "\r\n" || empty($data)) {
            return false;
        }
        if($logFile==1 && IS_CLI&&self::$log){
        	$logFile='console.log';
        	file_put_contents(ROOT_PATH.$logFile,"\r\n" . date('Y-m-d H:i:s') . "---------$name $file_line begin---------" . "\r\n",FILE_APPEND);
        	file_put_contents(ROOT_PATH.$logFile,self::log_details($data),FILE_APPEND);
        	file_put_contents(ROOT_PATH.$logFile,"\r\n" . date('Y-m-d H:i:s') . "---------$name $file_line end---------" . "\r\n",FILE_APPEND);
        }else{
	    	if (self::$log) {
	           error_log("\r\n" . date('Y-m-d H:i:s') . "---------$name $file_line begin---------" . "\r\n", 3, $logFile);
	           error_log(self::log_details($data), 3, $logFile);
	           error_log("\r\n" . date('Y-m-d H:i:s') . "---------$name $file_line end---------" . "\r\n", 3, $logFile);
	       }
        }
    }

    static function log_details($data, $pref = "")
    {
        if (is_array($data)) {
            $str = array(
                ""
            );
            foreach ($data as $k => $v)
                array_push($str, $pref . $k . " => " . self::log_details($v, $pref . "\t"));
            return implode("\n", $str);
        }
        return $data;
    }
}
?>