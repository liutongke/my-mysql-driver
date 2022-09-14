<?php

namespace App\Controller;

use App\Ext\Redis;

class RedisDemo
{
    public function setData(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $redis = new \Simps\DB\BaseRedis();
        $res = $redis->set($request->post['key'], $request->post['val']);
        return [
            "code" => 200,
            "msg" => "hello World!",
            "data" => [
                'res' => $res,
                'tm' => date('Y-m-d H:i:s'),
                'key' => $request->post['key'],
                'val' => $request->post['val'],
            ],
        ];
    }
}