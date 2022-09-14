<?php

namespace App\Ext;

use Sapi\Singleton;
use Simps\DB\PDO;
use Simps\DB\Redis;

class Pool
{
    use Singleton;

    public function startPool(...$args)
    {
        $mysql_config = DI()->config->get('db.mysql');
        if (!empty($mysql_config)) {
            PDO::getInstance($mysql_config);
        }

        $redis_config = DI()->config->get('db.redis');
        if (!empty($redis_config)) {
            Redis::getInstance($redis_config);
        }
    }
}