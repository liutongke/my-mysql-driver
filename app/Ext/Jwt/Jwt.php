<?php
/*
 * User: keke
 * Date: 2018/7/26
 * Time: 20:17
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

namespace chat\sw\Ext\Jwt;

abstract class Jwt
{
//    public static function __callStatic($fd, $arguments)
//    {
//        return self::fromUser($fd);
//    }

    //生成token
    public static function fromUser($data)
    {
        return self::getToken($data);
    }

    //生成token
    public function getToken($fd)
    {
        $header = self::header();
        $payload = self::payload($fd);
        $secret = self::secret($header, $payload);
        return $header . '.' . $payload . '.' . $secret;
    }

    //header
    public function header()
    {
        return base64_encode(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]));
    }

    //payload
    public function payload($user_id)
    {
        return base64_encode(json_encode([
            #非必须。issuer 请求实体，可以是发起请求的用户的信息，也可是jwt的签发者。
            "iss" => $_SERVER['PHP_SELF'],
            #非必须。issued at。 token创建时间，unix时间戳格式
            "iat" => $_SERVER['REQUEST_TIME'],
            #非必须。expire 指定token的生命周期。unix时间戳格式
            "exp" => $_SERVER['REQUEST_TIME'] + 7200,
            #非必须。该JWT所面向的用户
            "sub" => $user_id,
            # 非必须。not before。如果当前时间在nbf里的时间之前，则Token不被接受；一般都会留一些余地，比如几分钟。
            "nbf" => $_SERVER['REQUEST_TIME'] + 7200,
            # 非必须。JWT ID。针对当前token的唯一标识
            "jti" => '222we',

        ]));
    }

    //secret
    public function secret($header, $payload)
    {
        $str = $header + '.' + $payload;
        return hash('sha256', $str);
    }

    //base64解码
    public function base()
    {
        return;
    }

    //解密第二部分
    public function decode()
    {
        return json_decode(base64_decode(explode('.', $this->token)['1']), true);
    }
}