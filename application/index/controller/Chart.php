<?php
namespace app\index\controller;
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/1/13 18:49
 */
class Chart
{
    public function index()
    {
        if(empty($_POST['game_id'])){
            $return_res = [
                'success'=>false,
                'msg'=>"参数缺失",
                'data'=>"",
            ];
            return json_encode($return_res);
        }
        if(empty($_POST['content'])){
            $return_res = [
                'success'=>false,
                'msg'=>"参数缺失",
                'data'=>"",
            ];
            return json_encode($return_res);
        }
        $data = [
           'user'   => "松夏",
           'content'    =>  $_POST['content'],
        ];
        var_dump($_POST['http_server']->ports[1]->connections);
        foreach ($_POST['http_server']->ports[1]->connections as $fd){
            $_POST['http_server']->push($fd,json_encode($data));
        }
    }
}