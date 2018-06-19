<?php
namespace common\utils;

use think\Log as ThinkLog;
class AMQTools
{
    static $cache_exchange = array();

    static $cache_conn = array();

    static $slow_queue_route = array();

    protected $mq_queue_name = null;

    protected $mq_exchange = null;

    protected $mq_channel = null;

    protected $cache_bind_list = array();

    const DEFAULT_QUEUE_NAME = 'task_queue';

    const SLOW_QUEUE_NAME = 'slow_task_queue';

    const JAVA_QUEUE_NAME = 'java_task_queue';

    const EXCHANGE_NAME = 'exchange_';


    function __construct($conn, $exchangeName = '', $queueName = AMQTools::DEFAULT_QUEUE_NAME)
    {
        if (! $conn->isConnected()) {
            $conn->connect();
        }
        $this->buildExchange($conn,  $exchangeName, $queueName);
    }

    /* 创建交换机 */
    protected function buildExchange($conn, $exchangeName = '', $queueName = AMQTools::DEFAULT_QUEUE_NAME)
    {
        $this->mq_queue_name = $queueName;
        // 创建exchange名称和类型
        $channel = new \AMQPChannel($conn);
        $this->mq_channel = $channel;
        
        $exchange = new \AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE); // 持久化
        $exchange->declareExchange();
        $this->mq_exchange = $exchange;
    }

    /* 连接队列 */
    static function getAMQConn()
    {
    	$vhost=config('amq.vhost');
        if (isset(self::$cache_conn[$vhost]) && self::$cache_conn[$vhost] instanceof \AMQPConnection) {
            return self::$cache_conn[$vhost];
        } else {
            // 连接RabbitMQ
            $conn = new \AMQPConnection(config('amq'));
            $conn->connect();
            self::$cache_conn[$vhost] = $conn;
        }
        return self::$cache_conn[$vhost];
    }


    /* 实例化 */
    static function getInstance($routeKey = '', $queueName = AMQTools::DEFAULT_QUEUE_NAME)
    {
        if (in_array($routeKey, self::$slow_queue_route)) {
            $queueName = AMQTools::SLOW_QUEUE_NAME;
        } else {
            $queueName = (strlen($queueName) > 0) ? $queueName : AMQTools::DEFAULT_QUEUE_NAME;
        }
        
        $exchangeName = AMQTools::EXCHANGE_NAME . $queueName;
        $cache_key = config('amq.vhost') . ':' . $exchangeName;
        if (! isset(self::$cache_exchange[$cache_key])) {
            $conn = self::getAMQConn();
            $obj = new AMQTools($conn, $exchangeName, $queueName);
            self::$cache_exchange[$cache_key] = $obj;
        }
        return self::$cache_exchange[$cache_key];
    }

    /* 绑定队列&路由 */
    protected function bindRouteKey($routeKey)
    {
        if (! isset($this->cache_bind_list[$routeKey])) {
            // 创建queue名称，使用exchange，绑定routingkey
            $queue = new \AMQPQueue($this->mq_channel);
            $queue->setName($this->mq_queue_name);
            $queue->setFlags(AMQP_DURABLE); // 持久化
            $queue->declareQueue();
            $queue->bind($this->mq_exchange->getName(), $routeKey);
        }
    }

    /* 发送消息 */
    static function send($routeKey, $message, $queueName = '')
    {
        $obj = self::getInstance($routeKey, $queueName);
        $obj->bindRouteKey($routeKey);
        $obj->_sendMsg($routeKey, $message);
    }

    static function sendJava($routeKey, $message)
    {
        $obj = self::getInstance($routeKey, self::JAVA_QUEUE_NAME);
        $obj->bindRouteKey($routeKey);
        $res = $obj->mq_exchange->publish(json_encode($message), $routeKey, AMQP_NOPARAM, array(
            'delivery_mode' => 2,
            'priority' => 9
        ));
    }

    /* push msg */
    function _sendMsg($routeKey, $message)
    {
        // 消息发布
        // $channel->startTransaction();
        // 同一时刻，不要发送超过1条消息给一个工作者
        // $channel->qos(0,1);
        $res = $this->mq_exchange->publish(serialize($message), $routeKey, AMQP_NOPARAM, array(
            'delivery_mode' => 2,
            'priority' => 9
        ));
        // $channel->commitTransaction();
    }

    /* 关闭连接 */
    function __destruct()
    {
        foreach (self::$cache_conn as $conn) {
            $conn->disconnect();
        }
    }
    
    

    /**
     * 添加异步队列
     *
     * @param String $name
     *        	队列名称
     * @param String $token
     *        	商家token
     * @param array $argv
     *        	参数
     * @param array $argv2
     *        	参数2
     */
    static function async_queue($name, $cid, $openid, $argv, $argv2 = array(),$queueName = '') {
        $data ['process_name'] = $name;
        $data ['cid'] = $cid;
        $data ['openid'] = $openid;
        $data ['atime'] = time ();
        $data ['argv1'] = @serialize ( $argv );
        $data ['argv2'] = @serialize ( $argv2 );
        $data ['stats'] = 0;
        $id = db ( 'async_queue')->insertGetId( $data );
        $data['id'] = $id;
        try{
        	if(class_exists('\\AMQPConnection')){
	            AMQTools::send($name, $data, $queueName);
        	}
        } catch (\Exception $e) {
        	ThinkLog::error(array('message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()));
        }
        return $id;
    }
    
    static function async_queue_nodb($name, $cid, $openid, $argv, $argv2 = array(),$queueName = '') {
        $data ['process_name'] = $name;
        $data ['cid'] = $cid;
        $data ['openid'] = $openid;
        $data ['atime'] = time ();
        $data ['argv1'] = @serialize ( $argv );
        $data ['argv2'] = @serialize ( $argv2 );
        $data ['stats'] = 0;
        try{
            if(class_exists('\AMQPConnection')){
                AMQTools::send($name, $data, $queueName);
            }
        } catch (\Exception $e) {
    
        }
    }
    
    static function async_queue_java($name, $cid, $openid, $argv=array(), $argv2 = array()) {
        $data ['process_name'] = $name;
        $data ['cid'] = $cid;
        $data ['openid'] = $openid;
        $data ['atime'] = time ();
        $data ['argv1'] = serialize($argv);
        $data ['argv2'] = json_encode($argv2);
        $data ['stats'] = 0;
        $id = db ( 'async_queue')->insertGetId( $data );
        $data['id'] = $id;
        try{
            if(class_exists('\AMQPConnection')){
                AMQTools::sendJava($name, $data);
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

// if(!class_exists('\AMQPConnection')){
//     class AMQPConnection{}
//     class AMQPChannel{}
//     class AMQPQueue{}
//     class AMQPExchange{}
// }

?>