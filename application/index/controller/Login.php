<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/24
 * Time: 19:16
 */
namespace app\index\controller;

use app\common\Phpredis;
use app\common\Redis;

class Login
{
    public function index()
    {
        try{
            //获取手机号
            $phone = intval($_GET['phone_num']);
            $usr_code = intval($_GET['code']);
            if(empty($phone) || empty($usr_code)){
                $return_res = [
                    'success' =>   false,
                    'msg' =>   "手机号或者验证码不能为空",
                    'data' =>   "",
                ];
                return json_encode($return_res);
            }
            //和redis匹配手机号
            $redis_code = Phpredis::getInstance()->get(Redis::smsKey($phone));
            if($redis_code==$usr_code){
                //记录用户登录信息
                $user_data = [
                    'user'    => $phone,
                    'key'     => md5(Redis::userKey($phone)),
                    'time'    => time(),
                    'is_login'    => true,
                ];
                Phpredis::getInstance()->set(Redis::userKey($phone),$user_data);
                $return_res = [
                    'success' =>   true,
                    'msg' =>   "登录成功",
                    'data' =>   "0",
                ];
                return json_encode($return_res);;
            }else{
                $return_res = [
                    'success' =>   false,
                    'msg' =>   "验证码不正确",
                    'data' =>   "0",
                ];
                return json_encode($return_res);
            }
        }catch (\Exception $e){
            $return_res = [
                'success' =>   false,
                'msg' =>   $e->getMessage(),
                'data' =>   "0",
            ];
            return json_encode($return_res);
        }

    }
}