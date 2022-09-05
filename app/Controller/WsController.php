<?php
/*
 * User: keke
 * Date: 2021/7/14
 * Time: 17:01
 *——————————————————佛祖保佑 ——————————————————
 *                   _ooOoo_
 *                  o8888888o
 *                  88" . "88
 *                  (| -_- |)
 *                  O\  =  /O
 *               ____/`---'\____
 *             .'  \|     |//  `.
 *            /  \|||  :  |||//  \
 *           /  _||||| -:- |||||-  \
 *           |   | \\  -  /// |   |
 *           | \_|  ''\---/''  |   |
 *           \  .-\__  `-`  ___/-. /
 *         ___`. .'  /--.--\  `. . __
 *      ."" '<  `.___\_<|>_/___.'  >'"".
 *     | | :  ` - `.;`\ _ /`;.`/ - ` : | |
 *     \  \ `-.   \_ __\ /__ _/   .-` /  /
 *======`-.____`-.___\_____/___.-`____.-'======
 *                   `=---='
 *——————————————————代码永无BUG —————————————————
 */

namespace App\Controller;


use App\Core\Rule;

class WsController extends Rule
{
    public function rule()
    {
        return [
            'stop' => [
                'data' => ['name' => 'data', 'require' => true, 'type' => 'string']
            ]
        ];
    }

    public function index(\Swoole\WebSocket\Server $server, array $msg): array
    {
        return ['err' => 200, 'data' => 'hello apiSwoole'];
    }

    public function stop(\Swoole\WebSocket\Server $server, array $msg): array
    {
//        $msg["keke"];
//        new data();
        $redis = \App\Ext\Redis::getInstance();//        var_dump($redis);
        $redis->redis->set(rand(10000, 99999), json_encode(\Swoole\Coroutine::stats()), 60);//此处产生协程调度，cpu切到下一个协程(下一个请求)，不会阻塞进程
        return ["code" => 0, "msg" => "123123"];
    }
}