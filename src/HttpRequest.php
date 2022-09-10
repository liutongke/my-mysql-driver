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

use Sapi\Error\MyError;
use Sapi\Router\HttpRouter;

class HttpRequest
{
    use Singleton;

    public function __construct()
    {

    }

    public function handlerMsg(\Swoole\Http\Request $request, \Swoole\Http\Response $response, \Swoole\WebSocket\Server $server)
    {
        $chromeRule = ['/favicon.ico', '/favicon.png'];
        if (in_array($request->server['path_info'], $chromeRule) || in_array($request->server['request_uri'], $chromeRule)) {
            $response->end();
            return;
        }

        DI()->runTm->start();

        $rs = new HttpResponse($response);
        try {
            $pathUrl = strtolower($request->server['path_info']);//请求的地址
            $setUrlList = HttpRouter::GetHandlers();
            if (!isset($setUrlList[$pathUrl])) {
                $rs->setStatus(HttpCode::$StatusNotFound);
                $rs->setCode(HttpCode::$StatusNotFound);
                $rs->setData('url not find');
                $rs->output();
                return;
            }

            $routeInfo = $setUrlList[$pathUrl];

            $rs->setStatus(HttpCode::$StatusOK);

            if (is_array($routeInfo) && method_exists($routeInfo['0'], 'getRules')) {//先处理必须携带的参数
                $rule = $routeInfo['0']->getRules(call_user_func(function ($request) {
                    return strcmp($request->server['request_method'], 'GET') == 0 ? $request->get : $request->post;
                }, $request), $routeInfo['1']);
            }

            if (isset($rule['res']) && $rule['res']) {//验证未通过
                $rs->setCode(HttpCode::$StatusBadRequest);
                $rs->setData($rule['data']);
            } else {
                $rs->setCode(HttpCode::$StatusOK);
                try {
                    $rs->setData(call_user_func_array($routeInfo, [$request, $response, $server]));
                } catch (\Exception $e) {
                    DI()->Error->errorHandler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
                }
            }

            DI()->logger->echoHttpCmd($request, $response, $server, DI()->runTm->end());
            $rs->output();
        } catch (\Error $e) {
            DI()->Error->errorHandler($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());

            $rs->setStatus(HttpCode::$StatusInternalServerError);
            $rs->setCode(HttpCode::$StatusInternalServerError);
            $rs->setData($e->getMessage());

            DI()->logger->echoHttpCmd($request, $response, $server, DI()->runTm->end(), HttpCode::$StatusInternalServerError);

            $rs->output();
        }
    }
}