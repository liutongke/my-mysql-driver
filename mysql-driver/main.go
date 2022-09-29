package main

import (
	"fmt"
	"net"
)

var sequenceId uint8 = 1 //包序列id

type Mysql struct {
	TcpConn  net.Conn
	Username string
	Password string
}

func NewMysql(username, password string) *Mysql {
	conn, err := net.Dial("tcp", "192.168.0.107:3306")
	if err != nil {
		panic("dial failed, err:" + err.Error())
	}

	return &Mysql{TcpConn: conn, Username: username, Password: password}
}

func main() {
	mysql := NewMysql("root", "root")
	authPacket := mysql.ReadAuthResult()
	mysql.write(authPacket) //发送auth Packet

	for {
		packetLen := mysql.PayloadLen()

		packetData := make([]byte, packetLen)
		_, err := mysql.TcpConn.Read(packetData[:])
		if err != nil {
			panic("recv failed, err:" + err.Error())
		}
		fmt.Println(packetData)
		return
	}
}

func (conn *Mysql) write(data []byte) {
	_, err := conn.TcpConn.Write(data) //发送请求包
	if err != nil {
		panic("write err:" + err.Error())
	}
	return
}
