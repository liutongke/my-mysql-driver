<?php
/*
 * User: keke
 * Date: 2021/7/12
 * Time: 10:20
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

//路由
class HttpRouter implements Router
{
    private static $router = [];//[路由=>服务]

    public static function __callStatic($funName, $arguments)
    {
        (new self())->SetHandlers($funName, $arguments);
    }

    public function SetHandlers($name, $value)
    {
        self::$router[$value['0']] = $value['1'];
    }

    public static function GetHandlers()
    {
        $list = [];
        foreach (self::$router as $path => $call) {
            if (is_callable($call)) {//函数
                $list[$path] = $call;
            } else {
                $arr = explode('@', $call);
                $obj = new $arr['0']();
                $list[$path] = [$obj, $arr['1']];
            }
        }
        return $list;
    }
}