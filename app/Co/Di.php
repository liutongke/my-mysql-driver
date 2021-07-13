<?php
/*
 * User: keke
 * Date: 2021/7/13
 * Time: 10:48
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


class Di
{
    protected static $instance = NULL;
    protected $data = array();

    public function __construct()
    {
    }

    public static function one()
    {
        if (static::$instance == NULL) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get($key, $default = NULL)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }
        return $this->data[$key];
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name, NULL);
    }
}