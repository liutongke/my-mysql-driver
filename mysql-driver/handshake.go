package main

import (
	"bytes"
	"encoding/binary"
	"encoding/hex"
	"fmt"
)

type HandshakePacket struct {
	Protocol                   uint16 //版本协议
	Version                    string //版本号
	ThreadId                   uint32 //执行的线程号
	Salt1                      []byte //用于后期加密的salt1
	ServerCapabilities         uint16 //通信的协议
	ServerLanguage             uint16 //服务器语言
	ServerStatus               uint16 //服务器状态
	ExtendedServerCapabilities uint16
	AuthPluginLength           uint16
	Unused                     string //保留字符串
	Salt2                      []byte //用于后期加密的salt2
	//https://dev.mysql.com/doc/internals/en/authentication-method.html
	AuthenticationPlugin string //身份验证方法
}

func Handshakes(hexStringData string) []byte {
	HandshakePacket := &HandshakePacket{}
	packet, _ := hex.DecodeString(hexStringData)

	protocolPacket := []byte{0x00, 0x00}
	copy(protocolPacket, packet[0:1])
	fmt.Printf("protocolVersion:%d\n", binary.LittleEndian.Uint16(protocolPacket))
	HandshakePacket.Protocol = binary.LittleEndian.Uint16(protocolPacket)

	idx := bytes.IndexByte(packet[1:], 0x00)
	dbVer := packet[1:idx]

	fmt.Printf("serverVersion:%s\n", string(dbVer))
	HandshakePacket.Version = string(dbVer)
	idx = idx + 2

	fmt.Printf("threadId:%d\n", binary.LittleEndian.Uint32(packet[idx:idx+4]))
	HandshakePacket.ThreadId = binary.LittleEndian.Uint32(packet[idx : idx+4])

	fmt.Printf("salt:%s\n", string(packet[idx+4:idx+4+8]))
	saltByte := packet[idx+4 : idx+4+8]
	HandshakePacket.Salt1 = saltByte
	//fmt.Println("salt byte:", hex.EncodeToString(packet[idx+4:idx+4+8]))
	fmt.Printf("serverCapabilities:%d\n", binary.LittleEndian.Uint16(packet[idx+4+8+1:idx+4+8+1+2]))
	HandshakePacket.ServerCapabilities = binary.LittleEndian.Uint16(packet[idx+4+8+1 : idx+4+8+1+2])

	languagePacket := []byte{0x00, 0x00}
	copy(languagePacket, packet[idx+4+8+1+2:idx+4+8+1+2+1])
	fmt.Printf("server Language:%d\n", binary.LittleEndian.Uint16(append(languagePacket, 0x00)))
	HandshakePacket.ServerLanguage = binary.LittleEndian.Uint16(append(languagePacket, 0x00))

	fmt.Printf("server Status:%d\n", binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1:idx+4+8+1+2+1+2]))
	HandshakePacket.ServerStatus = binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1 : idx+4+8+1+2+1+2])

	fmt.Printf("Extended Server Capabilities:%d\n", binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1+2:idx+4+8+1+2+1+2+2]))
	HandshakePacket.ExtendedServerCapabilities = binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1+2 : idx+4+8+1+2+1+2+2])

	pluginLengthPacket := []byte{0x00, 0x00}
	copy(pluginLengthPacket, packet[idx+4+8+1+2+1+2+2:idx+4+8+1+2+1+2+2+1])
	fmt.Printf("plugin Length:%d\n", binary.LittleEndian.Uint16(pluginLengthPacket))
	HandshakePacket.AuthPluginLength = binary.LittleEndian.Uint16(pluginLengthPacket)

	fmt.Printf("Unused:%s\n", string(packet[idx+4+8+1+2+1+2+2+1:idx+4+8+1+2+1+2+2+1+10]))
	HandshakePacket.Unused = string(packet[idx+4+8+1+2+1+2+2+1 : idx+4+8+1+2+1+2+2+1+10])

	salt2Idx := bytes.IndexByte(packet[idx+4+8+1+2+1+2+2+1+10:], 0x00)

	salt2 := packet[idx+4+8+1+2+1+2+2+1+10 : idx+4+8+1+2+1+2+2+1+10+salt2Idx]
	HandshakePacket.Salt2 = salt2
	fmt.Printf("salt2:%s\n", string(salt2))
	fmt.Printf("Authentication Plugin:%s\n", string(packet[idx+4+8+1+2+1+2+2+1+10+len(salt2):]))
	HandshakePacket.AuthenticationPlugin = string(packet[idx+4+8+1+2+1+2+2+1+10+len(salt2):])
	fmt.Println(HandshakePacket)
	return GetAuthPacket(append(HandshakePacket.Salt1, HandshakePacket.Salt2...))
	//return GetAuthPacket(append(saltByte, salt2...)) //生成请求包
}
