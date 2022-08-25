<?php
/*
 * User: keke
 * Date: 2021/7/15
 * Time: 11:08
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

use Swoole\Process;
use Swoole\Coroutine;
use Swoole\Coroutine\Server\Connection;

class CoTcp
{
    public static $serv = [];

    public function run()
    {
        //创建Server对象，监听 127.0.0.1:9501 端口
        $server = new Swoole\Server('0.0.0.0', 9501);
        $server->set([
            'worker_num' => 8,
//            'task_worker_num' => 4,
        ]);
        $server->on('WorkerStart', function ($server, $worker_id) {

        });
//监听连接进入事件
        $server->on('Connect', function ($server, $fd) {
            var_dump("workerId:{$server->worker_id}");
            array_push(self::$serv, $fd);
            var_dump(self::$serv);
            echo "Client: Connect.\n";
        });

//监听数据接收事件
        $server->on('Receive', function ($server, $fd, $reactor_id, $data) {
            var_dump($server->worker_id);
            foreach (self::$serv as $key => $value) {
                var_dump("-------->", $value);
                $server->send($value, "Server to workerId {$server->worker_id}: {$data}");
            }
        });

//监听连接关闭事件
        $server->on('Close', function ($server, $fd) {
            echo "Client: Close.\n";
        });

//启动服务器
        $server->start();
    }
}

(new CoTcp())->run();