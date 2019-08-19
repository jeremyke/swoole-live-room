<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19
 * Time: 18:31
 */
namespace app\index\controller;

use app\common\Redis;
use app\common\Phpredis;

class Send
{
    //发送短信
    public function index()
    {
        try{
            $phone_num = intval($_GET['phone_num']);
            if(empty($phone_num)){
                throw new \Exception('手机号不能为空');
            }
            if(Phpredis::getInstance()->get(Redis::smsKey($phone_num))){
                throw new \Exception('2分钟内请勿反复尝试');
            }
            //产生6位随机数
            $code = rand(pow(10,(5)), pow(10,6)-1);

            //异步发送验证码
            $task_data = [
                'func' => 'sendSms',
                'data' =>[
                    'phone_num'   =>  $phone_num,
                    'code'        =>  $code,
                ]
            ];
            $_POST['http_server']->task($task_data);
            $res = [
                'success'   => true,
                'msg'   => '发送成功',
                'data'   => '',
            ];
            echo json_encode($res);
            return;
        }catch (\Exception $e){
            $res = [
                'success'   => false,
                'msg'   => $e->getMessage(),
                'data'   => '',
            ];
            echo json_encode($res);
            return;
        }

    }
}