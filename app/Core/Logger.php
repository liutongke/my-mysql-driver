<?php

/*
 * User: keke
 * Date: 2022/8/31
 * Time: 17:42
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

class Logger
{
    use Singleton;

    private $logFolder;

    public function __construct($logDir)
    {
        $this->logFolder = "{$logDir}runtime";

        if (!file_exists($this->logFolder)) {
            mkdir($this->logFolder, 0777, TRUE);
        }
    }

    public static function echoCmd(\Swoole\Http\Request $request, \Swoole\Http\Response $response, \Swoole\WebSocket\Server $server)
    {
//        [GIN] 2022/08/31 - 17:59:38 | 200 |     17.2792ms |   192.168.0.105 | GET      "/"
        $requestTm = date("Y/m/d-H:i:s", $request->server['request_time']);
//        var_dump($request, $response);
//        $server['path_info']
//        $server['request_method']

        $fd_info = $server->getClientInfo($request->fd);

//        $runTime = (microtime() - $fd_info['last_time']) * 1000 . ' ms';
        $runTime = '0.1ms';
        echo "[swoole] | {$requestTm} | 200 | {$runTime} | {$request->server['remote_addr']} | {$request->server['path_info']} | {$request->server['request_method']}\n";
    }

    private function log($msg, $logLevel)
    {
        $prefix = date('Ym');
        $date = date('Y-m-d H:i:s');
        $levelStr = $this->levelMap($logLevel);
        $filePath = $this->logFolder . "/{$prefix}.log";
        $logData = "[{$date}] | {$levelStr} |  {$msg}\n";
        file_put_contents($filePath, "{$logData}", FILE_APPEND | LOCK_EX);
        return $logData;
    }

    public function debug($msg)
    {
        $this->log($msg, self::LOG_LEVEL_DEBUG);
    }

    public function info($msg)
    {
        $this->log($msg, self::LOG_LEVEL_INFO);
    }

    public function notice($msg)
    {
        $this->log($msg, self::LOG_LEVEL_NOTICE);
    }

    public function waring($msg)
    {
        $this->log($msg, self::LOG_LEVEL_WARNING);
    }

    public function error($msg)
    {
        $this->log($msg, self::LOG_LEVEL_ERROR);
    }

    const LOG_LEVEL_DEBUG = 0;
    const LOG_LEVEL_INFO = 1;
    const LOG_LEVEL_NOTICE = 2;
    const LOG_LEVEL_WARNING = 3;
    const LOG_LEVEL_ERROR = 4;

    private function levelMap($level)
    {
        switch ($level) {
            case self::LOG_LEVEL_DEBUG:
                return 'debug';
            case self::LOG_LEVEL_INFO:
                return 'info';
            case self::LOG_LEVEL_NOTICE:
                return 'notice';
            case self::LOG_LEVEL_WARNING:
                return 'warning';
            case self::LOG_LEVEL_ERROR:
                return 'error';
            default:
                return 'unknown';
        }
    }
}