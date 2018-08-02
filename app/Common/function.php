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
//function DB($table = 'null')
//{
//    static $config;
//    if (!$config)
//        $config = $GLOBALS['config'];
//
//    static $_db;
//    if (!$_db)
//        $_db = new \ninvfeng\mysql($config['mysql']);
//    return $_db->table($table);
//}

function DB()
{
    return $database = new \Medoo\Medoo([
        // required
        'database_type' => 'mysql',
        'database_name' => $GLOBALS['config']['mysql']['database_name'],
        'server' => $GLOBALS['config']['mysql']['host'],
        'username' => $GLOBALS['config']['mysql']['username'],
        'password' => $GLOBALS['config']['mysql']['password'],

        // [optional]
        'charset' => 'utf8',
        'port' => $GLOBALS['config']['mysql']['port'],

        // [optional] Table prefix
        'prefix' => $GLOBALS['config']['mysql']['prefix'],

        // [optional] Enable logging (Logging is disabled by default for better performance)
        'logging' => true,

        // [optional] MySQL socket (shouldn't be used with server and port)
//        'socket' => '/tmp/mysql.sock',

        // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],

        // [optional] Medoo will execute those commands after connected to the database for initialization
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ]);
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