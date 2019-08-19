<?php
namespace app\index\controller;

use app\common\Sms;

class Index
{
    public function index()
    {
        return "";
    }

    public function hello($name = 'ThinkPHP5')
    {
        echo 'hello,' . $name;
    }
    public function send()
    {
        $sms_server = new Sms();
        $res = $sms_server->senSms('15013526701');
        var_dump($res);
    }
}
