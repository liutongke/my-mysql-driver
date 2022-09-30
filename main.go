package main

import (
	"go-mysql/binlog/packet"
	"go-mysql/binlog/server"
)

//var sequenceId uint8 = 1 //包序列id

func main() {
	mysql := server.NewMysql("root", "root", "192.168.0.107", "3306")
	//go PingTimer(Ping, mysql, 10*time.Second)

	authPacket := packet.NewHandshake().ReadAuthResult(mysql)
	mysql.Write(authPacket, 1) //发送auth Packet
	for {
		packetData := mysql.Payload()
		packet.NewPacket().Handler(packetData, mysql)
		typeSql := server.UserInput()

		mysql.Query(typeSql)
	}
}
