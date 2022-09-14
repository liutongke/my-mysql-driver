<?php

namespace App\Controller;

class TcpServe
{
    public function onReceive(\Swoole\Server $server, $fd, $threadId, $data)
    {
        echo $data;
    }
}