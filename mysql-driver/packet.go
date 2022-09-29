package main

import (
	"encoding/binary"
	"encoding/hex"
	"fmt"
)

type Packet struct {
	PacketType int
}

func NewPacket() *Packet {
	return &Packet{}
}

func (p *Packet) Handler(data []byte) {
	packetType := hex.EncodeToString(data[:1])

	if packetType == "00" { //成功报文
		sucMsg := p.success(data[1:])
		fmt.Printf("[Success] 受影响的行:%d - 自增id:%d\n", sucMsg.Row, sucMsg.LastInsertId)
	}

	if packetType == "ff" { //失败报文
		errMsg := p.error(data[1:])
		fmt.Printf("[Err] %d - %s\n", errMsg.ErrorCode, errMsg.ErrorMessage)
	}
}

type Success struct {
	Row          uint64
	LastInsertId uint64
	ServerStatus uint16
	Warnings     uint16
	Message      string
}

func (p *Packet) success(packet []byte) *Success {
	sucPacket := &Success{}

	row := make([]byte, 8)
	copy(row, packet[0:1])
	sucPacket.Row = binary.LittleEndian.Uint64(row)

	LastInsertId := make([]byte, 8)
	copy(LastInsertId, packet[1:2])
	sucPacket.LastInsertId = binary.LittleEndian.Uint64(LastInsertId)

	sucPacket.ServerStatus = binary.LittleEndian.Uint16(packet[2:4])

	sucPacket.Warnings = binary.LittleEndian.Uint16(packet[4:6])

	sucPacket.Message = string(packet[6:])

	return sucPacket
}

type Error struct {
	ErrorCode         uint16 //该错误的相应错误代码
	IdentificationBit []byte //标识位	SQL执行状态标识位，用’#’进行标识
	SqlState          string //执行状态	SQL的具体执行状态
	ErrorMessage      string //错误信息	具体的错误信息
}

func (p *Packet) error(packet []byte) *Error {
	errorPacket := &Error{}

	errorPacket.ErrorCode = binary.LittleEndian.Uint16(packet[0:2])

	errorPacket.IdentificationBit = packet[2:3]

	errorPacket.SqlState = string(packet[3:8])

	errorPacket.ErrorMessage = string(packet[8:])
	return errorPacket
}
