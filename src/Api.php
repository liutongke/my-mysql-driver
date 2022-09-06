<?php
/*
 * User: keke
 * Date: 2022/9/6
 * Time: 13:31
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


class Api
{
    public function __construct()
    {
//        $this->userCheck();//用户检验
    }

    //定义规则
    protected function rule()
    {
        return [];
    }

    //定义规则,用户自定义规则
    protected function userCheck()
    {
    }

    public function getRules($data, string $action): array
    {
        $check = $this->userCheck();

        if (!empty($check)) {
            return ["res" => true, "data" => $check];
        }

        return Rule::getInstance()->getByRule($data, $action, $this->rule());
    }
}