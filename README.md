# **(一).开始**
### **1.1下载与安装**

需要确保运行环境达到了以下的要求：
*   PHP >= 7.4
*   Swoole PHP 扩展 >= 4.5
*   Redis PHP 扩展 （如需使用 Redis 客户端）
*   PDO PHP 扩展 （如需使用 MySQL 客户端）

### **1.2通过 Composer 创建项目**

```
composer create-project api-swoole/skeleton
```
### **1.3启动**
支持 HTTP 服务、WebSocket 服务、tcp服务,项目根目录`./apiswoole.php`执行命令。

```
php apiswoole.php
```

# **(二).Hello World**

### **2.1编写一个接口**


在api-swoole框架中，业务主要代码在app目录中。里面各个命名空间对应一个子目录，项目的默认命名空间是App，创建项目后app目录中包含Common、Controller、Ext三个子目录，Common目录存放函数的functions.php文件，Ext一般放置工具等。目录结构如下：


```
./
└── app
    ├── Controller # 放置接口源代码，相当于控制器层
    ├── Common # 公共代码目录，
    └── Ext# 放置工具等
```
当项目需要新增接口时，先在`./app/Controller`目录中新建`hello.php`文件，并用编辑器编辑代码。


```
<?php

namespace App\Controller;

use Sapi\Api;

class Hello extends Api
{
    public function index()
    {
        return [
            'code' => 200,
            'data' => 'hello world'
        ];
    }
}
```

### **2.2定义路由**
在`route/http.php`路由文件中定义项目路由，第一个参数为定义的浏览器访问地址，第二个参数`@`前半部分为文件的完整命名空间，`@`后半部分为在类中调用的具体方法。


```
return [
    \HttpRouter("/hello", "App\Controller\Hello@index"),
];
```

### **2.3启动项目**


在项目根目录下执行如下命令以非守护模式启动。

```
php apiswoole.php
```
默认情况下监听本地的HTTP和websocket的9501端口，在cmd中出现如下输出信息表示项目启动成功。

```
[Success] Swoole: 4.5.9, PHP: 7.4.13, Port: 9501
[Success] Swoole Http Server running：http://0.0.0.0:9501
[Success] Swoole websocket Server running：ws://0.0.0.0:9501
[Success] Swoole tcp Server running：0.0.0.0:9500
[Success] Swoole udp Server running：0.0.0.0:9502
```

### **2.4访问接口**

在浏览器地址栏中输入定义的路由地址，地址组成`http://ip网址:端口/定义的路由`。

`http://127.0.0.1:9501/hello`

请求成功返回数据默认json方式返回，包含默认的code、msg、data字段，data中数据为方法中返回的数据。
```json
{
    "code": 200,
    "msg": "success",
    "data": {
        "code": 200,
        "data": "hello world"
    }
}
```


# **(三).反向代理（Nginx + Swoole 配置）**
由于 `Http\Server` 对 `HTTP` 协议的支持并不完整，建议仅作为应用服务器，用于处理动态请求，并且在前端增加 `Nginx` 作为代理。（[参考地址](https://wiki.swoole.com/#/http_server)）

```
server {
    listen 80;
    server_name swoole.test;

    location / {
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

        proxy_pass http://127.0.0.1:9501;
    }
}

```
可以通过读取 `$request->header['x-real-ip']` 来获取客户端的真实 `IP`

# **(四).配置**
### **4.1配置文件**

默认config文件夹下包含conf.php、db.php、events.php三个文件，conf.php放置项目的配置信息，http、tcp、udp、websocket等配置。db.php负责配置MySQL、Redis连接，events.php负责定义定制特定事件注册监听，现支持`workerStart、open、close、task、finish`事件注册 。可以通过`DI()->config->get('conf.ws')`或者`\Sapi\Di::one()->config->get('conf.ws')`方式读取配置信息。
```
./config/
├── conf.php #http、tcp、udp、websocket等配置
├── db.php #数据库配置
└── events.php #定制特定事件注册监听
```

### **4.2参数说明**


```
<?php
return [
    'debug' => true,//true 调试模式 false 关闭调试
    'log' => [
        'displayConsole' => true,//控制台打印日志，true打开 false 关闭
        'saveLog' => true,//保存日志 true 打开 false 关闭
    ],
    'udp' => [
        'host' => '0.0.0.0',//指定监听的ip地址
        'port' => 9502,//指定监听端口
        'sockType' => SWOOLE_SOCK_UDP,//指定这组 Server 的类型
        'events' => [
            ['packet', \App\Controller\UdpServe::class, 'onPacket'],//回调事件
        ],
        'settings' => [],//配置选项https://wiki.swoole.com/#/server/setting
    ],
    'tcp' => [
        'host' => '0.0.0.0',//指定监听的ip地址
        'port' => 9501,//指定监听端口
        'sockType' => SWOOLE_SOCK_TCP,//指定这组 Server 的类型
        'events' => [
            ['receive', \App\Controller\TcpServe::class, 'onReceive'],//回调事件
        ],
        'settings' => [],//配置选项https://wiki.swoole.com/#/server/setting
    ],
    'ws' => [
        'host' => '0.0.0.0',//指定监听的ip地址
        'port' => 9500,//指定监听端口
        'sockType' => SWOOLE_SOCK_TCP,//指定这组 Server 的类型
        'events' => [
            ['open', \Sapi\Events::class, 'onOpen'],
            ['message', \Sapi\Events::class, 'onMessage'],
            ['close', \Sapi\Events::class, 'onClose'],
            ['request', \Sapi\Events::class, 'onRequest'],
            ['Task', \Sapi\Events::class, 'onTask'],
            ['Finish', \Sapi\Events::class, 'onFinish'],
            ['workerStart', \Sapi\Events::class, 'onWorkerStart'],
            ['start', \Sapi\Events::class, 'onStart'],
        ],//回调事件
        'settings' => [
            'daemonize' => false,//设置 daemonize => true 时，程序将转入后台作为守护进程运行。长时间运行的服务器端程序必须启用此项。如果不启用守护进程，当 ssh 终端退出后，程序将被终止运行
            'worker_num' => swoole_cpu_num(),
            'log_file' => 'storage/swoole',
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%d %H:%M:%S',
            'log_level' => SWOOLE_LOG_DEBUG,
            'task_worker_num' => 10,
            'enable_coroutine' => true,//是否启用异步风格服务器的协程支持
        ],
    ],//配置选项https://wiki.swoole.com/#/websocket_server?id=%e9%80%89%e9%a1%b9
    'process' => [
        [\App\Controller\Process::class, 'addProcess']
    ],//添加用户自定义的工作进程 https://wiki.swoole.com/#/server/methods?id=addprocess
];
```


### **4.3配置读取示例**
项目启动时在`apiswoole.php`已经默认注册服务。

```
$di = DI();
$di->config = new Config("./config");
```
例如配置信息为conf.php,结构为：

```
return [
    'debug' => true,//调试模式
    'tcp' => [
        'host' => '0.0.0.0',
        'port' => 9500,
        'sockType' => SWOOLE_SOCK_TCP,
        'events' => [
            ['receive', \App\Controller\TcpServe::class, 'onReceive'],
        ],
        'settings' => [],
    ],
]
```
可以使用`DI()->config->get('conf.tcp')`读取配置文件


```
DI()->config->get('conf.tcp') #返回数组
```
返回数组数据结构：

```json
{
    "host": "0.0.0.0",
    "port": 9500,
    "sockType": 1,
    "events": [
        [
            "receive",
            "App\\Controller\\TcpServe",
            "onReceive"
        ]
    ],
    "settings": []
}
```

# **(五).日志**

根据PSR规范中详尽定义了日志接口，日志记录在`./storage/log`目录中，按照每日生成对应`日期.log`日志文件。目前支持以下几种日志记录：

*   **error**： 系统异常类日记
*   **info**： 业务纪录类日记
*   **debug**： 开发调试类日记
*   **notice**： 系统提示类日记
*   **waring**： 系统致命类日记

日志系统使用：

```
DI()->logger->debug("日志测试debug");  #开发调试类日记
DI()->logger->info("日志测试info");    #业务纪录类日记
DI()->logger->notice("日志测试notice");#系统提示类日记
DI()->logger->waring("日志测试waring");#系统致命类日记
DI()->logger->error("日志测试error");  #系统异常类日记
```
`./storage/log/20220906.log`目录下对应日志：

```
[swoole] | [2022-09-06 01:32:05] | debug |  日志测试debug
[swoole] | [2022-09-06 01:32:05] | info |  日志测试info
[swoole] | [2022-09-06 01:32:05] | notice |  日志测试notice
[swoole] | [2022-09-06 01:32:05] | warning |  日志测试waring
[swoole] | [2022-09-06 01:32:05] | error |  日志测试error
```


# **(六).HTTP/websocket服务器[(参考地址)](https://wiki.swoole.com/#/websocket_server)**


`WebSocket\Server` 继承自 [Http\Server](https://wiki.swoole.com/#/http_server)，所以 `Http\Server` 提供的所有 `API` 和配置项都可以使用。请参考 [Http\Server](https://wiki.swoole.com/#/http_server) 章节。
*   设置了 [onRequest](https://wiki.swoole.com/#/http_server?id=on) 回调，`WebSocket\Server` 也可以同时作为 `HTTP` 服务器
*   未设置 [onRequest](https://wiki.swoole.com/#/http_server?id=on) 回调，`WebSocket\Server` 收到 `HTTP` 请求后会返回 `HTTP 400` 错误页面

如若只想实现websocket服务器，则在`./config/conf.php`配置中删除`['request', \Sapi\Events::class, 'onRequest']`即可。

### **6.1HTTP/websocket服务器配置**

HTTP/websocket服务器配置选项在`./config/conf.php`中。具体配置信息可以参考Swoole文档[配置选项](https://wiki.swoole.com/#/server/setting)。

```
<?php
return [
    'ws' => [
        'host' => '0.0.0.0',//监听地址
        'port' => 9501,//监听端口
        'events' => [
            ['open', \Sapi\Events::class, 'onOpen'],
            ['message', \Sapi\Events::class, 'onMessage'],
            ['close', \Sapi\Events::class, 'onClose'],
            ['request', \Sapi\Events::class, 'onRequest'],//HTTP服务器回调
            ['Task', \Sapi\Events::class, 'onTask'],
            ['Finish', \Sapi\Events::class, 'onFinish'],
            ['workerStart', \Sapi\Events::class, 'onWorkerStart'],
            ['start', \Sapi\Events::class, 'onStart'],
        ],//回调函数
        'settings' => [
            'daemonize' => false,//设置 daemonize => true 时，程序将转入后台作为守护进程运行。长时间运行的服务器端程序必须启用此项。如果不启用守护进程，当 ssh 终端退出后，程序将被终止运行
            'worker_num' => swoole_cpu_num(),
            'log_file' => 'storage/swoole',
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%d %H:%M:%S',
            'log_level' => SWOOLE_LOG_DEBUG,
            'task_worker_num' => 10,
        ],
    ],
];
```

### **6.2HTTP路由**

HTTP 服务器的路由构建文件位于`./route/http.php`中。声明具体的路由规则，第一个参数为定义的浏览器访问地址，第二个参数`@`前半部分为文件的完整命名空间，`@`后半部分为在类中调用的具体方法。

```
return [
    \HttpRouter("/", "App\Controller\App@Index"),
    \HttpRouter("/hello", "App\Controller\Hello@index"),
    \HttpRouter("声明浏览器地址", "命名空间+类@类中的方法"),
];
```
完整的URL地址组成`http://ip网址:端口/定义的路由`。


构建基本路由只需要一个 URI 与一个 `闭包`，提供了一个非常简单定义路由的方法：


```
return [
    \HttpRouter("/start", function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
        return 'hello';
    }),
];
```


### **6.3HTTP控制器**

对应的控制器方法默认有两个参数 `$request` 和 `$reponse`，关于 Request 和 Response 对象完整的介绍请查看 Swoole 文档：[Http\Request](https://wiki.swoole.com/#/http_server?id=httprequest) 、 [Http\Response](https://wiki.swoole.com/#/http_server?id=httpresponse)

```
<?php

namespace App\Controller;

use Sapi\Api;

class Hello extends Api
{
    public function index()
    {
        return [
            'code' => 200,
            'data' => 'hello world'
        ];
    }
}
```


### **6.4接口参数规则**

接口参数规则通过继承`Api`类实现，具体的定义规则通过`rule()`方法。
*   一维下标是接口类的方法名。
*   二维下标是接口参数名称。
*   三维下标`name`对应客户端传入的值，下标`require`表示值传入可选项,`true`必须传入，`false`非必须传入。


```
<?php

namespace App\Controller;

use Sapi\Api;

class Auth extends Api
{

    public function rule()
    {
        return [
            'login' => [
                'username' => ['name' => 'username', 'require' => true]
            ]
        ];
    }

    public function login(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {

        return [
            "code" => 200,
            "msg" => "login",
            "data" => [
                'username' => $request->post['username']
            ]
        ];
    }
}
```




### **6.5websocket路由**

websocket服务器的路由定义文件位于`./route/websocket.php`中。声明具体的路由规则，第一个参数为定义的浏览器访问地址，第二个参数`@`前半部分为文件的完整命名空间，`@`后半部分为在类中调用的具体方法。

```
return [
    WsRouter("/", "\App\Controller\Websocket@index"),
    WsRouter("/login", "\App\Controller\Websocket@login"),
];
```
完整的URL地址组成` ws://ip网址:端口`。


### **6.6websocket控制器**
对应的控制器方法默认有两个参数 `$server` 和 `$msg`，关于 `$server`对象完整的介绍请查看 Swoole 文档：[WebSocket\Server](https://wiki.swoole.com/#/websocket_server?id=%25e6%2596%25b9%25e6%25b3%2595)。 `$msg`是客户端发送的数据信息。

##### **补充：websocket客户端消息传入格式**

 客户端发送的数据信息，默认需以json格式传送，必须包含id、path、data三个字段。
* `id`字段消息体的唯一标识。
* `path`字段是`./route/websocket.php`路由声明的访问地址。
* `data`字段是项目的具体消息参数，控制方法默认的`$msg`。
```json
{
    "id":"918wsEMQDrj0RXxm",
    "path":"/",
    "data": {
        "username": "api-swoole"
    }
}
```

控制器代码部分示例：

```
<?php

namespace App\Controller;

use Sapi\Api;

class Websocket extends Api
{
    public function index(\Swoole\WebSocket\Server $server, array $msg): array
    {
        return [
            'err' => 200,
            'data' => [
                'name' => 'api-swoole',
                'version' => '1.0.0',
            ]
        ];
    }
}
```


### **6.7websocket接口参数规则**

接口参数规则通过继承`Api`类实现，具体的定义规则通过`rule()`方法。
*   一维下标是接口类的方法名。
*   二维下标是接口参数名称。
*   三维下标`name`对应客户端传入的值，下标`require`表示值传入可选项,`true`必须传入，`false`非必须传入。


```
<?php

namespace App\Controller;

use Sapi\Api;

class Websocket extends Api
{
    public function rule()
    {
        return [
            'login' => [
                'username' => ['name' => 'username', 'require' => true]
            ]
        ];
    }

    public function login(\Swoole\WebSocket\Server $server, array $msg): array
    {
        return [
            'err' => 200,
            'data' => [
                'username' => $msg['username'],
            ]
        ];
    }

}
```


### **6.8http/websocket勾子函数**

`Api`类内置了钩子函数`userCheck`,HTTP/websocket控制器均可继承`Api`类重载。例如可完成用户身份验证。


首先定义声明`Base.php`文件。
```
<?php

namespace App\Controller;

use Sapi\Api;

class Base extends Api
{
    //用户权限验证
    public function userCheck()
    {
        if (true) {
            return "token过期";
        }
    }

}
```

然后继承`Base.php`实现类重载，。

```
<?php

namespace App\Controller;

use Sapi\Api;

class Auth extends Base
{

    public function rule()
    {
        return [
            'login' => [
                'username' => ['name' => 'username', 'require' => true]
            ]
        ];
    }

    public function login(\Swoole\Http\Request $request, \Swoole\Http\Response $response): array
    {
        return [
            "code" => 200,
            "msg" => "login",
            "data" => [
                'username' => $request->post['username']
            ]
        ];
    }
}
```



# **(七).TCP/UDP服务器**

`Server` 可以监听多个端口，每个端口都可以设置不同的协议处理方式，例如 80 端口处理 HTTP 协议，9500 端口处理 TCP 协议,9502 端口处理 UDP 协议。`SSL/TLS` 传输加密也可以只对特定的端口启用。参考Swoole官方文档[(多端口监听)](https://wiki.swoole.com/#/server/port)

### **7.1TCP服务器配置**
TCP服务器配置选项在`./config/conf.php`中增加**tcp**字段。具体配置信息可以参考Swoole文档[TCP配置。](https://wiki.swoole.com/#/server/setting)

```
    'tcp' => [
        'host' => '0.0.0.0',
        'port' => 9500,
        'sockType' => SWOOLE_SOCK_TCP,
        'events' => [
            ['receive', \App\Controller\TcpServe::class, 'onReceive'],//TCP服务器回调
        ],
        'settings' => [],
    ],
```

### **7.1UDP服务器配置**
UDP服务器配置选项在`./config/conf.php`中增加**udp**字段。具体配置信息可以参考Swoole文档[UDP配置。](https://wiki.swoole.com/#/server/setting)



```
    'udp' => [
        'host' => '0.0.0.0',
        'port' => 9502,
        'sockType' => SWOOLE_SOCK_UDP,
        'events' => [
            ['packet', \App\Controller\UdpServe::class, 'onPacket'],//UDP服务器回调
        ],
        'settings' => [],
    ],
```
# **(八).数据库**

Swoole 开发组采用 Hook 原生 PHP 函数的方式实现协程客户端，通过一行代码就可以让原来的同步 IO 的代码变成可以协程调度的异步 IO，即一键协程化。`Swoole` 提供的 [Swoole\Server 类簇](https://wiki.swoole.com/#/server/init)都是自动做好的，不需要手动做，参考 [enable_coroutine](https://wiki.swoole.com/#/server/setting?id=enable_coroutine)。具体内容可参考Swoole官网[一键协程化](https://wiki.swoole.com/#/runtime)

框架数据库引入`simple-swoole`第三方拓展包具体详情可参考[simple-swoole/db](https://github.com/simple-swoole/db)

### **8.1Redis**

#### **配置** 
Redis服务器配置选项在`./config/db.php`中。

```
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
];
```
#### **使用**
项目中使用Redis


```
<?php

namespace App\Controller;

use App\Ext\Redis;

class RedisDemo
{
    public function setData(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $redis = new \Simps\DB\BaseRedis();
        $res = $redis->set('我是key', '我是value');
        return [
            "code" => 200,
            "msg" => "hello World!",
            "data" => [
                'res' => $res,
                'key' => $request->get['key'],
                'val' => $request->get['val'],
            ],
        ];
    }
}
```
### **8.2MySQL**
#####  **8.2.1配置**
MySQL服务器配置选项在`./config/db.php`中。

```
<?php
return [
    'mysql' => [
        'host' => '',//连接地址
        'port' => ,//连接端口
        'database' => '',//数据库名称
        'username' => 'root',//用户
        'password' => '',//密码
        'charset' => 'utf8',//字符集
        'unixSocket' => null,//
        'options' => [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
        'size' => 64 // 连接池数量
    ],
];
```
`./app/Ext/Pool.php`配置连接池：

```
<?php

namespace App\Ext;

use Sapi\Singleton;
use Simps\DB\PDO;
use Simps\DB\Redis;

class Pool
{
    use Singleton;

    public function startPool(...$args)
    {
        $mysql_config = DI()->config->get('db.mysql');
        if (!empty($mysql_config)) {
            PDO::getInstance($mysql_config);
        }

        $redis_config = DI()->config->get('db.redis');
        if (!empty($redis_config)) {
            Redis::getInstance($redis_config);
        }
    }
}
```
#####  **8.2.2Medoo**
`simple-swoole`集成了轻量级的 PHP 数据库框架 [Medoo](https://medoo.lvtao.net/index.php) ，使用时需要继承 `Simps\DB\BaseModel`，所以使用方法和 Medoo 基本一致，具体请查看 [Medoo 的文档](https://medoo.lvtao.net/1.2/doc.php)

唯一不同的是事务相关操作，在 Medoo 中是使用 `action( $callback )` 方法，而在本框架中也可以使用 `action( $callback )` 方法，另外也支持以下方法

```
beginTransaction(); // 开启事务
commit(); // 提交事务
rollBack(); // 回滚事务
```

事务示例

```
$this->beginTransaction();

$this->insert("user", [
    "name" => "luffy",
    "gender" => "1"
]);

$this->delete("user", [
    "id" => 2
]);

if ($this->has("user", ["id" => 23]))
{
    $this->rollBack();
} else {
    $this->commit();
}
```
#### **8.2.3使用** 
项目中使用，数据库表结构：


```
CREATE TABLE `user_info` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1000001 DEFAULT CHARSET=utf8;
```


```
<?php

namespace App\Controller;


class MysqlDemo
{
    public function getOne(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $uid = $request->post['uid'];

        $database = new \Simps\DB\BaseModel();
        $res = $database->select("user_info", [
            "uid",
            "nick",
        ], [
            "uid" => $uid
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo getOne",
            "data" => [
                'res' => $res,
                'uid' => $uid,
            ],
        ];
    }

    public function save(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $username = $request->post['username'];
        $database = new \Simps\DB\BaseModel();
        $last_user_id = $database->insert("user_info", [
            "uid" => time(),
            "nick" => $username,
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo save",
            "data" => [
                'last_user_id' => $last_user_id,
                'username' => $username,
            ],
        ];
    }

    public function del(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $uid = $request->post['uid'];

        $database = new \Simps\DB\BaseModel();

        $res = $database->delete("user_info", [
            "uid" => $uid
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo del",
            "data" => [
                'res' => $res,
                'uid' => $uid,
            ],
        ];
    }

    public function update(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $uid = $request->post['uid'];
        $username = $request->post['username'];

        $database = new \Simps\DB\BaseModel();

        $res = $database->update("user_info", [
            "nick" => $username
        ], [
            "uid" => $uid
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo update",
            "data" => [
                'res' => $res,
                'uid' => $uid,
                'username' => $username,
            ],
        ];
    }
}
```


# **(九).addProcess**

添加一个用户自定义的工作进程。此函数通常用于创建一个特殊的工作进程，用于监控、上报或者其他特殊的任务。具体内容可参考Swoole官网[**addProcess**](https://wiki.swoole.com/#/server/methods?id=addprocess)

**注意事项：**
- 创建的子进程可以调用 `$server` 对象提供的各个方法，如 `getClientList/getClientInfo/stats`
- 在 `Worker/Task` 进程中可以调用 `$process` 提供的方法与子进程进行通信
- 在用户自定义进程中可以调用 `$server->sendMessage` 与 `Worker/Task` 进程通信
- 用户进程内不能使用 `Server->task/taskwait` 接口
- 用户进程内可以使用 `Server->send/close` 等接口
- 用户进程内应当进行 `while(true)`(如下边的示例) 或 [EventLoop](https://wiki.swoole.com/#/learn?id=%e4%bb%80%e4%b9%88%e6%98%afeventloop) 循环 (例如创建个定时器)，否则用户进程会不停地退出重启

**使用示例**

添加一个用户自定义的工作进程，在`./config/conf.php`中增加`process`配置，如下：

```
'process' => [
        [\App\Controller\Process::class, 'addProcess']
    ],
```
控制器示例：

```
<?php

namespace App\Controller;

class Process
{
    //添加用户自定义的工作进程
    public function addProcess($server)
    {
        return new \Swoole\Process(function ($process) use ($server) {
            while (true) {
                \Co::sleep(1);
                echo "Hello, api-swoole!\r\n";
            }
        }, false, 2, 1);
    }
}
```

# **(十).订制事件注册**

框架已默认埋点`workerStart、open、close、task、finish`五个订制事件注册。可根据具体的业务特征在对应的事件回调增加处理业务代码。`./confg/events.php`，每个事件允许有多个回调

```
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
];
```


