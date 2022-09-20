<?php

namespace Co;

class Utils
{
    public static function setProcessName(string $name): bool
    {
        return swoole_set_process_name($name);
    }
}