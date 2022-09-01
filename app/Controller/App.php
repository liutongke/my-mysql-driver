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
    public function Index(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $redis = \chat\sw\Ext\Redis::getInstance();
//        var_dump($redis);
//        $redis->set("tset", 1, 600);
        $key = md5(uniqid(mt_rand(1, 999999)));
        $redis->redis->set($key, $key);
//        $response->end("<h1>hello swoole!</h1>");
        DI()->logger->info("日志测试");
        return [
            "code" => 200,
            "msg" => "hello World!",
            "data" => [
                "id" => 1,
                "name" => "Reds",
                "Colors" => ["Crimson", "Red", "Ruby", "Maroon"]
            ]
        ];
    }

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