<?php

/*
 * User: keke
 * Date: 2017/5/14
 * Time: 1:54
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

class Wechat
{
    //封装
    // public  公共   都可以调用使用
    // private  私有  类内调用
    // protected  受保护  继承类使用
    private $appid;
    private $appsecret;
    private $token;

    //初始化成员变量
    public function __construct()
    {
        $this->appid = APPID;
        $this->appsecret = APPSECRET;
        $this->token = TOKEN;
    }

    //封装定义一个请求方法
    private function request($url, $https = true, $method = 'get', $data = null)
    {
        //1初始化
        $ch = curl_init($url);
        //2设置参数，直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //如果支持https协议
        if ($https === true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        //如果支持post传输
        if ($method === 'post') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //3发送请求
        $content = curl_exec($ch);
        //4关闭资源链接
        curl_close($ch);
        return $content;
    }

    //获取access token值
    public function getaccesstoken()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
        $content = $this->request($url);
        $content = json_decode($content);
        return $content->access_token;
    }

    //将access_token存入到redis中，已方便调取
    public function getAccessTokenByRedis()
    {
        //链接redis判断是否有缓存
       $redis = linkRedis();
        // $redis = new \Redis();
        // $redis->connect('127.0.0.1', 6379);
        $access_token = $redis->get('accessToken');
        if ($access_token === false) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
            $content = $this->request($url);
            $content = json_decode($content);
            $access_token = $content->access_token;
            //远程获取以后，要缓存到redis中
            $redis->set('accessToken', $access_token);
            // $redis->setTimeout('accessToken', 7100);
            $redis->setTimeout('accessToken', 7100);
        }
        return $access_token;
    }
}