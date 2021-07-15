<?php
/*
 * User: keke
 * Date: 2021/7/15
 * Time: 10:28
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

namespace chat\sw\Co;

class CoTable
{
    protected static $instance = NULL;
    protected static $table;

    public function __construct()
    {
        self::$table = Tb();
    }

    public static function getInstance()
    {
        if (static::$instance == NULL) {
            static::$instance = (new self());
        }
        return static::$instance;
    }

    public function set(string $key, array $value): bool
    {
        return self::$table->set($key, $value);
    }

    public function get($key)
    {
        return self::$table->set($key);
    }

    public function getAll()
    {
        return self::$table;
    }

    public function incr()
    {

    }

    public function decr()
    {

    }

    public function exist()
    {

    }

    public function del()
    {

    }

    public function count()
    {

    }
}