<?php
/*
 * User: keke
 * Date: 2021/7/12
 * Time: 10:37
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

namespace chat\sw\Co;

use Swoole\Process;
use Swoole\Coroutine;
use Swoole\Coroutine\Server\Connection;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\CloseFrame;
use Swoole\Coroutine\Http\Server;
use function Swoole\Coroutine\run;

class CoWs
{
    private $pool;//进程池

    private static $worker = ["WorkerStart"];

    public function __construct()
    {
        //多进程管理模块
        $this->pool = new Process\Pool(2);
        //让每个OnWorkerStart回调都自动创建一个协程
        $this->pool->set(['enable_coroutine' => true]);
        foreach (self::$worker as $workerInfo) {
            $this->pool->on($workerInfo, [$this, $workerInfo]);
        }
    }

    public function WorkerStart($pool, $workerId)
    {
        $this->ws();//websocket
    }

    private function http($server)
    {
        $server->handle('/test', function ($request, $response) {
            $rand = rand(1111, 9999);
            $response->end("<h1>Index1</h1>{$rand}");
        });
        $server->handle('/stop', function ($request, $response) use ($server) {
            $response->end("<h1>Stop3</h1>");
            $server->shutdown();
        });
    }

    private function ws()
    {
        $server = new Server('0.0.0.0', 9501, false, true);
        $this->http($server);
        $server->handle('/ws', function (Request $request, Response $ws) {
            $ws->upgrade();
            global $wsObjects;
            $objectId = spl_object_id($ws);
            $wsObjects[$objectId] = $ws;
            while (true) {
                $frame = $ws->recv();
                if ($frame === '') {
                    unset($wsObjects[$objectId]);
                    $ws->close();
                    break;
                } else if ($frame === false) {
                    echo 'errorCode: ' . swoole_last_error() . "\n";
                    $ws->close();
                    break;
                } else {
                    if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
                        unset($wsObjects[$objectId]);
                        $ws->close();
                        break;
                    }
                    $ws->push(Router::MsgHandle($ws, $frame));
                }
            }
        });
        $server->start();
    }

    public function start()
    {
        $this->pool->start();
    }
}