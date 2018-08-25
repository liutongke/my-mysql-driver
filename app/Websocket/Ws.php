<?php
/*
 * User: keke
 * Date: 2018/7/26
 * Time: 15:03
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

namespace chat\sw\Websocket;

class Ws
{
    //服务对象
    private $ws;

    //new的时候自动加载
    public function __construct()
    {
        self::initConst();
        self::initConfig();
        $this->init();
    }

    /*
     * 加载配置文件
     */
    private static function initConfig()
    {
        $GLOBALS['config'] = require CONFIG_PATH . 'config.php';
    }

    /*
     * 定义路径常量
     */
    private static function initConst()
    {
        define('DS', DIRECTORY_SEPARATOR);           //目录分隔符
        define('ROOT_PATH', getcwd() . DS);               //入口文件所在的目录
//define('APP_PATH',ROOT_PATH.'Application'.DS);
//define('FRAMEWORK_PATH', ROOT_PATH.'Framework'.DS);
        define('CONFIG_PATH', ROOT_PATH . 'config' . DS);
//define('CONTROLLER_PATH', APP_PATH.'Controller'.DS);
//define('MODEL_PATH', APP_PATH.'Model'.DS);
//define('VIEW_PATH', APP_PATH.'View'.DS);
//define('CORE_PATH', FRAMEWORK_PATH.'Core'.DS);
//define('LIB_PATH', FRAMEWORK_PATH.'Lib'.DS);
    }

    //初始程序
    public function init()
    {
        $this->ws = new \swoole_websocket_server("0.0.0.0", 9502);
        $this->ws->set(array(
            'log_file' => ROOT_PATH . $GLOBALS['config']['swoole']['logs'],
        ));
        $this->ws->on('open', [$this, 'open']);
        $this->ws->on('message', [$this, 'message']);
        $this->ws->on('close', [$this, 'close']);
    }

    //启动程序
    public function run()
    {
        $this->ws->start();
    }

    //连接事件
    public function open($ws, $request)
    {
        //查询用户有没有登陆，如果没登陆不让其发言
        /*
         * 生成依赖
         * 注入依赖
         */
        $pb = new SendChat(new Open());
        $pb->send($ws, $request);
    }

    //消息事件
    public function message($ws, $frame)
    {
        /*
         * 生成依赖
         * 注入依赖
         */
        $pb = new SendChat(new Message());
        $pb->send($ws, $frame);
    }

    //断开事件
    public function close($ws, $fd)
    {
        /*
         * 生成依赖
         * 注入依赖
         */
        $pb = new SendChat(new Close());
        $pb->send($ws, $fd);
    }
}