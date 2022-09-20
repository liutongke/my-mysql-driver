<?php
require_once __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('Asia/Shanghai');// 时区设置
(new \Co\CoServer())->start();