<?php


namespace App\Controller;


use Sapi\Singleton;

class EventsDemo
{
    use Singleton;

    public function workerStart(...$args)
    {
        var_dump('workerStart');
    }

    public function open(...$args)
    {
        var_dump('open');
    }

    public function close(...$args)
    {
        var_dump('close');
    }

    public function task(...$args)
    {
        var_dump('task');
    }

    public function finish(...$args)
    {
        var_dump('finish');
    }
}