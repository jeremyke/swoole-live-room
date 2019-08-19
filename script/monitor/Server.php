<?php
/**
 * Description:监控服务
 * User: Jeremy.Ke
 * Time: 2019/1/14 14:23
 */
class Server
{
    const PORT = 8888;
    public function port()
    {
        $shell = "netstat -anp 2>/dev/null | grep 8888 | grep LISTEN | wc -l";
        $res = shell_exec($shell);
        var_dump($res);
        if($res !=1){
            var_dump($res);
        }
    }
}
swoole_timer_tick(2000,function ($timer_id){
    (new Server())->port();
    echo "time-start".PHP_EOL;
});
