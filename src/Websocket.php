<?php
/*
 * User: keke
 * Date: 2018/4/10
 * Time: 13:49
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

namespace swoole\src;

class Websocket
{
    //服务对象
    private $ws;

    //new的时候自动加载
    public function __construct()
    {
        $this->init();
    }

    //初始程序
    public function init()
    {
        $this->ws = new \swoole_websocket_server("0.0.0.0", 9502);
        $this->ws->on('open', [$this, 'open']);
        $this->ws->on('message', [$this, 'message']);
        $this->ws->on('close', [$this, 'close']);
    }

    //启动程序
    public function run()
    {
        $this->ws->start();
    }

    //连接事件
    public function open($ws, $request)
    {

        $arr = [
            'fd' => $request->fd,
            'status' => (int)1,
            'msg' => '你好啊！'
        ];
        $this->redis()->hSet('user', $request->fd, time());
        $ws->push($request->fd, Jwt::response($arr));
    }

    //消息事件
    public function message($ws, $frame)
    {
        //发送过来的信息
        $arr = [
            'status' => (int)1,
            'msg' => $frame->data
        ];

        $user_all_fd = $this->redis()->hGetAll('user');
        foreach ($user_all_fd as $key => $value) {
            if ($key != $frame->fd) {
                $ws->push($key, Jwt::response($arr));
            }
        }


    }

    //断开事件
    public function close($ws, $fd)
    {
        //连接数据库
        $arr = [
            'status' => (int)1,
            'msg' => '用户' . $fd . '退出了聊天室！'
        ];

        $user_all_fd = $this->redis()->hGetAll('user');

        foreach ($user_all_fd as $key => $value) {
            if ($key != $fd) {
                $ws->push($key, Jwt::response($arr));
            }
        }
        //用户推出则删除用户的hash结构
        $this->redis()->hDel('user', $fd);
        echo "client-{$fd} is closed\n";
    }

    //对redis连接的封装
    public function redis()
    {
        //连接数据库
        $redis = new \Redis();
        $redis->connect('***', 6379);
        return $redis;
    }
}