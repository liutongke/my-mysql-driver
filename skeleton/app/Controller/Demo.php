<?php

namespace App\Controller;

use Sapi\Api;

class Demo extends Api
{
    public function rule()
    {
        return [
            'Index' => [
                'pic' => ['name' => 'pic', 'require' => true]
            ]
        ];
    }

    public function demo(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {
        return [
            "code" => 200,
            "msg" => "hello World!",
            "data" => demo
        ];
    }
}