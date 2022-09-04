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
    'debug' => true,
    'http' => [
        'host' => '0.0.0.0',
        'port' => 9500,
        'ssl' => false,
        'reuse_port' => true,//端口复用
    ],
    'ws' => [
        'type' => 1,//启动哪种服务 1 WebSocket
        'host' => '0.0.0.0',
        'port' => 9500,
        'ssl' => false,
        'reuse_port' => true,//端口复用
        'events' => [
            ['open', \chat\sw\Core\Events::class, 'onOpen'],
            ['message', \chat\sw\Core\Events::class, 'onMessage'],
            ['close', \chat\sw\Core\Events::class, 'onClose'],
            ['request', \chat\sw\Core\Events::class, 'onRequest'],
            ['Task', \chat\sw\Core\Events::class, 'onTask'],
            ['Finish', \chat\sw\Core\Events::class, 'onFinish'],
            ['workerStart', \chat\sw\Core\Events::class, 'onWorkerStart'],
            ['start', \chat\sw\Core\Events::class, 'onStart'],
        ],
        'settings' => [
            'daemonize' => false,//设置 daemonize => true 时，程序将转入后台作为守护进程运行。长时间运行的服务器端程序必须启用此项。如果不启用守护进程，当 ssh 终端退出后，程序将被终止运行
//            'dispatch_mode' => 2,//数据包分发策略。【默认值：2】
            'worker_num' => swoole_cpu_num(),
            'log_file' => 'swoole/log',
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%d %H:%M:%S',
            'log_level' => SWOOLE_LOG_DEBUG,
            'task_worker_num' => 10,
            'enable_coroutine' => true,//是否启用异步风格服务器的协程支持
//            'buffer_output_size' => 32 * 1024 * 1024, //配置发送输出缓存区内存尺寸。【默认值：2M】
        ],

//        SWOOLE_LOG_DEBUG	调试日志，仅作为内核开发调试使用
//        SWOOLE_LOG_TRACE	跟踪日志，可用于跟踪系统问题，调试日志是经过精心设置的，会携带关键性信息
//        SWOOLE_LOG_INFO	普通信息，仅作为信息展示
//        SWOOLE_LOG_NOTICE	提示信息，系统可能存在某些行为，如重启、关闭
//        SWOOLE_LOG_WARNING	警告信息，系统可能存在某些问题
//        SWOOLE_LOG_ERROR	错误信息，系统发生了某些关键性的错误，需要即时解决
//        SWOOLE_LOG_NONE	相当于关闭日志信息，日志信息不会抛出
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
        'host' => '192.168.0.105',
        'port' => 6379,
        'auth' => '',
        'db_index' => 0,
        'time_out' => 1,
        'size' => 64,
    ],
];