<?php
/*
 * User: keke
 * Date: 2021/7/12
 * Time: 10:28
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

namespace chat\sw\Co;

use chat\sw\Router\WsRouter;
use Swoole\Coroutine\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\CloseFrame;

//websocket
class Websocket
{
    public function ws(\Swoole\Http\Request $request, \Swoole\Http\Response $response, \Swoole\Coroutine\Http\Server $server, $workerId, \Swoole\Process\Pool $pool)
    {
        var_dump("getWorkId----->", $server->worker_id);
        $response->upgrade();
//        $t = new \stdClass();
//        $t->ojb = $response;
//        serialize($t);
//        global $wsObjects;
//        $objectId = spl_object_id($response);
//        $wsObjects[$objectId] = $response;
        CoWs::$rps[$workerId] = $response;
//        CoTable::getInstance()->set($workerId, ['fd' => $response->fd, 'workerId' => $workerId, 'ws' => serialize($server)]);
//        array_push(CoWs::$rps, $response);
//        array_push(CoWs::$fds, $response->fd);
//        var_dump($wsObjects);
        var_dump("连接的worker进行是{$workerId}");
        while (true) {
            $frame = $response->recv();
            if ($frame === '') {
//                unset($wsObjects[$objectId]);
                $response->close();
                break;
            } else if ($frame === false) {
                echo 'errorCode: ' . swoole_last_error() . "\n";
                $response->close();
                break;
            } else {
                if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
//                    unset($wsObjects[$objectId]);
                    $response->close();
                    break;
                }
                $list = CoTable::getInstance()->getAll();
                var_dump("-------------->workerId:{$workerId}===>", CoWs::$rps);
//                foreach ($list as $value) {
//                    var_dump("==========>", $value);
//                    $obj = unserialize($value['ws']);
//                $server->push($response->fd, "来了老弟rps发送的workerId:{$workerId}");
//                    $value->send("来了老弟send发送的workerId:{$workerId}");
//                }
//                foreach (CoWs::$fds as $fd) {
//                    $server->push($fd, "来了老弟fds发送的workerId:{$workerId}");
//                }
//                $response->push(WsRouter::MsgHandle($request, $response, $frame));
            }
        }
    }
}