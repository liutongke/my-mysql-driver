package main

import (
	"encoding/binary"
	"encoding/hex"
	"fmt"
	"net"
)

func main() {
	//decodeString, err := hex.DecodeString("4a000000")
	//if err != nil {
	//	return
	//}
	//fmt.Println(decodeString, string(decodeString))
	//payloadLength := decodeString[:3]
	//sequenceId := decodeString[3:]
	//fmt.Printf("payload_length:%d\n", binary.LittleEndian.Uint32(append(payloadLength, 0x00)))
	//fmt.Printf("sequence_id:%d\n", binary.LittleEndian.Uint16(append(sequenceId, 0x00)))
	//
	//return
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
		//fmt.Println(byteData)
		packetLen := binary.LittleEndian.Uint32(byteData)
		//fmt.Printf("包长度：%d", packetLen)
		packetData := make([]byte, packetLen)
		n, err = conn.Read(packetData[:])
		if err != nil {
			fmt.Println("recv failed, err:", err)
			return
		}
		//fmt.Println(packetData)
		hexStringData = hex.EncodeToString(packetData)
		fmt.Println(hexStringData)
		//Handshakes(hexStringData)
		_, err = conn.Write(Handshakes(hexStringData)) //发送请求包
		if err != nil {
			return
		}
		//return
	}
}

//func main() {
//	decodeString, err := hex.DecodeString("3a000001")
//	if err != nil {
//		return
//	}
//	fmt.Println(decodeString)
//	var testBytes = make([]byte, 4)
//	binary.LittleEndian.PutUint16(testBytes, uint16(58))
//	testBytes[3] = 1
//	fmt.Println("int32 to bytes:", testBytes)
//}
