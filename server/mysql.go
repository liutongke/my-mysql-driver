package server

import (
	"encoding/binary"
	"fmt"
	"net"
)

type Mysql struct {
	TcpConn  net.Conn
	Username string
	Password string
}

func NewMysql(username, password, ip, port string) *Mysql {

	conn, err := net.Dial("tcp", fmt.Sprintf("%s:%s", ip, port))
	if err != nil {
		panic("dial failed, err:" + err.Error())
	}

	return &Mysql{TcpConn: conn, Username: username, Password: password}
}

func (conn *Mysql) Payload() []byte {
	packetLen := conn.PayloadLen()

	packetData := make([]byte, packetLen)
	_, err := conn.TcpConn.Read(packetData[:])
	if err != nil {
		panic("recv failed, err:" + err.Error())
	}
	return packetData
}

func (conn *Mysql) PayloadLen() uint32 {
	buf := []byte{0x00, 0x00, 0x00, 0x00}
	n, err := conn.TcpConn.Read(buf[:])
	if err != nil {
		panic("recv failed, err:" + err.Error())
	}
	byteData := buf[:n]

	packetLen := binary.LittleEndian.Uint32(append(byteData[:3], 0x00))
	//fmt.Printf("包长度：%d", packetLen)
	return packetLen
}

func (conn *Mysql) Write(data []byte, sequenceId uint8) {
	var bytes = []byte{0x00, 0x00, 0x00, 0x00}
	binary.LittleEndian.PutUint16(bytes, uint16(len(data)))
	bytes[3] = sequenceId //包序列id

	_, err := conn.TcpConn.Write(append(bytes, data...)) //发送请求包
	if err != nil {
		panic("write err:" + err.Error())
	}
	return
}
