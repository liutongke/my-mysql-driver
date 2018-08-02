<?php
/*
 * User: keke
 * Date: 2018/7/26
 * Time: 14:10
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
require_once __DIR__ . '/vendor/autoload.php';
//use \app\Jwt;
use \chat\sw\Server\Ws;

$webSocket = new Ws();
$webSocket->run();
$res = DB()->insert('chat_fd', ['user_id' => 1,
    'fd' => 2,
    'token' => 'token']);
//$res = DB('chat_fd')
//    ->insert([
//        'user_id' => 1,
//        'fd' => 33
//    ]);
//生成依赖
//$msgMethod = new \app\server\Open();
//注入依赖
//$pb = new \app\server\SendMsg($msgMethod);
//dd($pd);
//
//$res = DB('chat_fd')
//    ->insert([
//        'user_id' => 1,
//        'fd' => 33
//    ]);
//dd($res);
//$token = new \chat\sw\Core\Jwt('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9seXRlc3QucmlsaWFuYXBwLmNvbVwvYXBpXC9nZXRcL2xvZ2luXC9wYXNzd29yZCIsImlhdCI6MTUzMjYwODE3NSwiZXhwIjoxNTM1MjAwMTc1LCJuYmYiOjE1MzI2MDgxNzUsImp0aSI6Imxwd2Zacnh0ek4yVWN5SGsiLCJzdWIiOjExLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.oSPRdQPsQ3VZGO-YFafjPls7KxemEMkwRq9loQ1PZRw');
//$res = $token->decode();
//dd($res);
//die;

////创建websocket服务器对象，监听0.0.0.0:9502端口
//$ws = new swoole_websocket_server("0.0.0.0", 9502);
//
////监听WebSocket连接打开事件
//$ws->on('open', function ($ws, $request) {
//    //生成依赖
//    $msgMethod = new Open();
//    //注入依赖
//    $pb = new SendMsg($msgMethod);
//    $pb->send($ws, $request);
//});
//
////监听WebSocket消息事件
//$ws->on('message', function ($ws, $frame) {
////    $ws->push($frame->fd, "server: {$frame->data}");
//});
//
////监听WebSocket连接关闭事件
//$ws->on('close', function ($ws, $fd) {
////    echo "client-{$fd} is closed\n";
//});
//
//$ws->start();