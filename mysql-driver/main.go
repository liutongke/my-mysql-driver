package main

import (
	"encoding/binary"
	"fmt"
	"net"
	"strings"
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
	mysql := NewMysql("root", "root")
	authPacket := mysql.ReadAuthResult()
	mysql.write(authPacket, 1) //发送auth Packet
	mysql.setChart()
	for {
		packetLen := mysql.PayloadLen()

		packetData := make([]byte, packetLen)
		_, err := mysql.TcpConn.Read(packetData[:])
		if err != nil {
			panic("recv failed, err:" + err.Error())
		}
		fmt.Println(packetData)

		typeSql := userInput()
		mysql.query(typeSql)
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

func (conn *Mysql) setChart() []byte {
	var bytes = []byte{0x03}
	conn.write(append(bytes, []byte("SET NAMES utf8;")...), 0)
	return nil
}

func (conn *Mysql) query(typeSql *sql) []byte {
	var bytes = []byte{typeSql.head}
	conn.write(append(bytes, []byte(typeSql.Sql)...), 0)
	return nil
}

func userInput() *sql {
	str := InputCmd()
	a := input(str)
	return a
}

type sql struct {
	Sql  string
	head uint8
}

func input(prompt string) *sql {
	list := strings.Split(prompt, " ")
	head := GetOrder(list[0])
	return &sql{
		Sql:  prompt,
		head: head,
	}
}
