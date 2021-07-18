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
        'events' => [
            ['open', \chat\sw\Core\Events::class, 'onOpen'],
            ['message', \chat\sw\Core\Events::class, 'onMessage'],
            ['close', \chat\sw\Core\Events::class, 'onClose'],
            ['request', \chat\sw\Core\Events::class, 'onRequest'],
            ['workerStart', \chat\sw\Core\Events::class, 'onWorkerStart'],
        ],
        'settings' => [
            'daemonize' => false,//设置 daemonize => true 时，程序将转入后台作为守护进程运行。长时间运行的服务器端程序必须启用此项。如果不启用守护进程，当 ssh 终端退出后，程序将被终止运行
//            'dispatch_mode' => 2,//数据包分发策略。【默认值：2】
            'worker_num' => swoole_cpu_num(),
            'log_file' => 'storage/logs/log',
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%d %H:%M:%S',
//            'document_root' => ROOT_PATH,
//            'enable_static_handler' => true,
//            'static_handler_locations' => ['/chatroom', '/app/images'],//设置静态处理器的路径。类型为数组，默认不启用。
        ],
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
    'redis' => [
        'host' => '192.168.99.100',
        'port' => 14005,
        'auth' => '',
        'db_index' => 0,
        'time_out' => 1,
        'size' => 64,
    ],
];