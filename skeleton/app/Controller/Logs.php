<?php

namespace App\Controller;

use Sapi\Api;

class Logs
{

    public function index(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        DI()->logger->debug("日志测试debug");
        DI()->logger->info("日志测试info");
        DI()->logger->notice("日志测试notice");
        DI()->logger->waring("日志测试waring");
        DI()->logger->error("日志测试error");
        return [
            'code' => 200,
            'data' => [
                'info' => 'log',
                'conf' => DI()->config->get('conf.tcp'),
            ],
        ];
    }
}