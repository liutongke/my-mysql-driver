<?php
/*
 * User: keke
 * Date: 2018/7/26
 * Time: 14:10
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
require_once __DIR__ . '/vendor/autoload.php';

use \chat\sw\Co\Websocket;
use \chat\sw\Co\CoWs;
use \chat\sw\Core\Config;
use \chat\sw\Core\CoHttp;

date_default_timezone_set('Asia/Shanghai');// 时区设置

define('DS', DIRECTORY_SEPARATOR);           //目录分隔符
define('ROOT_PATH', getcwd() . DS);               //入口文件所在的目录
//define('APP_PATH',ROOT_PATH.'Application'.DS);
//define('FRAMEWORK_PATH', ROOT_PATH.'Framework'.DS);
define('CONFIG_PATH', ROOT_PATH . 'config' . DS);
//define('CONTROLLER_PATH', APP_PATH.'Controller'.DS);
//define('MODEL_PATH', APP_PATH.'Model'.DS);
//define('VIEW_PATH', APP_PATH.'View'.DS);
//define('CORE_PATH', FRAMEWORK_PATH.'Core'.DS);
//define('LIB_PATH', FRAMEWORK_PATH.'Lib'.DS);
//var_dump(DS, ROOT_PATH, CONFIG_PATH);
$di = DI();
$di->config = new Config("./config");

// 调试模式
if ($di->config->get('conf.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

\chat\sw\Core\CoServer::getInstance()->start();
//(new \chat\sw\Core\CoServer())->start();