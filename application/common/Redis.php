<?php
namespace app\common;

class Redis
{
    //验证码前缀
    public static $sms_pre = "sms_";
    public static $usr_pre = "usr_";
    public static function smsKey($tel_num)
    {
        return self::$sms_pre.$tel_num;
    }
    //用户前缀
    public static function userKey($tel_num)
    {
        return self::$usr_pre.$tel_num;
    }
}