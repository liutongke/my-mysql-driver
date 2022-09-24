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

        Process::signal(SIGTERM, function () {
            echo "TERM\n";
        });

        $this->handler($pool);

        $this->server->start();
    }

    private static string $ws = 'upgrade';

    private function handler(\Swoole\Process\Pool $pool)
    {
        $this->server->handle('/', function (\Swoole\Http\Request $request, \Swoole\Http\Response $ws) use ($pool) {
            if (isset($request->header['connection']) && strtolower($request->header['connection']) == self::$ws) {
                $this->websocket($request, $ws);
            } else {
                $this->http($request, $ws, $pool);
            }
        });
    }

    private function http(\Swoole\Http\Request $request, \Swoole\Http\Response $response, \Swoole\Process\Pool $pool)
    {
        $socket = $pool->exportSocket();
        $socket->send("hello pro1\n");
//        var_dump($socket->recv());
        $response->end("<h1>IIIIIIIIIIIIIIIIIIII</h1>");
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