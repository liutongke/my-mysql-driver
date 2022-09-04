<?php
/*
 * User: keke
 * Date: 2022/9/3
 * Time: 23:07
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

namespace chat\sw\Core;


class WsResponse
{
    private $server;

    private $fd;
    private $id;
    private $msg;
    private $path;
    private $code;
    private $data;

    public function __construct(\Swoole\WebSocket\Server $server)
    {
        $this->server = $server;
    }

    public function setFd($fd)
    {
        $this->fd = $fd;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setStatus($status)
    {
//        $this->response->status($status);
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function output()
    {
        $this->server->push($this->fd, json_encode([
            'id' => $this->id ?? '-1',
            'code' => $this->code ?? '-1',
            'path' => $this->path ?? '',
            'msg' => $this->msg ?? 'success',
            'data' => $this->data ?? '',
        ]));
    }
}