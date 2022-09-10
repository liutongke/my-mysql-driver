<?php
/*
 * User: keke
 * Date: 2022/9/10
 * Time: 12:55
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

class EventsRegister
{
    use Singleton;

    private array $container = [];

    function __construct()
    {
        $this->register();
    }

    private function register()
    {
        $events = DI()->config->get('events');

        if (is_array($events) && !empty($events)) {
            foreach ($events as $workName => $eventItem) {
                $this->add($workName, $eventItem);
            }
        }
    }

    public function run(...$args)
    {
        $workName = $args['0'];
        $events = $this->get($workName);
        foreach ($events as $item) {
            call_user_func_array([new $item['0'], $item['1']], $args);
        }
    }

    private function add($key, $item): EventsRegister
    {
        $this->container[$key] = $item;
        return $this;
    }

    private function get(string $key): ?array
    {
        return $this->container[$key] ?? [];
    }

    public function all(): array
    {
        return $this->container;
    }
}