<?php
$pid = 240;
$signo = SIGTERM;
var_dump(Swoole\Process::kill($pid, $signo));
//Swoole\Coroutine\run(function () {
//    $ret = Swoole\Coroutine\System::exec("md5sum " . __FILE__);
//    var_dump($ret);
//});
