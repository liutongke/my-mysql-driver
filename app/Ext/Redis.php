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

namespace App\Ext;

class Redis
{
    private static $_instance = NULL;
    public $redis = "";

    //对redis连接的封装
    public function __construct()
    {
        $config = DI()->config->get('conf.redis');
        //连接数据库
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
        //授权
        $config['auth'] == '' ?: $this->redis->auth($config['auth']);
        $this->redis->select($config['db_index']);
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}