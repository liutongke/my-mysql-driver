<?php
/*
 * User: keke
 * Date: 2021/7/16
 * Time: 11:29
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

class CoServer
{
    use Singleton;

    public $server;

    public function __construct()
    {
        $this->initialize();
        $this->mainServer();
        $this->addProcess();
        $a = ["udp", "tcp"];
        foreach ($a as $key => $value) {
            $this->streamServers($key, $value);
        }
    }

    public function streamServers($k, $v)
    {
        $stream_config = DI()->config->get("conf.{$v}");
        if (!empty($stream_config)) {
            $tcp_server = $this->server->listen($stream_config['host'], $stream_config['port'], $stream_config['sockType']);

            $tcp_server->set($stream_config['settings']);

            foreach ($stream_config['events'] as $eventsInfo) {
                $tcp_server->on($eventsInfo['0'], [new $eventsInfo['1']($this->server), $eventsInfo['2']]);
            }
        }
    }

    public function addProcess()
    {
        $process_config = DI()->config->get('conf.process');

        if (!empty($process_config) && is_array($process_config)) {
            foreach ($process_config as $processData) {
                if (isset($processData['0']) && $processData['1']) {
                    $this->server->addProcess(call_user_func([new $processData['0'], $processData['1']], $this->server));
                }
            }
        }
    }

    public function mainServer()
    {
        $ws_config = DI()->config->get('conf.ws');

        $this->initSwooleServer($ws_config['host'], $ws_config['port']);

        if (isset($ws_config['settings']) && !empty($ws_config['settings'])) {
            $this->server->set($ws_config['settings']);
        }

        foreach ($ws_config['events'] as $eventsInfo) {
            $this->server->on($eventsInfo['0'], [new $eventsInfo['1']($this->server), $eventsInfo['2']]);
        }
    }

    public function initialize()
    {
        DI()->config->get('http');
        DI()->config->get('websocket');
        DI()->logger = Logger::getInstance(ROOT_PATH, DI()->config->get('conf.debug'));//初始化日志
        DI()->runTm = Runtime::getInstance(DI()->config->get('conf.debug'));
        DI()->Error = ApiError::getInstance();
    }

    const webSocketServer = 1;

    public function initSwooleServer($host, $prot)
    {
        //https://wiki.swoole.com/#/runtime
        \Swoole\Coroutine::set(['hook_flags' => SWOOLE_HOOK_TCP]);
        $this->server = new \Swoole\WebSocket\Server($host, $prot);
    }

    public function start()
    {
        Events::setProcessName("swoole server Master");
        $this->server->start();
    }
}