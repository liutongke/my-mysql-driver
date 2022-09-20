<?php
function getCpuNum(): int
{
    $num = swoole_cpu_num();
    return empty($num) ? 1 : $num;
}