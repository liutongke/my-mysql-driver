<?php

function DI()
{
    return \Sapi\Di::one();
}

//websocket路由设置
function WsRouter($url, $callable)
{
    \Sapi\Router\WsRouter::Register($url, $callable);
}

//http路由设置
function HttpRouter($url, $callable)
{
    \Sapi\Router\HttpRouter::Register($url, $callable);
}

//不中断格式化打印
function dump($data)
{
    echo '<pre />';
    var_dump($data);
    echo '<pre />';
}

//下载 sys_download_file('', true, true);
function sys_download_file($path, $name = null, $isRemote = false, $isSSL = false, $proxy = '')
{
    $url = str_replace(" ", "%20", $path);

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $temp = curl_exec($ch);
        echo $temp;
        file_put_contents("test.pdf", $temp);
    }
}