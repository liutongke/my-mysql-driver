package main

import (
	"bytes"
	"encoding/binary"
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

func (conn *Mysql) ReadAuthResult() []byte {
	packetLen := conn.PayloadLen()
	packetData := make([]byte, packetLen)
	_, err := conn.TcpConn.Read(packetData[:])
	if err != nil {
		panic("recv failed, err:" + err.Error())
	}

	return NewHandshake().Handshake(packetData, conn.Username, conn.Password)
}

func NewHandshake() *HandshakePacket {
	return &HandshakePacket{}
}

func (HandshakePacket *HandshakePacket) Handshake(packet []byte, username, pwd string) []byte {

	protocolPacket := []byte{0x00, 0x00}
	copy(protocolPacket, packet[0:1])
	HandshakePacket.Protocol = binary.LittleEndian.Uint16(protocolPacket)

	idx := bytes.IndexByte(packet[1:], 0x00)
	dbVer := packet[1:idx]
	HandshakePacket.Version = string(dbVer)

	idx = idx + 2

	HandshakePacket.ThreadId = binary.LittleEndian.Uint32(packet[idx : idx+4])

	HandshakePacket.Salt1 = packet[idx+4 : idx+4+8]

	HandshakePacket.ServerCapabilities = binary.LittleEndian.Uint16(packet[idx+4+8+1 : idx+4+8+1+2])

	languagePacket := []byte{0x00, 0x00}
	copy(languagePacket, packet[idx+4+8+1+2:idx+4+8+1+2+1])
	HandshakePacket.ServerLanguage = binary.LittleEndian.Uint16(append(languagePacket, 0x00))

	HandshakePacket.ServerStatus = binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1 : idx+4+8+1+2+1+2])

	HandshakePacket.ExtendedServerCapabilities = binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1+2 : idx+4+8+1+2+1+2+2])

	pluginLengthPacket := []byte{0x00, 0x00}
	copy(pluginLengthPacket, packet[idx+4+8+1+2+1+2+2:idx+4+8+1+2+1+2+2+1])
	HandshakePacket.AuthPluginLength = binary.LittleEndian.Uint16(pluginLengthPacket)

	HandshakePacket.Unused = string(packet[idx+4+8+1+2+1+2+2+1 : idx+4+8+1+2+1+2+2+1+10])

	salt2Idx := bytes.IndexByte(packet[idx+4+8+1+2+1+2+2+1+10:], 0x00)

	HandshakePacket.Salt2 = packet[idx+4+8+1+2+1+2+2+1+10 : idx+4+8+1+2+1+2+2+1+10+salt2Idx]

	HandshakePacket.AuthenticationPlugin = string(packet[idx+4+8+1+2+1+2+2+1+10+len(HandshakePacket.Salt2):])
	HandshakePacket.echo()
	return GetAuthPacket(append(HandshakePacket.Salt1, HandshakePacket.Salt2...), username, pwd)
	//return GetAuthPacket(append(saltByte, salt2...)) //生成请求包
}

func (HandshakePacket *HandshakePacket) echo() {
	fmt.Printf("protocolVersion:%d\n", HandshakePacket.Protocol)
	fmt.Printf("serverVersion:%s\n", HandshakePacket.Version)
	fmt.Printf("threadId:%d\n", HandshakePacket.ThreadId)
	fmt.Printf("salt:%s\n", string(HandshakePacket.Salt1))
	fmt.Printf("serverCapabilities:%d\n", HandshakePacket.ServerCapabilities)
	fmt.Printf("server Language:%d\n", HandshakePacket.ServerLanguage)
	fmt.Printf("server Status:%d\n", HandshakePacket.ServerStatus)
	fmt.Printf("Extended Server Capabilities:%d\n", HandshakePacket.ExtendedServerCapabilities)
	fmt.Printf("plugin Length:%d\n", HandshakePacket.AuthPluginLength)
	fmt.Printf("Unused:%s\n", HandshakePacket.Unused)
	fmt.Printf("salt2:%s\n", string(HandshakePacket.Salt2))
	fmt.Printf("Authentication Plugin:%s\n", HandshakePacket.AuthenticationPlugin)
}
