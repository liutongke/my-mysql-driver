<?php
/*
 * User: keke
 * Date: 2021/7/12
 * Time: 18:12
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

namespace chat\sw\Controller;


class App
{
//    public function Index(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
//    {
//        $response->end("this is router test!");
//    }

    public function Index1(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        EchoHtml($response, "index.html");
//        $rand = rand(1111, 9999);
//        $response->end("<h1>------>Index1</h1>{$rand}");
    }

    public function stop(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $tm = date('Y-m-d H:i:s');
        $response->end("<h1>------>stop{$tm}</h1>");
    }
}