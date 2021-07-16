<?php
/*
 * User: keke
 * Date: 2021/7/16
 * Time: 14:54
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


class Index
{
    public function list(\Swoole\Http\Request $request, \Swoole\Http\Response $response, $server)
    {
        $redis = new \Simps\DB\BaseRedis();
        // $server->connections 遍历所有websocket连接用户的fd，给所有用户推送
        foreach ($server->connections as $fd) {
            $redis->set('key', $fd);
//            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            var_dump($fd, $server->isEstablished($fd));
//            if ($this->server->isEstablished($fd)) {
//                $this->server->push($fd, $request->get['message']);
//            }
        }
        $redis->get('key');
        $response->end("hhhhh");
        return;
    }
}