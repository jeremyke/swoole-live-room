<?php
namespace app\admin\controller;

use app\common\Phpredis;
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/1/12 15:31
 */
class Live
{
    //数据入库，发送到直播页面
    public function push()
    {
        if(empty($_GET)){
            $return_res = [
                'success'=>false,
                'msg'=>"参数缺失",
                'data'=>"",
            ];
            return json_encode($return_res);
        }
        $teams = [
            1=>[
                'name'  => '马刺',
                'logo'  => '/liv/imgs/team1.png',
            ],
            4=>[
                'name'  => '火箭',
                'logo'  => '/liv/imgs/team2.png',
            ],
        ];
        $data = [
            'type'  => intval($_GET['type']),
            'title' => !empty($teams[$_GET['team_id']]['name'])?$teams[$_GET['team_id']]['name']:"直播员",
            'logo'  => !empty($teams[$_GET['team_id']]['logo'])?$teams[$_GET['team_id']]['logo']:"",
            'content'=> !empty($_GET['content'])?$_GET['content']:"",
            'image'=> !empty($_GET['image'])?$_GET['image']:"",
        ];
        //异步推送给所有用户
        $task_data = [
            'func'  => 'pushLive',
            'data'  => $data,
        ];
        $_POST['http_server']->task($task_data);
        $return_res = [
            'success'=>true,
            'msg'=>"发送成功",
            'data'=>"",
        ];
        return json_encode($return_res);


    }
}