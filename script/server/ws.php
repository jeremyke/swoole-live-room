<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 18:20
 */
class Ws
{
    CONST HOST = "0.0.0.0";
    CONST PORT = 8888;
    CONST CHART_PORT = 8899;

    public $ws = null;
    public function __construct() {
        //删除redis里面所有的客户端id
        //app\common\Phpredis::getInstance()->dmembers(config('comconfig.redis.live_game_key'));
        $this->ws = new swoole_websocket_server(self::HOST, self::PORT);
        $this->ws->listen(self::HOST,self::CHART_PORT,SWOOLE_SOCK_TCP);
        $this->ws->set(
            [
                'enable_static_handler' => true,
                'task_async' => true,
                //'enable_coroutine'=>true,
                'document_root' => "/data/wwwroot/zhibo/public/static",
                'worker_num' => 4,
                'task_worker_num' => 4,
            ]
        );
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("workerstart", [$this, 'onWorkerStart']);
        $this->ws->on("request", [$this, 'onRequest']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);
        $this->ws->start();
    }

    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request) {
        //将客户端id存入redis
        //var_dump($ws);
        app\common\Phpredis::getInstance()->sadd(config('comconfig.redis.live_game_key'),$request->fd);
        //var_dump($request->fd);
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame) {
        echo "ser-push-message:{$frame->data}\n";
        $ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));
    }

    public function onWorkerStart($ser,$worker_id)
    {
        define('APP_PATH', __DIR__ . '/../../application/');
        require_once __DIR__ . '/../../thinkphp/start.php';
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
        $this->writeLog();
        $_POST['http_server'] = $this->ws;
        $_FILES = [];
        if(isset($request->files)){
            foreach ($request->files as $k=>$v){
                $_FILES[$k] = $v;
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
            $res = $obj->$func($data['data'],$serv);
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
        //将客户端id从redis删除
        app\common\Phpredis::getInstance()->srem(config('comconfig.redis.live_game_key'),$fd);
        //echo "客户端{$fd}关闭\n";
    }

    //记录日志
    public function writeLog()
    {
        $data = [];
        $data['date'] = date('Y-m-d H:i:s');
        if(isset($_GET)&&!empty($_GET)){
            $data['get'] = $_GET;
        }
        if(isset($_POST)&&!empty($_POST)){
            $data['post'] = $_POST;
        }
        if(isset($_SERVER)&&!empty($_SERVER)){
            $data['server'] = $_SERVER;
        }
        $logs= "";
        foreach ($data as $k=>$v){
            if(is_array($v)){
                foreach ($v as $k1=>$v1){
                    $logs .= $k1 .":".json_encode($v1)." ";
                }
            }else{
                $logs .= $k .":".$v." ";
            }
        }
        //异步IO写入文件
        swoole_async_writefile(APP_PATH."../runtime/log/".date('Ym')."/".date('d')."_access.log",$logs.PHP_EOL,function ($filename){

        },FILE_APPEND);
    }
}
new ws();