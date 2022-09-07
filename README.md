# **(一).开始**
### **1.1下载与安装**

需要确保运行环境达到了以下的要求：
*   PHP >= 7.4
*   Swoole PHP 扩展 >= 4.5
*   Redis PHP 扩展 （如需使用 Redis 客户端）

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
在`config/http.php`路由文件中定义项目路由，第一个参数为定义的浏览器访问地址，第二个参数`@`前半部分为文件的完整命名空间，`@`后半部分为在类中调用的具体方法。


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
### **4.1访问接口**

默认config文件夹下包含conf.php、http.php、websocket.php三个文件，conf.php放置项目的配置信息，数据库、http、tcp、udp、websocket等配置。http.php负责定义HTTP请求的路由信息，websocket.php负责定义websocket请求的路由信息。可以通过`DI()->config->get('conf.ws')`或者`\Sapi\Di::one()->config->get('conf.ws')`方式读取配置信息。
```
./config/
├── conf.php #数据库、http、tcp、udp、websocket等配置
├── http.php #http路由配置
└── websocket.php #websocket路由配置
```
### **4.1配置读取示例**
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

HTTP 服务器的路由构建文件位于`./confg/http.php`中。声明具体的路由规则，第一个参数为定义的浏览器访问地址，第二个参数`@`前半部分为文件的完整命名空间，`@`后半部分为在类中调用的具体方法。

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

websocket服务器的路由定义文件位于`./confg/websocket.php`中。声明具体的路由规则，第一个参数为定义的浏览器访问地址，第二个参数`@`前半部分为文件的完整命名空间，`@`后半部分为在类中调用的具体方法。

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
* `path`字段是`./confg/websocket.php`路由声明的访问地址。
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

### **7.1Redis**

Redis服务器配置选项在`./config/conf.php`中。


```
    'redis' => [
        'host' => 'Redis服务器地址',
        'port' => 指定 Redis 监听端口,
        'auth' => '登录密码',
        'db_index' => 指定数据库,
    ],
```
项目中使用Redis


```
use App\Ext\Redis;

$redis = Redis::getInstance();
$res = $redis->redis->set('test', 'test');
```

