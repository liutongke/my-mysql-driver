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

namespace chat\sw\Core;

class Jwt
{
    private $token;

    public function __construct($token)
    {
        return $this->token = $token;
    }

    //将token切割成数组
    public function exToken()
    {
        return self::base();
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