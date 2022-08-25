<?php
/*
 * User: keke
 * Date: 2022/8/26
 * Time: 0:04
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


abstract class Rule
{
    //        ['engineData' => [
//            'data' => ['name' => 'data', 'require' => true, 'type' => 'string']
//        ]
//        ];
    abstract protected function rule();

    //参数处理
    public function getByRule($data, string $action): array
    {
        $rules = $this->rule();
        if (!isset($rules[$action])) {
            return ["res" => false, "data" => ""];
        }

        $rule = $rules[$action];
        $t = ["res" => false, "data" => ""];

        foreach ($rule as $k => $v) {
            var_dump($k, $v);
            if ($v['require'] && !isset($data[$k])) {//必须滴
                $t = ["res" => true, "data" => "must require {$k}"];
            }
        }

        return $t;
    }
}