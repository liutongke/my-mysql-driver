<?php


namespace App\Controller;

use Sapi\Api;
use Sapi\HttpCode;

class ChatAuth extends Api
{
    public function rule()
    {
        return [
            'login' => [
                'username' => ['name' => 'username', 'require' => true],
                'password' => ['name' => 'password', 'require' => true],
            ]
        ];
    }

    //登录
    public function login(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {
        $username = $request->post['username'];
        $password = $request->post['password'];
        $localIp = getLocalIp();


        $token = md5($username . $password);
        $redis = new \Simps\DB\BaseRedis();

        $pipe = $redis->multi(\Redis::PIPELINE);
        $pipe->set($token, $username);
        $pipe->hmset($username, $localIp);
        $pipe->expire($token, 86400);
        $pipe->expire($username, 86400);
        $replies = $pipe->exec();

        return [
            "code" => HttpCode::$StatusOK,
            "msg" => "success",
            "data" => [
                'localIp' => $localIp,
                'token' => $token,
                '$replies' => $replies
            ],
        ];
    }
}