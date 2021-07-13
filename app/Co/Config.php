<?php
/*
 * User: keke
 * Date: 2021/7/13
 * Time: 10:57
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

namespace chat\sw\Co;

class Config
{
    private $path = '';
    private $confMap = [];

    public function __construct($confDirPath)
    {
        $this->path = $confDirPath;
    }

    public function get($key, $default = NULL)
    {
        $keyArr = explode('.', $key);
        $fileName = $keyArr['0'];
        if (!isset($this->confMap[$fileName])) {
            $this->loadConfig($fileName);
        }
        $confData = $this->confMap[$fileName];
        foreach ($keyArr as $idx) {
            if (isset($confData[$idx])) {
                $data = $confData[$idx];
                break;
            }
        }
        return $data ?? $default;
    }

    private function loadConfig($fileName)
    {
        $filePath = $this->path . DIRECTORY_SEPARATOR . $fileName . ".php";
        $this->confMap[$fileName] = include_once($filePath);
    }
}