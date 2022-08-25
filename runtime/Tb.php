<?php
/*
 * User: keke
 * Date: 2021/7/15
 * Time: 14:05
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
// 实例化一个占用的共享内存大小为1024的内存表
$table = new Swoole\Table(1024);
// 内存表增加3列
$table->column('fd', Swoole\Table::TYPE_INT);
$table->column('reactor_id', Swoole\Table::TYPE_INT);
$table->column('data', Swoole\Table::TYPE_STRING, 64);
$table->create();

$serv = new Swoole\Server('127.0.0.1', 9501);
// 设置数据包分发策略：轮循模式
$serv->set(['dispatch_mode' => 1]);
$serv->table = $table;

$serv->on('receive', function ($serv, $fd, $reactor_id, $data) {
    $cmd = explode(" ", trim($data));

    if ($cmd[0] == 'get') {
        //get self
        if (count($cmd) < 2) {
            $cmd[1] = $fd;
        }
        $get_fd = intval($cmd[1]);
        $info = $serv->table->get($get_fd);
        $serv->send($fd, var_export($info, true) . "\n");
    } elseif ($cmd[0] == 'set') {
        // 使用连接的文件描述符作为key写入内存表
        $ret = $serv->table->set($fd, array('fd' => $fd, 'reactor_id' => $reactor_id, 'data' => $cmd[1]));
        if ($ret === false) {
            $serv->send($fd, "ERROR\n");
        } else {
            $serv->send($fd, "OK\n");
        }
    } else {
        $serv->send($fd, "command error.\n");
    }
});

$serv->start();