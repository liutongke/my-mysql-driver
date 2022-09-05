# 一.开始
## 下载与安装

*   PHP >= 7.4
*   Swoole PHP 扩展 >= 4.5.9

## 通过 Composer 创建项目

```
composer require api-swoole/library:dev-master
```

支持 HTTP 服务、WebSocket 服务、tcp服务。

# **Hello World**

### **编写一个接口**


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

use Sapi\Rule;

class Hello extends Rule
{
    public function rule()
    {

    }

    public function index()
    {
        return [
            'code' => 200,
            'data' => 'hello world'
        ];
    }
}
```

### **定义路由**
在`config/http.php`路由文件中定义项目路由，第一个参数为定义的浏览器访问地址，第二个参数`@`前半部分为文件的完整命名空间，`@`后半部分为在类中调用的具体方法。


```
return [
    \HttpRouter("/hello", "App\Controller\Hello@index"),
];
```

### **启动项目**


在项目根目录下执行如下命令以非守护模式启动。

```
php apiswoole.php
```
默认情况下监听本地的HTTP和websocket的9501端口，在cmd中出现如下输出信息表示项目启动成功。

```
[Success] Swoole: 4.5.9, PHP: 7.4.13, Port: 9501
[Success] Swoole Http Server running：http://0.0.0.0:9501
[Success] Swoole websocket Server running：ws://0.0.0.0:9501
```

### **访问接口**

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


## 反向代理（Nginx + Swoole 配置）
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


# HTTP服务
## 路由



```
return [
    \HttpRouter("/", "App\Controller\App@Index"),
    \HttpRouter("/post", "App\Controller\App@post"),
    \HttpRouter("/hello", function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
        $response->end('hello');
    })
];
```


### 接口参数规则
### 路由定义
### 路由参数

## 控制器

# websocket服务
## 路由


```
return [
    WsRouter("/", "\App\Controller\WsController@index"),
];
```


### 接口参数规则
### 路由定义
### 路由参数

## 控制器

# 自定义服务

# 配置

# 日志

# tcp服务

[多端口监听](https://wiki.swoole.com/#/server/port)
