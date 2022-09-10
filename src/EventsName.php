<?php
/*
 * User: keke
 * Date: 2022/9/10
 * Time: 12:59
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

namespace Sapi;


class EventsName
{
    public static $onStart = 'start';
    public static $onShutdown = 'shutdown';
    public static $onWorkerStart = 'workerStart';
    public static $onWorkerStop = 'workerStop';
    public static $onWorkerExit = 'workerExit';
    public static $onTimer = 'timer';
    public static $onConnect = 'connect';
    public static $onReceive = 'receive';
    public static $onPacket = 'packet';
    public static $onClose = 'close';
    public static $onBufferFull = 'bufferFull';
    public static $onBufferEmpty = 'bufferEmpty';
    public static $onTask = 'task';
    public static $onFinish = 'finish';
    public static $onPipeMessage = 'pipeMessage';
    public static $onWorkerError = 'workerError';
    public static $onManagerStart = 'managerStart';
    public static $onManagerStop = 'managerStop';
    public static $onRequest = 'request';
    public static $onHandShake = 'handShake';
    public static $onMessage = 'message';
    public static $onOpen = 'open';
}