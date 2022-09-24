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

        $this->pool->set($this->set());

        foreach (self::$events as $event) {
            $this->pool->on($event['0'], array(new $event['1'], $event['2']));;
        }
        $this->oneProcess();
    }

    private function oneProcess()
    {
        $process = new Process(function ($process) {
            echo 'start oneProcess' . PHP_EOL;
            while (true) {
                run(function () use ($process) {
                    $socket = $process->exportSocket();
                    echo $socket->recv();
                    $socket->send("hello master\n");
                    echo "proc1 stop\n";
                });
            }
        });
        $process->start();
//        $status = Process::wait(true);
//        var_dump($status);
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

    private function set(): array
    {
        return ['enable_coroutine' => true];
    }
}