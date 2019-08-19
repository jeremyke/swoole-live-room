<?php
/**
 * 同步阻塞redis客户端
 * User: Administrator
 * Date: 2018/12/27
 * Time: 20:16
 */
namespace app\common;
class Phpredis
{
    public $redis;
    private static $instance = null;
    private function __construct()
    {
        $this->redis = new \Redis();
        $res = $this->redis->connect(config('redis.host'),config('redis.port'),config('redis.out_time'));
        if(!$res){
            throw new \Exception('redis连接失败');
        }
    }
    private function __clone(){}
    public static function getInstance()
    {
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    //设置
    public function set($key,$value,$time=0)
    {
        if(!$key){
            return "";
        }
        if(is_array($value)){
            $value = json_encode($value);
        }
        if(empty($time)){
            $this->redis->set($key,$value);
        }else{
            $this->redis->setex($key,$time,$value);
        }
    }
    //获取
    public function get($key)
    {
        if(!$key){
            return "";
        }
        return $this->redis->get($key);
    }

    //操作有序集合
    public function sadd($key,$value)
    {
        return $this->redis->sAdd($key,$value);
    }
    public function srem($key,$value)
    {
        return $this->redis->sRem($key,$value);
    }

    //有序集合所有值
    public function smembers($key)
    {
        return $this->redis->sMembers($key);
    }
    //删除集合的所有值
    public function dmembers($key)
    {
        $members = $this->redis->sMembers($key);
        foreach ($members as $k){
            return $this->redis->sPop($k);
        }
    }


    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $args = explode(',',$arguments);
        $this->redis->$name($args);
    }
}