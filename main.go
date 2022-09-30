package main

import (
	"encoding/binary"
	"fmt"
	"net"
)

//var sequenceId uint8 = 1 //包序列id

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
	//decodeString, err := hex.DecodeString("03646566046b656b6509626975626975626975096269756269756269750269640269640c3f000b000000030342000000")
	//if err != nil {
	//	return
	//}
	//fmt.Println(decodeString)
	//
	//obj := NewSelectInfo()
	//obj.ResultSetField(decodeString)
	//
	//return
	mysql := NewMysql("root", "root")
	authPacket := mysql.ReadAuthResult()
	mysql.write(authPacket, 1) //发送auth Packet
	for {
		packetData := mysql.Payload()
		//mysql.SetChart()
		fmt.Println(packetData)
		NewPacket().Handler(packetData, mysql)
		typeSql := UserInput(packetData)

		mysql.Query(typeSql)
	}
}

func (conn *Mysql) write(data []byte, sequenceId uint8) {
	var bytes = []byte{0x00, 0x00, 0x00, 0x00}
	binary.LittleEndian.PutUint16(bytes, uint16(len(data)))
	bytes[3] = sequenceId //包序列id

	_, err := conn.TcpConn.Write(append(bytes, data...)) //发送请求包
	if err != nil {
		panic("write err:" + err.Error())
	}
	return
}