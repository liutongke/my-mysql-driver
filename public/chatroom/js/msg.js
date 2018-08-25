$(document).ready(function(e) {
var wsServer = 'ws://ip地址:域名';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
    //接收到系统消息
    var obj = JSON.parse(evt.data);
    //给hidden赋值
    $("#token").attr("value",obj.token);
  var htmlData =   '<div class="msg_item fn-clear">'
                   + '   <div class="uface"><img src="images/hetu.jpg" width="40" height="40"  alt=""/></div>'
             + '   <div class="item_right">'
             + '     <div class="msg own">' + obj.msg + '</div>'
             + '     <div class="name_time">' + obj.username + ' · 30秒前</div>'
             + '   </div>'
             + '</div>';
  $("#message_box").append(htmlData);
  $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
  $("#message").val('');    
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};

  $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
  $('.uname').hover(
      function(){
        $('.managerbox').stop(true, true).slideDown(100);
      },
    function(){
        $('.managerbox').stop(true, true).slideUp(100);
    }
  );
  
  var fromname = $('#fromname').val();
  var to_uid   = 0; // 默认为0,表示发送给所有用户
  var to_uname = '';
  $('.user_list > li').dblclick(function(){
    to_uname = $(this).find('em').text();
    to_uid   = $(this).attr('data-id');
    if(to_uname == fromname){
        alert('您不能和自己聊天!');
      return false;
    }
    if(to_uname == '所有用户'){
        $("#toname").val('');
      $('#chat_type').text('群聊');
    }else{
        $("#toname").val(to_uid);
      $('#chat_type').text('您正和 ' + to_uname + ' 聊天');
    }
    $(this).addClass('selected').siblings().removeClass('selected');
      $('#message').focus().attr("placeholder", "您对"+to_uname+"说：");
  });
  
  $('.sub_but').click(function(event){
      sendMessage(event, fromname, to_uid, to_uname);
  });
  
  /*按下按钮或键盘按键*/
  $("#message").keydown(function(event){
    var e = window.event || event;
        var k = e.keyCode || e.which || e.charCode;
    //按下ctrl+enter发送消息
    if((event.ctrlKey && (k == 13 || k == 10) )){
      sendMessage(event, fromname, to_uid, to_uname);
    }
  });

//发送消息
function sendMessage(event, from_name, to_uid, to_uname){
    var msg = $("#message").val();
  if(to_uname != ''){
      msg = '您对 ' + to_uname + ' 说： ' + msg;
  }
  console.log(msg);
  var token = $("#token").val();
  content = '{"msg":'+msg+',"stats":2,"token":"'+token+'"}';
  // console.log(content);
  //发送信息给服务器
  websocket.send(content);
  var htmlData =   '<div class="msg_item fn-clear">'
                   + '   <div class="uface"><img src="images/hetu.jpg" width="40" height="40"  alt=""/></div>'
             + '   <div class="item_right">'
             + '     <div class="msg own">' + msg + '</div>'
             + '     <div class="name_time">' + from_name + ' · 30秒前</div>'
             + '   </div>'
             + '</div>';
  $("#message_box").append(htmlData);
  $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
  $("#message").val('');
}
})