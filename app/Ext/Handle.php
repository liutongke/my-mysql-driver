<?php
/*
 * User: keke
 * Date: 2018/8/2
 * Time: 16:23
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

namespace chat\sw\Ext;

use chat\sw\Ext\Jwt\Jwt;

//集中处理swoole的open、message、close事件
class Handle
{
    //open事件
    public static function Open($request)
    {
        //生成token
        $token = @Jwt::fromUser($request->fd);

        //将fd存入数据中
        DB()->insert('chat_fd', ['user_id' => $request->fd,
            'fd' => $request->fd,
            'token' => $token]);

        return Send::msg($token, '系统消息', 2, '欢迎光临' . $request->fd, $request->fd);//$username = 0, $state, $msg, $id = 0

    }

    //close时间
    public static function Close($fd)
    {
        //修改，改用删除吧
        DB()->update('chat_fd', [
            'status' => 0
        ], [
            'fd' => $fd
        ]);
    }
}