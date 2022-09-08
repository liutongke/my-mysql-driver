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
        $this->tcpServer();
        $this->udpServer();
    }

    public function addProcess()
    {
        $this->process_config = DI()->config->get('conf.process');

        if (!empty($this->process_config) && is_array($this->process_config)) {
            foreach ($this->process_config as $processData) {
                if (isset($processData['0']) && $processData['1']) {
                    $this->server->addProcess(call_user_func([new $processData['0'], $processData['1']], $this->server));
                }
            }
        }
    }

    public function mainServer()
    {
        $this->ws_config = DI()->config->get('conf.ws');

        $this->initSwooleServer($this->ws_config['host'], $this->ws_config['port']);

        if (isset($this->ws_config['settings']) && !empty($this->ws_config['settings'])) {
            $this->server->set($this->ws_config['settings']);
        }

        foreach ($this->ws_config['events'] as $eventsInfo) {
            $this->server->on($eventsInfo['0'], [new $eventsInfo['1']($this->server), $eventsInfo['2']]);
        }
    }

    public function tcpServer()
    {
        $this->tcp_config = DI()->config->get('conf.tcp');
        if (!empty($this->tcp_config)) {
            $tcp_server = $this->server->listen($this->tcp_config['host'], $this->tcp_config['port'], $this->tcp_config['sockType']);

            $tcp_server->set($this->tcp_config['settings']);

            foreach ($this->tcp_config['events'] as $eventsInfo) {
                $tcp_server->on($eventsInfo['0'], [new $eventsInfo['1']($this->server), $eventsInfo['2']]);
            }
        }
    }

    public function udpServer()
    {
        $this->udp_config = DI()->config->get('conf.udp');
        if (!empty($this->udp_config)) {
            $tcp_server = $this->server->listen($this->udp_config['host'], $this->udp_config['port'], $this->udp_config['sockType']);

            $tcp_server->set($this->udp_config['settings']);

            foreach ($this->udp_config['events'] as $eventsInfo) {
                $tcp_server->on($eventsInfo['0'], [new $eventsInfo['1']($this->server), $eventsInfo['2']]);
            }
        }
    }

    public function initialize()
    {
        DI()->config->get('http');
        DI()->config->get('websocket');
        DI()->logger = Logger::getInstance(ROOT_PATH, DI()->config->get('conf.debug'));//初始化日志
        DI()->runTm = Runtime::getInstance(DI()->config->get('conf.debug'));
        DI()->Error = Errors::getInstance();
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

    public function getServer()
    {
        return $this->server;
    }
}