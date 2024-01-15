package MySQLPackets

import (
	"encoding/binary"
	"errors"
	"io"
	"net"
)

// payload_length uint32
// sequence_id uint8
// payload []byte

// DecodePacket 解码客户端的Packet
// https://dev.mysql.com/doc/dev/mysql-server/latest/page_protocol_basic_packets.html#sect_protocol_basic_packets_packet
func DecodePacket(conn net.Conn) (uint32, uint8, []byte, error) {
	// 读取四个字节
	header := make([]byte, 4)
	_, err := conn.Read(header)

	if err == io.EOF {
		// 连接已关闭
		return 0, 0, nil, errors.New("EOF 客户端主动关系连接")
	} else if err != nil {
		// 其他读取错误
		//fmt.Println("其他错误：", err)
		return 0, 0, nil, err
	}

	// 第四个字节转为 uint8
	sequenceID := uint8(header[3])

	// 前三个字节转为 uint32
	payloadLength := binary.LittleEndian.Uint32(append(header[:3], 0x00))

	// 读取剩下的数据
	payload := make([]byte, payloadLength)
	_, err = io.ReadFull(conn, payload)
	if err != nil {
		return 0, 0, nil, err
	}

	return payloadLength, sequenceID, payload, nil
}

// EncodePacket 编码信息发送给客户端
func EncodePacket(payload []byte, sequenceID uint8) []byte {
	// 计算数据包总长度
	length := uint16(len(payload))
	packet := make([]byte, 4+len(payload))

	// 将长度和序列ID写入切片
	binary.LittleEndian.PutUint16(packet, length)
	packet[3] = sequenceID

	// 将数据复制到切片中
	copy(packet[4:], payload)
	return packet
}
