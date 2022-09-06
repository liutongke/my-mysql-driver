<?php
/*
 * User: keke
 * Date: 2022/9/5
 * Time: 12:08
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

use Sapi\Router\HttpRouter;

class HttpRequest
{
    use Singleton;

    public function __construct()
    {

    }

    public function handlerMsg(\Swoole\Http\Request $request, \Swoole\Http\Response $response, \Swoole\WebSocket\Server $server)
    {
        if (
            $request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico' ||
            $request->server['request_uri'] == '/favicon.png'
        ) {
            $response->end();
            return;
        }

        DI()->runTm->start();

        $rs = new HttpResponse($response);

        register_shutdown_function(function () use ($rs) {
            $error = error_get_last();
            if (!empty($error)) {
                DI()->logger->error($error['message']);
                $rs->setStatus(HttpCode::$StatusInternalServerError);
                $rs->setCode(HttpCode::$StatusInternalServerError);
                $rs->setData($error['message']);
                $rs->output();
            }
        });

        $pathUrl = strtolower($request->server['path_info']);//请求的地址
        $setUrlList = HttpRouter::GetHandlers();
        if (!isset($setUrlList[$pathUrl])) {
//            DI()->logger->error($request);
            $rs->setStatus(HttpCode::$StatusNotFound);
            $rs->setCode(HttpCode::$StatusNotFound);
            $rs->setData('url not find');
            $rs->output();
            return;
        }
        $data = null;

        switch ($request->server['request_method']) {
            case 'GET':
                $data = $request->get;
                break;

            case 'POST':
                $data = $request->post;
                break;
        }
        $routeInfo = $setUrlList[$pathUrl];

        $rs->setStatus(HttpCode::$StatusOK);

        if (is_array($routeInfo) && method_exists($routeInfo['0'], 'getRules')) {//先处理必须携带的参数
            $rule = $routeInfo['0']->getRules($data, $routeInfo['1']);
        }

        if (isset($rule['res']) && $rule['res']) {//验证未通过
            $rs->setCode(HttpCode::$StatusBadRequest);
            $rs->setData($rule['data']);
        } else {
            $rs->setCode(HttpCode::$StatusOK);
            $rs->setData(call_user_func_array($routeInfo, [$request, $response, $server]));
        }
        DI()->logger->echoHttpCmd($request, $response, $server, DI()->runTm->end());
        $rs->output();
    }
}