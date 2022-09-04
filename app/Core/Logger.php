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
    private $debug;

    public function __construct($logDir, $debug)
    {
        $this->logFolder = "{$logDir}runtime";
        $this->debug = $debug;

        if (!file_exists($this->logFolder)) {
            mkdir($this->logFolder, 0777, TRUE);
        }
    }

    public static function println($strings)
    {
        echo $strings . PHP_EOL;
    }

    public static function echoSuccessCmd($msg)
    {
        self::println("[Success] \033[32m{$msg}\033[0m");
    }

    public static function echoErrCmd($msg)
    {
        self::println('[ERROR] ' . "\033[31m{$msg}\033[0m");
    }

    public function echoWsCmd(\Swoole\WebSocket\Server $server, $fd, $runTime)
    {
        if ($this->debug) {
            $clientInfo = $server->getClientInfo($fd);
            $lastTime = $clientInfo['last_time'];
            $remoteIp = $clientInfo['remote_ip'];
            $requestTm = date("Y/m/d-H:i:s", $lastTime);

            $this->console("[websocket] | {$requestTm} | $remoteIp | 200 | {$runTime}\n");
        }
    }

    public function echoHttpCmd(\Swoole\Http\Request $request, \Swoole\Http\Response $response, \Swoole\WebSocket\Server $server, $runTime)
    {
        if ($this->debug) {
            $clientInfo = $server->getClientInfo($request->fd);
            $lastTime = $clientInfo['last_time'];
            $remoteIp = $clientInfo['remote_ip'];
//        [GIN] 2022/08/31 - 17:59:38 | 200 |     17.2792ms |   192.168.0.105 | GET      "/"
            $requestTm = date("Y/m/d-H:i:s", $lastTime);

            $this->console("[http] | {$requestTm} | $remoteIp | 200 | {$runTime} |  {$request->server['path_info']} | {$request->server['request_method']}\n");
        }
    }

    public function console($msg)
    {
        echo $msg;
    }

    public function log($msg, $logLevel)
    {
        $prefix = date('Ym');
        $date = date('Y-m-d H:i:s');
        $levelStr = $this->levelMap($logLevel);
        $filePath = $this->logFolder . "/{$prefix}.log";
        $logData = "[swoole] | [{$date}] | {$levelStr} |  {$msg}\n";
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