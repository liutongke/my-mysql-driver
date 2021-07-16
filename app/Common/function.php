<?php
/*
 * User: keke
 * Date: 2018/7/26
 * Time: 16:09
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
function DI()
{
    return \chat\sw\Core\Di::one();
}

function EchoHtml(\Swoole\Http\Response $response, $htmlPathName)
{
    $response->header("Content-Type", "text/html; charset=utf-8");
//    var_dump(file_get_contents(ROOT_PATH . "/public/" . $htmlPathName));
    $response->end(file_get_contents(ROOT_PATH . "public/chatroom/" . $htmlPathName));
}

//websocket路由设置
function WsRouter($url, $callable)
{
    \chat\sw\Router\WsRouter::Register($url, $callable);
}

//http路由设置
function HttpRouter($url, $callable)
{
    \chat\sw\Router\HttpRouter::Register($url, $callable);
}

function DB()
{
    return $database = new \Medoo\Medoo([
        // required
        'database_type' => 'mysql',
        'database_name' => $GLOBALS['config']['mysql']['database_name'],
        'server' => $GLOBALS['config']['mysql']['host'],
        'username' => $GLOBALS['config']['mysql']['username'],
        'password' => $GLOBALS['config']['mysql']['password'],

        // [optional]
        'charset' => 'utf8',
        'port' => $GLOBALS['config']['mysql']['port'],

        // [optional] CoTable prefix
        'prefix' => $GLOBALS['config']['mysql']['prefix'],

        // [optional] Enable logging (Logging is disabled by default for better performance)
        'logging' => true,

        // [optional] MySQL socket (shouldn't be used with server and port)
//        'socket' => '/tmp/mysql.sock',

        // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],

        // [optional] Medoo will execute those commands after connected to the database for initialization
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ]);
}

//Redis数据库
function Redis()
{
    static $config;
    if (!$config)
        $config = $GLOBALS['config'];

    static $redis;
    if (!$redis)
        $redis = new swoole\Redis($config['redis']);
    return $redis;
}

//中断格式化打印
function dd($data)
{
    echo '<pre />';
    var_dump($data);
    echo '<pre />';
    die;
}

//不中断格式化打印
function dump($data)
{
    echo '<pre />';
    var_dump($data);
    echo '<pre />';
}

//下载 sys_download_file('', true, true);
function sys_download_file($path, $name = null, $isRemote = false, $isSSL = false, $proxy = '')
{
    $url = str_replace(" ", "%20", $path);

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $temp = curl_exec($ch);
        echo $temp;
        file_put_contents("test.pdf", $temp);
    }
}