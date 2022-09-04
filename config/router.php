<?php
/*
 * User: keke
 * Date: 2021/7/16
 * Time: 10:58
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
return [
    'http' => [
        \HttpRouter("/", "\chat\sw\Controller\App@Index"),
        \HttpRouter("/post", "\chat\sw\Controller\App@post"),
        \HttpRouter("/t", function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            $response->end('hello');
        }),
    ],
    'ws' => [
        WsRouter("websocket", "\chat\sw\Controller\WsController@stop"),
    ]
];