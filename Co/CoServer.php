<?php

namespace Co;

use Swoole\Process;
use Swoole\Coroutine;
use Swoole\Coroutine\Server\Connection;
use Swoole\Coroutine\Http\Server;
use function Swoole\Coroutine\run;

class CoServer
{
    private static $events = [
        ['workerStart', \Co\Events::class, 'onWorkerStart'],
        ['workerStop', \Co\Events::class, 'onWorkerStop'],
//        ['message', \Co\Events::class, 'onMessage'],
    ];

    private Process\Pool $pool;

    public function __construct()
    {
        Utils::setProcessName('master Worker');
        $cpuNum = getCpuNum();

        echo "cpu num{$cpuNum}\n";


        $this->pool = new Process\Pool($cpuNum);//多进程管理模块

        $this->pool->set(['enable_coroutine' => true]);

        foreach (self::$events as $event) {
            $this->pool->on($event['0'], array(new $event['1'], $event['2']));;
        }
    }

    public function start()
    {
        $this->pool->start();
    }

    //获取服务
    public function getServer()
    {
        return $this->pool;
    }
}