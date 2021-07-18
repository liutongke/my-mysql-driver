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

use \chat\sw\Co\Websocket;
use \chat\sw\Co\CoWs;
use chat\sw\Core\CoTable;
use \chat\sw\Router\HttpRouter;

use \chat\sw\Core\Config;
use \chat\sw\Core\CoHttp;

define('DS', DIRECTORY_SEPARATOR);           //目录分隔符
define('ROOT_PATH', getcwd() . DS);               //入口文件所在的目录
//define('APP_PATH',ROOT_PATH.'Application'.DS);
//define('FRAMEWORK_PATH', ROOT_PATH.'Framework'.DS);
define('CONFIG_PATH', ROOT_PATH . 'config' . DS);
//define('CONTROLLER_PATH', APP_PATH.'Controller'.DS);
//define('MODEL_PATH', APP_PATH.'Model'.DS);
//define('VIEW_PATH', APP_PATH.'View'.DS);
//define('CORE_PATH', FRAMEWORK_PATH.'Core'.DS);
//define('LIB_PATH', FRAMEWORK_PATH.'Lib'.DS);
//var_dump(DS, ROOT_PATH, CONFIG_PATH);
//string(1) "/"
//string(14) "/var/www/html/"
//string(21) "/var/www/html/config/"
$di = DI();
$di->config = new Config("./Config");
//var_dump($di->config->get('conf.mysql', 123));
//die;
//HttpRouter::Register("index/test", function ($b) {
//    echo "test!!!";
//});
//\HttpRouter("/", "\chat\sw\Controller\App@Index1");
//\HttpRouter("/app/test", "\chat\sw\Controller\App@Index1");
//\HttpRouter("/stop", "\chat\sw\Controller\App@stop");
//\HttpRouter("/ws", "\chat\sw\Co\Events@ws");//websocket
//WsRouter("websocket", "\chat\sw\Controller\WsController@stop");
//CoTable::getInstance();
//CoTable::getInstance()->table("http")->set("http", "http1", ['fd' => 1, 'data' => "test http"]);
//CoTable::getInstance()->table("ws")->set("ws", "ws1", ['fd' => 1, 'workerId' => 1]);
//$list = CoTable::getInstance()->getAll();
//foreach ($list as $key => $table) {
//    var_dump("------------>{$key}");
//    foreach ($table as $row) {
//        var_dump($row);
//    }
//}
//(new \chat\sw\Co\App())->Index1();
//$router = new HttpRouter();
//$router->GetHandlers("index/test", [1, 2]);
//$router->GetHandlers("app/test", [1, 2]);
//$router->Register("index/test", \chat\sw\Co\App\Index);
//(new CoWs())->start();//协程风格
//(new CoHttp())->start();
//(new CoWs())->start();
(new \chat\sw\Core\CoServer())->start();
//(new Events())->run();//异步风格
//use \app\Jwt;
//use \chat\sw\Events\Events;
//
//$webSocket = new Events();
//$webSocket->run();
//$res = DB()->insert('chat_fd', ['user_id' => 1,
//    'fd' => 2,
//    'token' => 'token']);
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