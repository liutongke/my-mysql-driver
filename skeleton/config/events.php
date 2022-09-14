<?php
return [
    'workerStart' => [
        [\App\Ext\Pool::class, 'startPool'],//启动连接池
//        [\App\Controller\EventsDemo::class, 'workerStart'],
    ],
    'open' => [
        [\App\Controller\EventsDemo::class, 'open'],
    ],
    'close' => [
        [\App\Controller\EventsDemo::class, 'close'],
    ],
    'task' => [
        [\App\Controller\EventsDemo::class, 'task'],
    ],
    'finish' => [
        [\App\Controller\EventsDemo::class, 'finish'],
    ],
];//定制特定事件