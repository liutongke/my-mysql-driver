<?php

namespace App\Controller;

use Sapi\Api;
use Simps\DB\BaseRedis;

class App extends Api
{
    public function rule()
    {
        return [
//            'Index' => [
//                'pic' => ['name' => 'pic', 'require' => true]
//            ]
        ];
    }

    public function Index(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {
//        $redis = new BaseRedis();
//        $key = $this->create_uuid('keke_');
//        $res = $redis->set($key, date("Y-m-d H:i:s"));
//        $redis->expire($key, 86400);
//        var_dump($request->get);
        var_dump($request->getContent());
        return [
            "code" => 200,
            "msg" => "hello World!",
            'tm' => date('Y-m-d H:i:s'),
            "data" => [
                'name' => 'api-swoole',
                'version' => '1.0.0',
                'postData' => $request->post,
                '$request' => $request
            ],
        ];
    }

    //uuid生成方法（可以指定前缀）
    function create_uuid($prefix = "")
    {
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 8) . '-';
        $uuid .= substr($str, 8, 4) . '-';
        $uuid .= substr($str, 12, 4) . '-';
        $uuid .= substr($str, 16, 4) . '-';
        $uuid .= substr($str, 20, 12);
        return $prefix . $uuid;
    }
}