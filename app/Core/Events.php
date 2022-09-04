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

namespace chat\sw\Core;

use chat\sw\Router\HttpRouter;
use chat\sw\Router\WsRouter;

class Events
{
    private $server;

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function onOpen(\Swoole\WebSocket\Server $server, $request)
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public function onClose($ser, $fd)
    {
        echo "client {$fd} closed\n";
    }

    public function onMessage(\Swoole\WebSocket\Server $server, $frame)
    {
//        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        DI()->runTm->start();

        $ws = new WsResponse($server);
        $ws->setFd($frame->fd);

        register_shutdown_function(function () use ($server, $frame, $ws) {
            $error = error_get_last();

            if (!empty($error)) {
                DI()->logger->error($error['message']);
                $ws->setStatus(HttpCode::$StatusInternalServerError);
                $ws->setCode(HttpCode::$StatusInternalServerError);
                $ws->setMsg($error);
                $ws->output();
            }
        });

        $this->handlerWsData($server, $frame, $ws);

        DI()->logger->echoWsCmd($this->server, $frame->fd, DI()->runTm->end());

        $ws->output();
    }

    public function handlerWsData(\Swoole\WebSocket\Server $server, $frame, \chat\sw\Core\WsResponse $ws)
    {
        $res = json_decode($frame->data, true);
        $list = WsRouter::GetHandlers();
        if (!is_array($res) || !isset($list[strtolower($res['path'])]) || empty($res)) {
            $ws->setCode(HttpCode::$StatusBadRequest);
            $ws->setMsg('data err');
        }
        $c = $list[strtolower($res['path'])];
        $api = $c['0'];
        $action = $c['1'];
        //先处理必须携带的参数
        $rule = $api->getByRule($res['data'], $action);
        if ($rule['res']) {//验证未通过
            $ws->setId($res['id']);
            $ws->setCode(HttpCode::$StatusBadRequest);
            $ws->setMsg($rule['data']);
            $ws->setPath($res['path']);
            $ws->setData($res['data']);
        }
        $data = $api->{$action}($server, $res);

        $ws->setId($res['id']);
        $ws->setCode(HttpCode::$StatusOK);
        $ws->setPath($res['path']);
        $ws->setData($data);
    }

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        if (
            $request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico' ||
            $request->server['request_uri'] == '/favicon.png'
        ) {
            $response->end();
            return;
        }

        DI()->runTm->start();

        $rs = new HttpResponse($response);

        register_shutdown_function(function () use ($rs) {
            $error = error_get_last();
            if (!empty($error)) {
                DI()->logger->error($error['message']);
                $rs->setStatus(HttpCode::$StatusInternalServerError);
                $rs->setCode(HttpCode::$StatusInternalServerError);
                $rs->setData($error['message']);
                $rs->output();
//                Error::getInstance()->httpBadRequest($request, $response, $error);
            }
        });

        $pathUrl = strtolower($request->server['path_info']);//请求的地址
        $setUrlList = HttpRouter::GetHandlers();
        if (!isset($setUrlList[$pathUrl])) {
            DI()->logger->error($request);
            $rs->setStatus(HttpCode::$StatusNotFound);
            $rs->setCode(HttpCode::$StatusNotFound);
            $rs->setData('url not find');
            $rs->output();
            return;
        }

        try {
            $rps = call_user_func_array($setUrlList[$pathUrl], [$request, $response, $this->server]);
        } catch (\Exception $e) {
            echo "------->";
            echo $e->getMessage();
        }

        DI()->logger->echoHttpCmd($request, $response, $this->server, DI()->runTm->end());

        $rs->setStatus(HttpCode::$StatusOK);
        $rs->setCode(HttpCode::$StatusOK);
        $rs->setData($rps);
        $rs->output();
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
        echo "Tasker进程接收到数据,task_id:";
        var_dump($task_id);
        var_dump($data);
    }

    public function onFinish(\Swoole\Server $server, int $task_id, int $src_worker_id, array $data)
    {

    }

    public function onStart(\Swoole\Server $server)
    {
        $host = $server->host;
        $port = $server->port;
        Logger::echoSuccessCmd("Swoole Http Server running：http://{$host}:{$port}");
        Logger::echoSuccessCmd("Swoole websocket Server running：ws://{$host}:{$port}");
    }
}