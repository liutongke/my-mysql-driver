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

namespace chat\sw\Co;

//路由
class Router
{
    public $router = [];//[路由=>服务]

    public function Register(string $key, callable $val)
    {
        $this->router[$key] = $val;
    }

    public function GetHandlers(string $key, $default = NULL): callable
    {
        if (!isset($this->router)) {
            $this->router[$key] = $default;
        }
        return $this->router[$key];
    }

    public static function MsgHandle($ws, $frame)
    {
        $res = json_decode($frame->data, true);
        if (empty($res) || !is_array($res)) {
            return json_encode(['id' => -1, 'err' => 400, 'path' => '', 'data' => date("Y-m-d H:i:s")]);
        }
        return json_encode(['id' => -1, 'err' => 0, 'path' => '', 'data' => date("Y-m-d H:i:s")]);
    }
}