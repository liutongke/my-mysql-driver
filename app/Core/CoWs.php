<?php
/*
 * User: keke
 * Date: 2021/7/16
 * Time: 11:10
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

namespace chat\sw\Core;


class CoWs
{
    private $pool;//进程池
    private $_config;
    private static $worker = ["WorkerStart"];

    public function start()
    {
        DI()->config->get('router.http');
        DI()->config->get('router.ws');
        $this->_config = DI()->config->get('conf.http');
        CoTable::getInstance();
        $server = new \Swoole\WebSocket\Server($this->_config['host'], $this->_config['port']);
        $server->on('open', function (\Swoole\WebSocket\Server $server, $request) {
            echo "server: handshake success with fd{$request->fd}\n";
        });
        $server->on('message', function (\Swoole\WebSocket\Server $server, $frame) {
            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            $server->push($frame->fd, "this is server");
        });

        $server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });
        $server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
            global $server;//调用外部的server
            // $server->connections 遍历所有websocket连接用户的fd，给所有用户推送
            foreach ($server->connections as $fd) {
                // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                if ($server->isEstablished($fd)) {
                    $server->push($fd, $request->get['message']);
                }
            }
        });
        $server->start();
    }
}