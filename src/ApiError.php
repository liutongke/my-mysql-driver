<?php
/*
 * User: keke
 * Date: 2022/9/2
 * Time: 17:58
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

namespace Sapi;

class ApiError
{
    use Singleton;

    function __construct()
    {
        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'fatalErrorHandler']);
    }

    public function fatalErrorHandler()
    {
        $last_error = error_get_last();
        if ($last_error) {
            $this->errorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }

    public function errorHandler($errno, $errstr, $errfile, $line)
    {
        $err = [
            'err_code' => $errno,
            'msg' => $errstr,
            'file' => $errfile,
            'line' => $line,
        ];
        $content = json_encode($err) . PHP_EOL;
        DI()->logger->error($content);
    }

//E_ERROR	1
//E_WARNING	2
//E_PARSE	4
//E_NOTICE	8
//E_CORE_ERROR	16
//E_CORE_WARNING	32
//E_COMPILE_ERROR	64
//E_COMPILE_WARNING	128
//E_USER_ERROR	256
//E_USER_WARNING	512
//E_USER_NOTICE	1024
//E_STRICT	2048
//E_RECOVERABLE_ERROR	4096
//E_DEPRECATED	8192
//E_USER_DEPRECATED	16384
//E_ALL	32767
}