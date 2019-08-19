<?php
namespace app\admin\controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/12
 * Time: 11:27
 */
//
class Image
{
    public function index()
    {
        $files = request()->file('file');
        $info = $files->move('../public/static/upload');
        if($info){
            $data = [];
            $data['image'] = config('comconfig.host')."/upload/".$info->getSaveName();

            $return_res = [
              'success' => true,
              'msg'     => '上传成功',
              'data'    => $data,
            ];
        }else{
            $return_res = [
                'success' => false,
                'msg'     => '上传失败',
                'data'    => '',
            ];
        }
        return json_encode($return_res);
    }
}