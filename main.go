package main

import (
	"go-mysql/binlog/packet"
	"go-mysql/binlog/server"
)

//var sequenceId uint8 = 1 //包序列id

func main() {
	//binlog.ComRegisterSlave()
	//return
	//binlog.Binlog()
	//return
	mysql := server.NewMysql("root", "root", "192.168.0.107", "3306")
	//mysql := server.NewMysql("root", "xCl5QUb9ES2YfkvX", "192.168.0.105", "3304")
	//go PingTimer(Ping, mysql, 10*time.Second)

	authPacket := packet.NewHandshake().ReadAuthResult(mysql)
	mysql.Write(authPacket, 1) //发送auth Packet
	//server.InitBinlog(mysql)   //从服务器注册
	//go server.PingTimer(server.Ping, mysql, 30*time.Second)
	for {
		packetData := mysql.Payload()
		//fmt.Println(packetData)
		packet.NewPacket().Handler(packetData, mysql)
		typeSql := server.UserInput()

		mysql.Query(typeSql)
	}
}
