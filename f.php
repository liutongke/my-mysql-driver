<?php

/*
 * User: keke
 * Date: 2021/7/13
 * Time: 10:36
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

function get_subjects($obj_name)
{
    if (!is_object($obj_name)) {
        return (false);
    }
    return ($obj_name->subjects);
}

$obj_name = new stdClass;
$obj_name->subjects = array('Google', 'Runoob', 'Facebook');
var_dump(get_subjects(NULL));
var_dump(get_subjects($obj_name));

class foo
{

    function __call($name, $arguments)
    {
        var_dump($name, $arguments);
    }

    public function __set($name, $value)
    {
        var_dump('__set', $name, $value);
    }

    public function __get($name)
    {
        return $this->get('__get', $name, NULL);
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        var_dump($name, $arguments);
    }
}

foo::Get("path/", "test@index");
$x = new foo();

$x->doStuff("123");
$x->test1 = "test";
$x->test1;
//$x->fancy_stuff();