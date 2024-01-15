package ConnPhase

import (
	"bytes"
	"encoding/binary"
	"fmt"
	"go-mysql/driver/MySQLPackets"
)

//https://dev.mysql.com/doc/dev/mysql-server/latest/page_protocol_connection_phase_packets_protocol_handshake.html

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

func NewHandshake() *HandshakePacket {
	return &HandshakePacket{}
}
func (h *HandshakePacket) DecodeHandshake(mysql *MySQLPackets.MySQLConnection) (*HandshakePacket, error) {
	_, _, payload, err := MySQLPackets.DecodePacket(mysql.TCPConnection)
	if err != nil {
		return nil, fmt.Errorf("接收数据失败: %v", err)
	}

	return h.handshake(payload)
}

func (h *HandshakePacket) handshake(packet []byte) (*HandshakePacket, error) {
	protocolPacket := []byte{0x00, 0x00}
	copy(protocolPacket, packet[0:1])
	h.Protocol = binary.LittleEndian.Uint16(protocolPacket)

	idx := bytes.IndexByte(packet[1:], 0x00)
	dbVer := packet[1:idx]
	h.Version = string(dbVer)

	idx = idx + 2

	h.ThreadId = binary.LittleEndian.Uint32(packet[idx : idx+4])

	h.Salt1 = packet[idx+4 : idx+4+8]

	h.ServerCapabilities = binary.LittleEndian.Uint16(packet[idx+4+8+1 : idx+4+8+1+2])

	languagePacket := []byte{0x00, 0x00}
	copy(languagePacket, packet[idx+4+8+1+2:idx+4+8+1+2+1])
	h.ServerLanguage = binary.LittleEndian.Uint16(append(languagePacket, 0x00))

	h.ServerStatus = binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1 : idx+4+8+1+2+1+2])

	h.ExtendedServerCapabilities = binary.LittleEndian.Uint16(packet[idx+4+8+1+2+1+2 : idx+4+8+1+2+1+2+2])

	pluginLengthPacket := []byte{0x00, 0x00}
	copy(pluginLengthPacket, packet[idx+4+8+1+2+1+2+2:idx+4+8+1+2+1+2+2+1])
	h.AuthPluginLength = binary.LittleEndian.Uint16(pluginLengthPacket)

	h.Unused = string(packet[idx+4+8+1+2+1+2+2+1 : idx+4+8+1+2+1+2+2+1+10])

	salt2Idx := bytes.IndexByte(packet[idx+4+8+1+2+1+2+2+1+10:], 0x00)

	h.Salt2 = packet[idx+4+8+1+2+1+2+2+1+10 : idx+4+8+1+2+1+2+2+1+10+salt2Idx]

	h.AuthenticationPlugin = string(packet[idx+4+8+1+2+1+2+2+1+10+len(h.Salt2):])

	h.echo()
	return h, nil
}

func (h *HandshakePacket) echo() {
	fmt.Printf("协议版本:%d\n", h.Protocol)
	fmt.Printf("服务器版本:%s\n", h.Version)
	fmt.Printf("线程ID:%d\n", h.ThreadId)
	fmt.Printf("盐值:%s\n", string(h.Salt1))
	fmt.Printf("服务器协议能力:%d\n", h.ServerCapabilities)
	fmt.Printf("服务器语言:%d\n", h.ServerLanguage)
	fmt.Printf("服务器状态:%d\n", h.ServerStatus)
	fmt.Printf("扩展服务器协议能力:%d\n", h.ExtendedServerCapabilities)
	fmt.Printf("插件长度:%d\n", h.AuthPluginLength)
	fmt.Printf("未使用字段:%s\n", h.Unused)
	fmt.Printf("盐值2:%s\n", string(h.Salt2))
	fmt.Printf("身份验证插件:%s\n", h.AuthenticationPlugin)
	fmt.Printf("--------------------------------------------\n\n")
}
