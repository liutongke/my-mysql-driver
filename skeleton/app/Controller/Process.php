<?php

namespace App\Controller;

class Process
{
    //添加用户自定义的工作进程 https://wiki.swoole.com/#/server/methods?id=addprocess
    public function addProcess($server)
    {
        return new \Swoole\Process(function ($process) use ($server) {
            while (true) {
                \Co::sleep(30);
                $now = date("Y-m-d H:i:s");
                echo "Hello, api-swoole!{$now}\r\n";
            }
        }, false, 2, 1);
    }
}