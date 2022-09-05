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

namespace App\Controller;


use App\Core\Rule;

class App extends Rule
{
    public function rule()
    {
        return [
            'Index' => [
//                'pic' => ['name' => 'pic', 'require' => true]
            ]
        ];
    }

    public function Index(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {
//        $redis = \App\Ext\Redis::getInstance();
//        $data = [];
//        $data["test"];
//        new data();
//        var_dump($redis);
//        $redis->set("tset", 1, 600);
//        $key = md5(uniqid(mt_rand(1, 999999)));
//        $redis->redis->set($key, $key);
//        $response->end("<h1>hello swoole!</h1>");
//        DI()->logger->info("日志测试{$key}");

        return [
            "code" => 200,
            "msg" => "hello World!",
            "data" => [
                "id" => 1,
//                "getUrl" => $request->get['keke'],
                "name" => "Reds",
                "Colors" => ["Crimson", "Red", "Ruby", "Maroon"]
            ]
        ];
    }

    public function post(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {
        $tm = date('Y-m-d H:i:s');
        return $request->post;
    }
}