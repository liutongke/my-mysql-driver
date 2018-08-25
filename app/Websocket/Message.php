<?php
/*
 * User: keke
 * Date: 2018/7/26
 * Time: 14:34
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

namespace chat\sw\Websocket;

class Message implements Chat
{
    public function __construct()
    {
    }

    public function Handle($ws, $frame)
    {
        //查询出所有的用户
        $send_msg = json_decode($frame->data, true)['msg'];
        $all_user = DB()
            ->select('chat_fd','*');

        foreach ($all_user as $key => $value) {
            $msg = Send::msg($value['token'], '用户', 2, $send_msg, $value['fd']);
            $ws->push($frame->fd, $msg);
        }
    }
}