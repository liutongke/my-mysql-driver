<?php
return [
    'debug' => true,//调试模式
    'log' => [
        'displayConsole' => true,//true控制台打印日志
        'saveLog' => true,//保存日志
    ],
    'udp' => [
        'host' => '0.0.0.0',
        'port' => 9502,
        'sockType' => SWOOLE_SOCK_UDP,
        'events' => [
            ['packet', \App\Controller\UdpServe::class, 'onPacket'],
        ],
        'settings' => [],
    ],
    'tcp' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'sockType' => SWOOLE_SOCK_TCP,
        'events' => [
            ['receive', \App\Controller\TcpServe::class, 'onReceive'],
        ],
        'settings' => [],
    ],
    'ws' => [
        'host' => '0.0.0.0',
        'port' => 9500,
        'events' => [
//            ['open', \Sapi\Events::class, 'onOpen'],
            ['handshake', \App\Controller\ChatEvents::class, 'onHandshake'],
            ['message', \Sapi\Events::class, 'onMessage'],
            ['close', \Sapi\Events::class, 'onClose'],
            ['request', \Sapi\Events::class, 'onRequest'],
            ['Task', \Sapi\Events::class, 'onTask'],
            ['Finish', \Sapi\Events::class, 'onFinish'],
            ['workerStart', \Sapi\Events::class, 'onWorkerStart'],
            ['start', \Sapi\Events::class, 'onStart'],
        ],
        'settings' => [
            'daemonize' => false,//设置 daemonize => true 时，程序将转入后台作为守护进程运行。长时间运行的服务器端程序必须启用此项。如果不启用守护进程，当 ssh 终端退出后，程序将被终止运行
//            'dispatch_mode' => 2,//数据包分发策略。【默认值：2】
            'worker_num' => swoole_cpu_num(),
            'log_file' => 'storage/swoole',
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%d %H:%M:%S',
            'log_level' => SWOOLE_LOG_DEBUG,
            'task_worker_num' => 10,
            'enable_coroutine' => true,//是否启用异步风格服务器的协程支持
//            'buffer_output_size' => 32 * 1024 * 1024, //配置发送输出缓存区内存尺寸。【默认值：2M】
//            'document_root' => ROOT_PATH,
//            'enable_static_handler' => true,//开启静态文件请求处理功能
//            'static_handler_locations' => ['/chatroom', '/app/images'],//设置静态处理器的路径。类型为数组，默认不启用。
        ],

//        SWOOLE_LOG_DEBUG	调试日志，仅作为内核开发调试使用
//        SWOOLE_LOG_TRACE	跟踪日志，可用于跟踪系统问题，调试日志是经过精心设置的，会携带关键性信息
//        SWOOLE_LOG_INFO	普通信息，仅作为信息展示
//        SWOOLE_LOG_NOTICE	提示信息，系统可能存在某些行为，如重启、关闭
//        SWOOLE_LOG_WARNING	警告信息，系统可能存在某些问题
//        SWOOLE_LOG_ERROR	错误信息，系统发生了某些关键性的错误，需要即时解决
//        SWOOLE_LOG_NONE	相当于关闭日志信息，日志信息不会抛出
    ],
//    'process' => [
//        [\App\Controller\Process::class, 'addProcess']
//    ],//添加用户自定义的工作进程
];