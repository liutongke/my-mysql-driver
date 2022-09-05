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

namespace chat\sw\Core;


class CoServer
{
    use Singleton;

    public $server;

    public function __construct()
    {
        $this->initialize();
        $this->ws_config = DI()->config->get('conf.ws');

        $this->initSwooleServer($this->ws_config['host'], $this->ws_config['port'], $this->ws_config['type']);

        if (isset($this->ws_config['settings']) && !empty($this->ws_config['settings'])) {
            $this->server->set($this->ws_config['settings']);
        }

        foreach ($this->ws_config['events'] as $eventsInfo) {
            $this->server->on($eventsInfo['0'], [new $eventsInfo['1']($this->server), $eventsInfo['2']]);
        }
    }

    public function initialize()
    {
        DI()->config->get('router.http');
        DI()->config->get('router.ws');
        DI()->logger = Logger::getInstance(ROOT_PATH, DI()->config->get('conf.debug'));//初始化日志
        DI()->runTm = Runtime::getInstance(DI()->config->get('conf.debug'));
        DI()->Error = Error::getInstance();
    }

    const webSocketServer = 1;

    public function initSwooleServer($host, $prot, $type)
    {
        switch ($type) {
            case self::webSocketServer:
                $this->server = new \Swoole\WebSocket\Server($host, $prot);
                break;
        }
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