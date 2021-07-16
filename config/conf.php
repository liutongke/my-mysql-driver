<?php
/*
 * User: keke
 * Date: 2021/7/13
 * Time: 11:06
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
return [
    'http' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'ssl' => false,
        'reuse_port' => true,//端口复用
    ],
    'ws' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'ssl' => false,
        'reuse_port' => true,//端口复用
    ],
    'swoole_tables' => [
        'http' => [ // 表名，会加上 CoTable 后缀，比如这里是 wsTable
            'size' => 1024, //  表容量
            'column' => [ // 表字段，字段名为 value
                ['name' => 'fd', 'type' => \Swoole\Table::TYPE_INT, 'size' => 8],
//                ['name' => 'workerId', 'type' => \Swoole\Table::TYPE_INT, 'size' => 8],
                ['name' => 'data', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 2048],
            ],
        ],
        'ws' => [ // 表名，会加上 CoTable 后缀，比如这里是 wsTable
            'size' => 10240, //  表容量
            'column' => [ // 表字段，字段名为 value
                ['name' => 'fd', 'type' => \Swoole\Table::TYPE_INT, 'size' => 8],
                ['name' => 'workerId', 'type' => \Swoole\Table::TYPE_INT, 'size' => 8],
//                ['name' => 'ws', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 2048],
            ],
        ],
    ],
];