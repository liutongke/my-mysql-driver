<?php


namespace App\Ext;

class Timer
{
    public static function dateId()
    {
        return date("Ymd");
    }

    public static function now()
    {
        return date("Y-m-d H:i:s");
    }
}