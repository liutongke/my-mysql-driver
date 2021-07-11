<?php
/*
 * User: keke
 * Date: 2021/7/11
 * Time: 2:20
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

use Swoole\Process;
use Swoole\Coroutine;
use Swoole\Coroutine\Server\Connection;

use Swoole\Coroutine\Channel;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\CloseFrame;
use Swoole\Coroutine\Http\Server;
use function Swoole\Coroutine\run;

class goWs
{
    public $pool;
    public static $workPool = [];

    public function __construct()
    {
        run(function () {
            for ($i = 1; $i <= 10; $i++) {
                $channel = new Channel(100);
                self::$workPool[$i] = $channel;
                $this->StartOneWorker($i, $channel);//启动协程
            }
        });
        //多进程管理模块
        $this->pool = new Process\Pool(2);
        //让每个OnWorkerStart回调都自动创建一个协程
        $this->pool->set(['enable_coroutine' => true]);
        $this->pool->on('workerStart', function ($pool, $id) {
            var_dump($pool, $id);
            $server = new Swoole\Coroutine\Http\Server('0.0.0.0', 9500, false, true);
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
                        $ws->push("来自远方的问候Server：{$frame->data}");
//                        foreach ($wsObjects as $obj) {
//                            $obj->push("Server：{$frame->data}");
//                        }
                    }
                }
            });
            $server->start();
        });
    }

    public function start()
    {
        $this->pool->start();
    }

    public function SendData($workId, $data)
    {
//        var_dump(self::$workPool);
//        run(function () use ($workId) {
//        self::$workPool[$workId]->push(['rand' => rand(1000, 9999), 'index' => time(), 'chan' => $workId]);
        self::$workPool[$workId]->push($data);
//        });
    }

    public function StartOneWorker($workId, $chan)
    {
        go(function () use ($workId, $chan) {
//            var_dump("进程id：" . posix_getpid());
//            var_dump("获取当前协程的父 ID=>" . Co::getPcid());
//            var_dump("获取当前协程的唯一 ID, 它的别名为 getuid, 是一个进程内唯一的正整数=>" . Co::getcid());
            while (true) {
                $data = $chan->pop(-1);
                if ($data) {
                    var_dump("workId:" . $workId);
                    var_dump("----------");
                    var_dump($data);
                    $data['ws']->push($data['data']);
                } else {
                    assert($chan->errCode === SWOOLE_CHANNEL_TIMEOUT);
                    break;
                }
            }
        });
    }
}

(new goWs())->start();