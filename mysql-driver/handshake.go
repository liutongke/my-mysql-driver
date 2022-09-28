package main

import (
	"encoding/binary"
	"encoding/hex"
	"fmt"
	"net"
)

var SaltPacket []byte

func main() {
	conn, err := net.Dial("tcp", "192.168.0.107:3306")
	if err != nil {
		fmt.Printf("dial failed, err:%v\n", err)
		return
	}
	var hexStringData string
	for {
		buf := []byte{00, 00, 00, 00}
		n, err := conn.Read(buf[:])
		if err != nil {
			fmt.Println("recv failed, err:", err)
			return
		}
		byteData := buf[:n]
		fmt.Println(byteData)
		packetLen := binary.LittleEndian.Uint32(byteData)
		fmt.Printf("包长度：%d", packetLen)
		packetData := make([]byte, packetLen)
		n, err = conn.Read(packetData[:])
		if err != nil {
			fmt.Println("recv failed, err:", err)
			return
		}
		fmt.Println(packetData)
		hexStringData = hex.EncodeToString(packetData)
		fmt.Println(hexStringData)
		_, err = conn.Write(Handshakes(hexStringData)) //发送请求包
		if err != nil {
			return
		}
	}
}

func Handshakes(hexStringData string) []byte {
	packet, _ := hex.DecodeString(hexStringData)

	protocolPacket := []byte{00, 00}
	copy(protocolPacket, packet[0:1])
	protocolVersion = binary.LittleEndian.Uint16(protocolPacket)
	fmt.Printf("protocolVersion:%d\n", protocolVersion)

	var dbVer []byte
	var idx int
	for k, item := range packet[1:] {
		dbVer = append(dbVer, item)
		if item == 0 {
			idx = k
			goto next
		}
	}

next:
	fmt.Printf("serverVersion:%s\n", string(dbVer))
	idx = idx + 2

	fmt.Printf("threadId:%d\n", binary.LittleEndian.Uint32(packet[idx:idx+4]))

	fmt.Printf("salt:%s\n", string(packet[idx+4:idx+4+8]))
	saltByte := packet[idx+4 : idx+4+8]
	//fmt.Println("salt byte:", hex.EncodeToString(packet[idx+4:idx+4+8]))
	fmt.Printf("serverCapabilities:%d\n", binary.LittleEndian.Uint16(packet[idx+4+8+1:idx+4+8+1+2]))

	languagePacket := []byte{00, 00}
	copy(languagePacket, packet[idx+4+8+1+2:idx+4+8+1+2+1])
	fmt.Printf("server Language:%d\n", binary.LittleEndian.Uint16(append(languagePacket, 00)))

	fmt.Printf("server Status:%d\n", binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1:idx+4+8+1+2+1+2]))

	fmt.Printf("Extended Server Capabilities:%d\n", binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1+2:idx+4+8+1+2+1+2+2]))

	pluginLengthPacket := []byte{00, 00}
	copy(pluginLengthPacket, packet[idx+4+8+1+2+1+2+2:idx+4+8+1+2+1+2+2+1])
	fmt.Printf("plugin Length:%d\n", binary.LittleEndian.Uint16(pluginLengthPacket))

	fmt.Printf("Unused:%s\n", string(packet[idx+4+8+1+2+1+2+2+1:idx+4+8+1+2+1+2+2+1+10]))

	var salt2 []byte
	for _, saltIem := range packet[idx+4+8+1+2+1+2+2+1+10:] {
		if saltIem == 0 {
			goto salt2jump
		}
		salt2 = append(salt2, saltIem)
	}

salt2jump:
	fmt.Printf("salt2:%s\n", string(salt2))
	fmt.Printf("Authentication Plugin:%s\n", string(packet[idx+4+8+1+2+1+2+2+1+10+len(salt2):]))

	pass := scramblePassword(append(saltByte, salt2...), "root") //密码
	return GetAuthPacket(pass)                                   //生成请求包
}

var (
	protocolVersion    uint16 //版本协议
	serverVersion      string //版本号
	threadId           uint32 //执行的线程号
	public             string //用于后期加密的salt1
	serverCapabilities uint16 //通信的协议
	serverCharsetIndex uint16 //编码格式
	serverStatus       uint16 //服务端的状态
	restOfScrambleBuff string //这个其实就是seed2
)
