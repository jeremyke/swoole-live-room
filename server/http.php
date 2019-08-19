<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 18:20
 */
class http
{
    CONST HOST = "0.0.0.0";
    CONST PORT = 8888;

    public $http = null;
    public function __construct() {
        $this->http = new swoole_http_server(self::HOST, self::PORT);
        $this->http->set(
            [
                'enable_static_handler' => true,
                'task_async' => true,
                //'enable_coroutine'=>true,
                'document_root' => "/data/wwwroot/zhibo/public/static",
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );
        $this->http->on("workerstart", [$this, 'onWorkerStart']);
        $this->http->on("request", [$this, 'onRequest']);
        $this->http->on("task", [$this, 'onTask']);
        $this->http->on("finish", [$this, 'onFinish']);
        $this->http->on("close", [$this, 'onClose']);
        $this->http->start();
    }

    public function onWorkerStart($ser,$worker_id)
    {
        define('APP_PATH', __DIR__ . '/../application/');
        require_once __DIR__ . '/../thinkphp/start.php';
    }

    public function onRequest($request, $response)
    {
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
        $_POST['http_server'] = $this->http;
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
    }
    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data) {
        try{
            //分发task任务
            $obj = new app\common\task\Task();
            $func = $data['func'];
            $res = $obj->$func($data['data']);
        }catch (\Exception $e){
            $res = [
              'success'=>false,
              'msg'=> $e->getMessage(),
              'data'=>'',
            ];
            echo json_encode($res);
        }
        echo json_encode($res);
        return $res; // 告诉worker
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {
        //echo "taskId:{$taskId}\n";
        //echo "finish-data-sucess:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {

    }
}
new http();