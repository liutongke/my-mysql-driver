<?php

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use function Swoole\Coroutine\run;

//$http = new Swoole\CoHttp\Server('0.0.0.0', 9500);
//
//$http->on('Request', function ($request, $response) {
//    $response->header('Content-Type', 'text/html; charset=utf-8');
//    $response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');
//});
//
//$http->start();
//$redis = new Redis();
//$redis->connect('192.168.0.102', 6379, 60);
//
//$sql = 'select * from tb_order order by id desc limit 10';
////伪代码，从数据库中获取数据
////$data = $db->query($sql);
////$data = json_encode($data, JSON_UNESCAPED_UNICODE);
//$key = md5($sql);
////缓存数据
//$redis->set($key, $sql, 600);
//
////获取数据
//$data = $redis->get($key);
//var_dump($data);
class test
{
    public static $workPool = [];

    public function WorkerStart($ws, $workerId)
    {
        var_dump($workerId);
//        $channel = new Channel(1);
//        self::$workPool[$workerId] = $channel;
//        $this->StartOneWorker($workerId, $channel);//启动协程
    }

    public function open($ws, $request)
    {
        $ws->push($request->fd, "hello, welcome\n");
        echo "workdId:" . $ws->getWorkerId() . "\n";
    }

    public function Message($ws, $frame)
    {
        $this->SendData(rand(0, 9));
        echo "Message: {$frame->data}\n";
        echo "workdId:" . $ws->getWorkerId() . "\n";
        $ws->push($frame->fd, "server: {$frame->data}");
    }

    public function Close($ws, $fd)
    {
        echo "client-{$fd} is closed\n";
    }

    public function run()
    {
        $this->ws->start();
    }

    //服务对象
    private $ws;
    private static $worker = ["WorkerStart", "open", "message", "close"];

    public function __construct()
    {
        run(function () {
            for ($i = 1; $i <= 10; $i++) {
                $channel = new Channel(1);
                self::$workPool[$i] = $channel;
                $this->StartOneWorker($i, $channel);//启动协程
            }
        });
        $this->ws = new Swoole\WebSocket\Server("0.0.0.0", 9500);
        foreach (self::$worker as $workerInfo) {
            $this->ws->on($workerInfo, [$this, $workerInfo]);
        }
//        $this->ws->on('WorkerStart', [$this, 'WorkerStart']);
//        $this->ws->on('open', [$this, 'open']);
//        $this->ws->on('message', [$this, 'message']);
//        $this->ws->on('close', [$this, 'close']);
    }

    public function SendData($workId)
    {
//        var_dump(self::$workPool);
//        run(function () use ($workId) {
        self::$workPool[$workId]->push(['rand' => rand(1000, 9999), 'index' => time(), 'chan' => $workId]);
//        });
    }

    public function StartOneWorker($workId, $chan)
    {
        go(function () use ($workId, $chan) {
            while (true) {
                $data = $chan->pop(-1);
                if ($data) {
                    var_dump("workId:" . $workId);
                    var_dump("----------");
                    var_dump($data);
                } else {
                    assert($chan->errCode === SWOOLE_CHANNEL_TIMEOUT);
                    break;
                }
            }
        });
    }
}

(new test())->run();