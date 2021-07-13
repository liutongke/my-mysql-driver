### 启动swoole
bash: ./a.sh: /bin/bash^M: bad interpreter: No such file or directory的解决方法:sed -i "s/\r//" start.sh
- 项目根目录运行  
```shell
chmod -R 777 kill.sh
```  
赋予运行权限  
- ./kill.sh启动swoole
> chrome按f12在console调试代码：  
```js  
//普通方式
var wsServer = 'ws://ip地址:端口号';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};

//wss加密连接方式
var wsServer = 'wss://域名';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};
```