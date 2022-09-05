<?php
/*
 * User: keke
 * Date: 2022/9/2
 * Time: 23:33
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


class Runtime
{
    use Singleton;

    private $tm;
    private $debug;

    function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

    public function start()
    {
        if ($this->debug) {
            $this->tm = microtime(true);
        }
    }

    public function end()
    {
        if ($this->debug) {
            $endTm = microtime(true);
            $runTime = ($endTm - $this->tm) * 1000;
            return round($runTime, 3) . "ms";
        }
        return "0";
    }
}