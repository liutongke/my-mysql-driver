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

namespace chat\sw\Server;

class Message implements Chat
{
    public function __construct()
    {
    }

    public function Handle($ws, $frame)
    {
//        echo 'message';
//        echo $frame->data;
        //将swoole分配给用户的fd和表进行关联
        $res = DB('chat_fd')
            ->insert([
                'user_id' => 1,
                'fd' => $frame->fd
            ]);
        var_dump($res);

        $ws->push($frame->fd, "server: {$frame->data}");
    }
}