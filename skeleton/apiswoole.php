<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Sapi\Config;
use \Sapi\CoServer;

date_default_timezone_set('Asia/Shanghai');// 时区设置

define('DS', DIRECTORY_SEPARATOR);           //目录分隔符
define('ROOT_PATH', getcwd() . DS);               //入口文件所在的目录
define('CONFIG_PATH', ROOT_PATH . 'config' . DS);

$di = DI();
$di->config = new Config("./config");

// 调试模式
if ($di->config->get('conf.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

CoServer::getInstance()->start();