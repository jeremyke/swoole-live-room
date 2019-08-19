<?php
namespace app\common\task;

use app\common\Sms;
use app\common\Redis;
use app\common\Phpredis;
/**
 * swoole所有异步任务
 * @package app\common\task
 */
class Task
{
    public function sendSms($data,$server)
    {
        try{
            //发送短信
            $sms_ser = new Sms();
            $res = $sms_ser->sendSms($data['phone_num'],$data['code']);
            Phpredis::getInstance()->set(Redis::smsKey($data['phone_num']),$data['code'],config('redis.out_time'));
            //协成存入redis
            /*$redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'),config('redis.port'));
            $redis->setDefer();
            $redis->set(Redis::smsKey($data['phone_num']),$data['code'],config('redis.out_time'));*/

        }catch (\Exception $e){
            echo $e->getMessage();
        }

    }

    public function pushLive($data,$server)
    {
        //获取连接的用户
        $clients = Phpredis::getInstance()->sMembers(config('comconfig.redis.live_game_key'));
        foreach ($clients as $fd){
            $server->push($fd,json_encode($data));
        }
    }
}