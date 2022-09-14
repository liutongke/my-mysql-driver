<?php
return [
    'redis' => [
        'host' => '192.168.0.105',//Redis服务器地址
        'port' => 6379,//指定 Redis 监听端口
        'auth' => '',//登录密码
        'db_index' => 2,//指定数据库
        'time_out' => 1,//
        'size' => 64,//连接池数量
    ],
    'mysql' => [
        'host' => '192.168.0.105',
        'port' => 3305,
        'database' => 'demo',
        'username' => 'root',
        'password' => 'xCl5QUb9ES2YfkvX',
        'charset' => 'utf8',
        'unixSocket' => null,
        'options' => [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
        'size' => 64 // 连接池size
    ],
];