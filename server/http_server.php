<?php
$http = new swoole_http_server("0.0.0.0", 8888);

$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/data/wwwroot/zhibo/public/static",
        'worker_num' => 5,
    ]
);
$http->on('WorkerStart',function ($ser,$worker_id){
    //define('APP_PATH', __DIR__ . '/../application/');
    //require __DIR__ . '/../thinkphp/base.php';
});
$http->on('request', function($request, $response) use($http) {
    define('APP_PATH', __DIR__ . '/../application/');
    require_once __DIR__ . '/../thinkphp/base.php';
    if(isset($request->header)){
        foreach ($request->header as $k=>$v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    if(isset($request->server)){
        foreach ($request->server as $k=>$v){
            $_HEADER[strtoupper($k)] = $v;
        }
    }
    $_GET = [];
    if(isset($request->get)){
        foreach ($request->get as $k=>$v){
            $_GET[$k] = $v;
        }
    }
    $_POST = [];
    if(isset($request->post)){
        foreach ($request->post as $k=>$v){
            $_POST[$k] = $v;
        }
    }
    // 执行应用并响应
    //开启缓存
    ob_start();
    try{
        think\Container::get('app', [APP_PATH])->run()->send();
    }catch (\Exception $e){

    }
    $res = ob_get_contents();
    ob_end_clean();
    $response->end($res);
    //$http->close();
});

$http->start();