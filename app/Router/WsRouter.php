<?php
/*
 * User: keke
 * Date: 2021/7/13
 * Time: 17:38
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

namespace chat\sw\Router;


class WsRouter implements Router
{
    private static $router = [];//[路由=>服务]

    public static function __callStatic($funName, $arguments)
    {
        (new self())->SetHandlers($funName, $arguments);
    }

    public function SetHandlers($name, $value)
    {
        if (is_callable($value['1'])) {//函数
            self::$router[$value['0']] = $value['1'];
        } else {
            $arr = explode('@', $value['1']);
            $obj = new $arr['0']();
            self::$router[$value['0']] = [$obj, $arr['1']];
        }
    }

    public static function GetHandlers()
    {
        return self::$router;
    }

    public static function MsgHandle($request, $response, $frame)
    {
        $res = json_decode($frame->data, true);
        $list = self::GetHandlers();
        if (!isset($list[$res['path']]) || empty($res) || !is_array($res)) {
            return json_encode(['id' => -1, 'err' => 400, 'path' => '', 'data' => date("Y-m-d H:i:s")]);
        }
        $c = $list[$res['path']];
        $c['0']->{$c['1']}($request, $response, $res);
        return json_encode(['id' => -1, 'err' => 0, 'path' => '', 'data' => date("Y-m-d H:i:s")]);
    }
}