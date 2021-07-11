<?php
/*
 * User: keke
 * Date: 2021/7/9
 * Time: 17:18
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

namespace sw;

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use function Swoole\Coroutine\run;

class Pool
{
    public static $chanArr = [];

    //开始进程池
    public function StartPool()
    {
        run(function () {
            for ($i = 0; $i <= 10; $i++) {
                $channel = new Channel(1);
                self::$chanArr[$i] = $channel;
                $this->StartOneWorker($i, $channel);//启动协程
            }
            Coroutine::sleep(1.0);
//            Coroutine::create(function () use ($channel) {
//                for ($i = 0; $i < 10; $i++) {
//                    Coroutine::sleep(1.0);
//                    $channel->push(['rand' => rand(1000, 9999), 'index' => $i]);
//                    echo "{$i}\n";
//                }
//            });

//            Coroutine::create(function () use ($channel) {
//                while (1) {
//                    $data = $channel->pop(2.0);
//                    if ($data) {
//                        var_dump($data);
//                    } else {
//                        assert($channel->errCode === SWOOLE_CHANNEL_TIMEOUT);
//                        break;
//                    }
//                }
//            });
        });
    }

    public function StartOneWorker($workId, $chan)
    {
        go(function () use ($workId, $chan) {
            while (true) {
                $data = $chan->pop(-1);
                if ($data) {
                    var_dump("workId:" . $workId);
                    var_dump("----------");
                    var_dump($data);
                } else {
                    assert($chan->errCode === SWOOLE_CHANNEL_TIMEOUT);
                    break;
                }
            }
        });
    }

    public function SendData()
    {
        run(function () {
            $chan = rand(0, 9);
            self::$chanArr[$chan]->push(['rand' => rand(1000, 9999), 'index' => time(), 'chan' => $chan]);
        });
    }
}