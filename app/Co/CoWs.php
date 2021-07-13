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

use chat\sw\Router\HttpRouter;
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
    private $_config;
    private static $worker = ["WorkerStart"];

    public function __construct()
    {
        $this->_config = DI()->config->get('conf.ws');
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
        var_dump($pool);
//        $http = new \Swoole\Http\Server('0.0.0.0', 9501);
//
//        $http->on('Request', function ($request, $response) {
//            $response->header('Content-Type', 'text/html; charset=utf-8');
//            $response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');
//        });

//        $http->start();
        $this->startServ($pool, $workerId);
    }

    private function startServ($pool, $workerId)
    {
        $server = new Server($this->_config['host'], $this->_config['port'], $this->_config['ssl'], $this->_config['reuse_port']);
        $this->http($server, $pool, $workerId);
        $server->start();
    }

    private function http($server, $pool, $workerId)
    {
        $list = HttpRouter::GetHandlers();
        foreach ($list as $url => $objInfo) {//$server->handle('/Index', [new App(), 'Index']);
//            $server->handle($key, $value);
            $server->handle($url, function ($request, $response) use ($server, $objInfo, $pool, $workerId) {
                call_user_func_array($objInfo, [$request, $response, $workerId]);
            });
        }
        $server->handle('/Index', [new App(), 'Index']);
        $server->handle('/test', function ($request, $response) {
            $rand = rand(1111, 9999);
            $response->end("<h1>Index1</h1>{$rand}");
        });
        $server->handle('/stop', function ($request, $response) use ($server) {
            (new App())->stop($request, $response, $server);
//            $response->end("<h1>Stop3</h1>");
//            $server->shutdown();
        });
    }

    public function start()
    {
        $this->pool->start();
    }
}