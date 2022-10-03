package server

import (
	"encoding/binary"
	"fmt"
	"github.com/google/uuid"
)

func InitBinlog(mysql *Mysql) {
	mysql.ShowMaster()
	mysql.SetChecksum()
	mysql.SetSlaveUuid()
	mysql.Show()
	//mysql.RegisterSlave()
	mysql.Write(ComRegisterSlave(), 0) //注册从服务器
	mysql.Write(Binlog(), 0)           //注册dump
}

// Binlog 注册Binlog dump让主服务器推送binlog https://dev.mysql.com/doc/internals/en/com-binlog-dump.html
func Binlog() []byte {
	//1              [12] COM_BINLOG_DUMP
	//4              binlog-pos
	//2              flags
	//4              server-id
	//string[EOF]    binlog-filename
	//head := make([]byte, 4) //头
	var b = make([]byte, 4)
	binary.LittleEndian.PutUint32(b, uint32(2344)) //binlog-pos
	var bytes = []byte{0x12}
	bytes = append(bytes, b...) //COM_BINLOG_DUMP

	bytes = append(bytes, 0x00, 0x00)

	var serverId = make([]byte, 4)
	binary.LittleEndian.PutUint32(serverId, uint32(4)) //server-id
	bytes = append(bytes, serverId...)

	bytes = append(bytes, []byte("mysql-bin.000001")...) //binlog-filename
	return bytes
}

// ComRegisterSlave 注册从服务器 https://mariadb.com/kb/en/com_register_slave/
func ComRegisterSlave() []byte {
	//uint<1> command (COM_REGISTER_SLAVE = 0x15)
	//uint<4> Slave server_id
	//uint<1> Slave hostname length
	//string<n> Hostname
	//uint<1> Slave username len
	//string<n> Username
	//uint<1> Slave password len
	//string<n> Slave password
	//uint<2> Slave connection port
	//uint<4> Replication rank
	//uint<4> Master server id
	var bytes = []byte{0x15} //command

	var serverId = make([]byte, 4)
	binary.LittleEndian.PutUint32(serverId, uint32(4)) //server-id
	bytes = append(bytes, serverId...)
	bytes = append(bytes, []byte{0x00, 0x00, 0x00}...) //Hostname Username password可不设置

	SlaveConnectionPort := []byte{0x00, 0x00}
	binary.LittleEndian.PutUint16(SlaveConnectionPort, uint16(3306)) //Slave connection port
	bytes = append(bytes, SlaveConnectionPort...)
	bytes = append(bytes, []byte{0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00}...) //Replication rank  Master server id可不设置
	return bytes
}

// SET @master_heartbeat_period= 30000001024
// SET @master_binlog_checksum= @@global.binlog_checksum
// SET @slave_uuid= '62c16f3c-41ab-11ed-8d8e-0242ac110002'
//163	5.714582	192.168.0.105	192.168.0.107	MySQL	76	Request Register Slave

func (conn *Mysql) ShowMaster() {
	var bytes = []byte{0x03}
	conn.Write(append(bytes, []byte("show master status;")...), 0)
}
func (conn *Mysql) Show() {
	var bytes = []byte{0x03}
	conn.Write(append(bytes, []byte("show global variables like 'binlog_checksum';")...), 0)
}

func (conn *Mysql) SetSlaveUuid() {
	var bytes = []byte{0x03}
	uuid := uuid.New().String()
	fmt.Printf("MySQL slave uuid:%s\n", uuid)

	conn.Write(append(bytes, []byte(fmt.Sprintf("SET @slave_uuid= '%s';", uuid))...), 0)
}

func (conn *Mysql) SetChecksum() {
	var bytes = []byte{0x03}
	conn.Write(append(bytes, []byte("set @master_binlog_checksum= @@global.binlog_checksum;")...), 0)
}
