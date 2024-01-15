package MySQLPackets

func SendMsg(m *MySQLConnection, sendData []byte, sequenceId uint8) {
	_, err := m.TCPConnection.Write(EncodePacket(sendData, sequenceId)) //发送请求包
	if err != nil {
		panic("write err:" + err.Error())
	}
	return
}
