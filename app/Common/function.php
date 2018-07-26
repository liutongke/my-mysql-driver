<?php
/*
 * User: keke
 * Date: 2018/7/26
 * Time: 16:09
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
//mysql数据库
function DB($table = 'null')
{
    static $config;
    if (!$config)
        $config = $GLOBALS['config'];

    static $_db;
    if (!$_db)
        $_db = new \ninvfeng\mysql($config['mysql']);
    return $_db->table($table);
}

//Redis数据库
function Redis()
{
    static $config;
    if (!$config)
        $config = $GLOBALS['config'];

    static $redis;
    if (!$redis)
        $redis = new swoole\Redis($config['redis']);
    return $redis;
}

function dd($data)
{
    echo '<pre />';
    var_dump($data);
    echo '<pre />';
    die;
}

function dump($data)
{
    echo '<pre />';
    var_dump($data);
    echo '<pre />';
}