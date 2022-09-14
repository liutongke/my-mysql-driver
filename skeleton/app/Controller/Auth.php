<?php

namespace App\Controller;

use Sapi\Api;

class Auth extends Base
{

    public function rule()
    {
        return [
            'login' => [
                'username' => ['name' => 'username', 'require' => true]
            ]
        ];
    }

    public function login(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {
        return [
            "code" => 200,
            "msg" => "login",
            "data" => [
                'username' => $request->post['username']
            ]
        ];
    }
}