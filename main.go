package main

import (
	"flag"
	"fmt"
	"go-mysql/driver/packet"
	"go-mysql/driver/server"
)

var (
	host     string
	password string
	user     string
	port     string
) // 定义几个变量，用于接收命令行的参数值

func init() {
	//go run main.go -u root -p root -P 3306 -h 127.0.01
	// &user 就是接收命令行中输入 -u 后面的参数值，其他同理
	flag.StringVar(&host, "h", "", "连接地址")
	flag.StringVar(&password, "p", "", "用户密码")
	flag.StringVar(&user, "u", "", "用户名")
	flag.StringVar(&port, "P", "3306", "连接端口")
	// 解析命令行参数写入注册的flag里
	flag.Parse()
}

//var sequenceId uint8 = 1 //包序列id

func main() {
	if host == "" || password == "" || user == "" {
		fmt.Printf("示例: go run main.go -u root -p root -P 3306 -h 192.168.0.105\n")
		fmt.Printf("连接地址地址必填 \n")
		fmt.Printf("当前请求参数: -u %s -p %s -P %s -h %s\n", user, password, port, host)
		flag.Usage()
		return
	}
	//binlog.ComRegisterSlave()
	//return
	//binlog.Binlog()
	//return
	//mysql := server.NewMysql("root", "root", "192.168.0.107", "3306")
	mysql := server.NewMysql(user, password, host, port)
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
