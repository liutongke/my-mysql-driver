<?php
/*
 * User: keke
 * Date: 2021/7/15
 * Time: 15:19
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
$server = new Swoole\WebSocket\Server("0.0.0.0", 9501);

$server->set(['worker_num' => 9]);
$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
    foreach ($server->connections as $fd) {
        var_dump("fd===>{$fd}");
    }
    var_dump("workerId:{$server->worker_id}");
    $GLOBALS['fd'][$request->fd]['id'] = $request->fd;//设置用户id
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//    $server->push($frame->fd, "this is server");
    foreach ($server->connections as $fd) {
        $server->push($fd, "test in");
    }
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();
