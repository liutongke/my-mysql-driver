<?php

namespace Co;

use Swoole\Process;
use Swoole\Coroutine;
use Swoole\Coroutine\Server\Connection;
use Swoole\Coroutine\Http\Server;
use function Swoole\Coroutine\run;

class Events
{
    public $server;

    public function onWorkerStart(\Swoole\Process\Pool $pool, int $workerId)
    {

        Utils::setProcessName("onWorkerStart workerId:{$workerId}");
        $this->server = new Server('0.0.0.0', 9502, false, true);
        $s = $this->server;
        //收到15信号关闭服务
        Process::signal(SIGUSR1, function () use ($s) {
            $s->shutdown();
            while ($ret = Swoole\Process::wait(false)) {
                echo "PID={$ret['pid']}\n";
            }
        });

        $this->handler();

        $this->server->start();
    }

    private function handler()
    {
//        call_user_func_array($routeInfo, [$request, $response, $server]);
        $this->server->handle('/', function ($request, $response) {
            var_dump($request->server['request_uri']);
            $response->end("<h1>Index1111Index1111Index1111</h1>");
        });

        $this->server->handle('/websocket', function (\Swoole\Http\Request $request, \Swoole\Http\Response $ws) {
            $ws->upgrade();
            while (true) {
                $frame = $ws->recv();
                if ($frame === '') {
                    $ws->close();
                    break;
                } else if ($frame === false) {
                    echo 'errorCode: ' . swoole_last_error() . "\n";
                    $ws->close();
                    break;
                } else {
                    if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
                        $ws->close();
                        break;
                    }
                    $ws->push("Hello {$frame->data}!");
                    $ws->push("How are you, {$frame->data}?");
                }
            }
        });
    }

    public function onWorkerStop(\Swoole\Process\Pool $pool, int $workerId)
    {

    }

    public function onMessage(\Swoole\Process\Pool $pool, string $data)
    {

    }
}