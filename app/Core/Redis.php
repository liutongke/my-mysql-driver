<?php

/*
 * User: keke
 * Date: 2018/7/26
 * Time: 17:02
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

class Redis
{
    //对redis连接的封装
    public function __construct($config)
    {
        //连接数据库
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
        //授权
//        $this->redis->auth($config['pass'] == '' ? '' : $config['pass']);
        $config['pass'] == '' ?: $this->redis->auth($config['pass']);
    }

    //获取值
//    public function get($key)
//    {
//        return $this->redis->get($key);
//    }
}