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

use swoole\src\Jwt;

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
        //查询用户有没有登陆，如果没登陆不让其发言
//        $res = $this->redis()->hGet('user', $request->fd);
//        if (!$res) {
//        $arr = $this->msg('true', '0', '0', '系统提示信息：新用户请注册后发言');

//        $jwt = new Jwt();
        $arr = [
            'qwertyu',
            '000000000000',
            '1234567890-'
        ];
        var_dump($arr);
//        $arr = $this->msg('flase', $frame->fd, $name, $msg);
        $ws->push($request->fd, Jwt::response($arr));
    }

    //消息事件
    public function message($ws, $frame)
    {
        //发送过来的信息
        $sms = $frame->data;
        $sms = json_decode($sms, TRUE);
        $ws->push($frame->fd, $sms);

    }

    //断开事件
    public function close($ws, $fd)
    {
        //连接数据库
        $rows = $this->redis()->hGetAll('user');
        $name = $this->redis()->hGet('user', $fd);
        $msg = '用户' . $name . '退出了聊天室！';
        $arr = $this->msg('true', '0', '0', $msg);
        foreach ($rows as $key => $value) {
            $ws->push($key, $arr);
        }
        //用户推出则删除用户的hash结构
        $this->redis()->hDel('user', $fd);
        echo "client-{$fd} is closed\n";
    }


    //对发送的方法进行封装
    public function msg($state, $id = 0, $username = 0, $msg)
    {
        $arr = [
            'state' => $state,
            'id' => $id,
            'username' => $username,
            'msg' => $msg
        ];
        $arr = json_encode($arr);
        return $arr;
    }

    //对redis连接的封装
    public function redis()
    {
        //连接数据库
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
//        $redis->auth('your_password');
        return $redis;
    }
}