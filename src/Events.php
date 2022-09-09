<?php
/*
 * User: keke
 * Date: 2021/7/16
 * Time: 11:30
 *——————————————————佛祖保佑 ——————————————————
 *                   _ooOoo_
 *                  o8888888o
 *                  88" . "88
 *                  (| -_- |)
 *                  O\  =  /O
 *               ____/`---'\____
 *             .'  \|     |//  `.
 *            /  \|||  :  |||//  \
 *           /  _||||| -:- |||||-  \
 *           |   | \\  -  /// |   |
 *           | \_|  ''\---/''  |   |
 *           \  .-\__  `-`  ___/-. /
 *         ___`. .'  /--.--\  `. . __
 *      ."" '<  `.___\_<|>_/___.'  >'"".
 *     | | :  ` - `.;`\ _ /`;.`/ - ` : | |
 *     \  \ `-.   \_ __\ /__ _/   .-` /  /
 *======`-.____`-.___\_____/___.-`____.-'======
 *                   `=---='
 *——————————————————代码永无BUG —————————————————
 */

namespace Sapi;

class Events
{
    private $server;

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function onOpen(\Swoole\WebSocket\Server $server, $request)
    {
        if (DI()->config->get('conf.debug')) {
            echo "server: handshake success with fd{$request->fd}\n";
        }
    }

    public function onClose($ser, $fd)
    {
        if (DI()->config->get('conf.debug')) {
            echo "client {$fd} closed\n";
        }
    }

    public function onMessage(\Swoole\WebSocket\Server $server, $frame)
    {
//        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        WsRequest::getInstance()->handlerMsg($server, $frame);
    }

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        HttpRequest::getInstance()->handlerMsg($request, $response, $this->server);
    }

    public function onWorkerStart(\Swoole\Server $server, int $workerId)
    {
        if ($server->taskworker) {
            self::setProcessName("swoole server task:{$workerId}");
        } else {
            self::setProcessName("swoole server worker:{$workerId}");
        }
    }

    public static function setProcessName(string $processName)
    {
        swoole_set_process_name($processName);
    }

    //使用 task 必须为 Server 设置 onTask 和 onFinish 回调，否则 Server->start 会失败
    public function onTask(\Swoole\Server $server, int $task_id, int $src_worker_id, array $data)
    {
        echo "Tasker进程接收到数据,task_id:{$task_id}";
    }

    public function onFinish(\Swoole\Server $server, int $task_id, int $src_worker_id, array $data)
    {

    }

    public function onStart(\Swoole\Server $server)
    {
        echo <<<EOL
                 _  _____                    _      
     /\         (_)/ ____|                  | |     
    /  \   _ __  _| (_____      _____   ___ | | ___ 
   / /\ \ | '_ \| |\___ \ \ /\ / / _ \ / _ \| |/ _ \
  / ____ \| |_) | |____) \ V  V / (_) | (_) | |  __/
 /_/    \_\ .__/|_|_____/ \_/\_/ \___/ \___/|_|\___|
          | |                                       
          |_|                                       
EOL. "\n";

        $phpVersion = phpversion();
        $swooleVersion = SWOOLE_VERSION;

        Logger::echoSuccessCmd("Swoole: {$swooleVersion}, PHP: {$phpVersion}, Port: {$server->port}");
        Logger::echoSuccessCmd("Swoole Http Server running：http://{$server->host}:{$server->port}");
        Logger::echoSuccessCmd("Swoole websocket Server running：ws://{$server->host}:{$server->port}");

        $tcp_config = DI()->config->get('conf.tcp');
        if (isset($tcp_config) && isset($tcp_config)) {
            Logger::echoSuccessCmd("Swoole tcp Server running：{$tcp_config['host']}:{$tcp_config['port']}");
        }

        $udp_config = DI()->config->get('conf.udp');
        if (isset($udp_config) && isset($udp_config)) {
            Logger::echoSuccessCmd("Swoole udp Server running：{$udp_config['host']}:{$udp_config['port']}");
        }
    }
}