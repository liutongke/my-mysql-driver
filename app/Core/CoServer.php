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
    public $server;

    public static function welcome()
    {
        $swooleVersion = SWOOLE_VERSION;
        echo <<<EOL
        Swoole: {$swooleVersion}\n
        EOL;
    }

    public function __construct()
    {
        self::welcome();
        DI()->config->get('router.http');
        DI()->config->get('router.ws');
        $this->_config = DI()->config->get('conf.ws');
        CoTable::getInstance();
        $this->server = new \Swoole\WebSocket\Server($this->_config['host'], $this->_config['port']);
        if (isset($this->_config['settings']) && !empty($this->_config['settings'])) {
            $this->server->set($this->_config['settings']);
        }
        foreach ($this->_config['events'] as $eventsInfo) {
//            var_dump($eventsInfo['0'], $eventsInfo['1'], $eventsInfo['2']);
            $this->server->on($eventsInfo['0'], [new $eventsInfo['1']($this->server), $eventsInfo['2']]);
        }
    }

    public function start()
    {
        $this->server->start();
    }
}