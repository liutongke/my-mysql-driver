<?php
/*
 * User: keke
 * Date: 2022/9/3
 * Time: 13:55
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

namespace Sapi;


class HttpResponse
{
    private $response;
    private array $data = [];
    private int $code;
    private string $msg;
    private $debug;

    public function __construct(\Swoole\Http\Response $response)
    {
        $this->response = $response;
        $this->header();
    }

    private function header()
    {
        $this->response->header('content-type', 'application/json', true);
        return $this;
    }

    public function setStatus($ret)
    {
        $this->response->status($ret);
        return $this;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setDebug(int $errno, string $errstr, string $errfile, int $line)
    {
        $debug = [
            'err_code' => $errno,
            'msg' => $errstr,
            'file' => $errfile,
            'line' => $line,
        ];
        $this->debug = $debug;
        return $this;
    }

    public function output()
    {
        $res = [
            'code' => $this->code,
            'msg' => $this->msg ?? 'success',
            'data' => $this->data,
        ];

        if (DI()->config->get('conf.debug')) {
            $res['debug'] = $this->debug;
        }

        $this->response->end(json_encode($res));
    }
}