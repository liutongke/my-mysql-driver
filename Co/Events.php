<?php

namespace Co;

use Sapi\HttpRequest;
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

    private static string $ws = 'Upgrade';

    private function handler()
    {
        $this->server->handle('/', function (\Swoole\Http\Request $request, \Swoole\Http\Response $ws) {
            if ($request->header['connection'] != self::$ws) {
                $this->http($request, $ws);
            } else {
                $this->websocket($request, $ws);
            }
        });
    }

    private function http(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $response->end("<h1>Index1111Index1111Index1111</h1>");
    }

    private function websocket(\Swoole\Http\Request $request, \Swoole\Http\Response $ws)
    {
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
    }

    public function onWorkerStop(\Swoole\Process\Pool $pool, int $workerId)
    {

    }

    public function onMessage(\Swoole\Process\Pool $pool, string $data)
    {

    }
}