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

namespace chat\sw\Core;

class CoTable
{
    protected static $instance = NULL;
    protected static $table = [];

    public function __construct()
    {
        $this->init();
    }

    public static function getInstance()
    {
        if (static::$instance == NULL) {
            static::$instance = (new self());
        }
        return static::$instance;
    }

    private function init()
    {
        $_config = DI()->config->get('conf.swoole_tables');
        foreach ($_config as $tableName => $tableInfo) {
            $tb = new \Swoole\Table($tableInfo['size']);
            foreach ($tableInfo['column'] as $column) {
                $tb->column($column['name'], $column['type'], $column['size']);
            }
            $tb->create();
            self::$table[$tableName] = $tb;
        }
//        $tb = new \Swoole\Table(1024);
//        $tb->column('fd', \Swoole\Table::TYPE_INT);
//        $tb->column('workerId', \Swoole\Table::TYPE_INT);
//        $tb->column('ws', \Swoole\Table::TYPE_STRING, 2048);
//        $tb->create();
//        self::$table = $tb;
    }

    public $_this;

    public function table(string $tableName)
    {
        $this->_this = self::$table[$tableName];
        return $this;
    }

    public function set(string $tableName, string $key, array $value): bool
    {
        return $this->_this->set($key, $value);
    }

    public function get(string $tableName, $key)
    {
        return self::$table[$tableName]->set($key);
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