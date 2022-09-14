<?php

namespace App\Controller;

use Sapi\Api;

class Websocket extends Api
{
//    public function userCheck()
//    {
//        return "需要userCheck";
//    }

    public function rule()
    {
        return [
            'login' => [
                'username' => ['name' => 'username', 'require' => true]
            ]
        ];
    }

    public function index(\Swoole\WebSocket\Server $server, array $msg): array
    {
        return [
            'err' => 200,
            'data' => [
                'name' => 'api-swoole',
                'version' => '1.0.0',
            ]
        ];
    }

    public function login(\Swoole\WebSocket\Server $server, array $msg): array
    {
        return [
            'err' => 200,
            'data' => [
                'username' => $msg['username'],
            ]
        ];
    }

}