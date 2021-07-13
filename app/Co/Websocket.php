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
    public function ws(\Swoole\Http\Request $request, \Swoole\Http\Response $response, $server)
    {
        var_dump("getWorkId----->", $server);
        $response->upgrade();
        global $wsObjects;
        $objectId = spl_object_id($response);
        $wsObjects[$objectId] = $response;
        var_dump($wsObjects);
        while (true) {
            $frame = $response->recv();
            if ($frame === '') {
                unset($wsObjects[$objectId]);
                $response->close();
                break;
            } else if ($frame === false) {
                echo 'errorCode: ' . swoole_last_error() . "\n";
                $response->close();
                break;
            } else {
                if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
                    unset($wsObjects[$objectId]);
                    $response->close();
                    break;
                }
                $response->push(WsRouter::MsgHandle($request, $response, $frame));
            }
        }
    }
}