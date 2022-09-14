<?php

namespace App\Controller;


class MysqlDemo
{

//CREATE TABLE `user_info` (
//  `uid` int(11) NOT NULL AUTO_INCREMENT,
//  `nick` varchar(15) DEFAULT NULL,
//  PRIMARY KEY (`uid`)
//) ENGINE=InnoDB AUTO_INCREMENT=1000001 DEFAULT CHARSET=utf8;

    public function getOne(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $uid = $request->post['uid'];

        $database = new \Simps\DB\BaseModel();
        $res = $database->select("user_info", [
            "uid",
            "nick",
        ], [
            "uid" => $uid
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo getOne",
            'tm' => date('Y-m-d H:i:s'),
            "data" => [
                'res' => $res,
                'uid' => $uid,
            ],
        ];
    }

    public function save(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $username = $request->post['username'];
        $database = new \Simps\DB\BaseModel();
        $last_user_id = $database->insert("user_info", [
            "uid" => time(),
            "nick" => $username,
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo save",
            'tm' => date('Y-m-d H:i:s'),
            "data" => [
                'last_user_id' => $last_user_id,
                'username' => $username,
            ],
        ];
    }

    public function del(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $uid = $request->post['uid'];

        $database = new \Simps\DB\BaseModel();

        $res = $database->delete("user_info", [
            "uid" => $uid
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo del",
            'tm' => date('Y-m-d H:i:s'),
            "data" => [
                'res' => $res,
                'uid' => $uid,
            ],
        ];
    }

    public function update(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $uid = $request->post['uid'];
        $username = $request->post['username'];

        $database = new \Simps\DB\BaseModel();

        $res = $database->update("user_info", [
            "nick" => $username
        ], [
            "uid" => $uid
        ]);

        return [
            "code" => 200,
            "msg" => "MysqlDemo update",
            'tm' => date('Y-m-d H:i:s'),
            "data" => [
                'res' => $res,
                'uid' => $uid,
                'username' => $username,
            ],
        ];
    }
}