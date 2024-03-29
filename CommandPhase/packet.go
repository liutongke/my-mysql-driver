package CommandPhase

import (
	"encoding/binary"
	"encoding/hex"
	"fmt"
	"go-mysql/driver/MySQLPackets"
)

type Packet struct {
}

func NewPacket() *Packet {
	return &Packet{}
}

func (p *Packet) Handler(data []byte, mysql *MySQLPackets.MySQLConnection) {
	packetType := hex.EncodeToString(data[:1])

	if packetType == "00" { //成功报文
		sucMsg := p.success(data[1:])
		//fmt.Printf("\033[1;32;40m%s\033[0m\n", sucMsg.Row, sucMsg.LastInsertId)
		fmt.Printf("\u001B[1;32;40m[Success] 受影响的行:%d - 自增id:%d\u001B[0m\n", sucMsg.Row, sucMsg.LastInsertId)
	} else if packetType == "ff" { //失败报文
		errMsg := p.error(data[1:])
		fmt.Printf("\u001B[1;31;40m[Err] %d - %s\u001B[0m\n", errMsg.ErrorCode, errMsg.ErrorMessage)
	} else {
		//fmt.Printf("field Num:%d\n", binary.LittleEndian.Uint16(append(data, 00)))
		//本次查询总共总共多少字段
		fieldNum := binary.LittleEndian.Uint16(append(data, 00))

		fieldInfo := &FieldsInfo{
			FieldNum:  fieldNum,
			FieldMap:  nil,
			FieldList: nil,
		}
		//读取字段
		for i := uint16(0); i < fieldNum; i++ {
			obj := NewSelectInfo()
			//packetData := mysql.Payload()
			_, _, packetData, _ := MySQLPackets.DecodePacket(mysql.TCPConnection)
			obj.ResultSetField(packetData, fieldInfo)
		}
		_, _, payload, _ := MySQLPackets.DecodePacket(mysql.TCPConnection)
		NewEof().Eof(payload)
		//NewEof().Eof(mysql.Payload())
		//fmt.Println(fieldInfo.FieldMap)
		//读取字段的值
		rowObj := NewRowPacket()
		rowObj.RowPacket(mysql)

		var list []map[string]string
		for k, v := range rowObj.RowList {
			fields := make(map[string]string)

			n := uint16(k) % fieldNum

			fields[fieldInfo.FieldMap[n]] = v
			list = append(list, fields)
		}

		fmt.Println("\u001B[1;32;40m+--------------------------+\u001B[0m")
		for _, listData := range list {
			for fieldName, fieldVal := range listData {
				fmt.Printf("%s : %s\n", fieldName, fieldVal)
			}
		}
		fmt.Println("\u001B[1;32;40m+--------------------------+\u001B[0m")
	}
}

type FieldsInfo struct {
	FieldNum  uint16 //几个字段
	FieldMap  []string
	FieldList []map[string]string
}

func NewRowPacket() *Row {
	return &Row{RowList: nil}
}

type Row struct {
	RowList []string
}

func (r *Row) RowPacket(mysql *MySQLPackets.MySQLConnection) {
	for {
		_, _, packetData, _ := MySQLPackets.DecodePacket(mysql.TCPConnection)
		//packetData := mysql.Payload()

		packetType := hex.EncodeToString(packetData[:1])
		if packetType == "fe" { //需要做一个eof判断是否结束
			return
		}

		rowIdx = 0 //初始化一下
		r.row(packetData)
	}
}

var rowIdx uint16

func (r *Row) row(packetData []byte) {

	if int(rowIdx) >= len(packetData) {
		return
	}

	lengthBytes := make([]byte, 2)
	copy(lengthBytes, packetData[rowIdx:rowIdx+1])
	length := binary.LittleEndian.Uint16(lengthBytes)

	if int(rowIdx+1+length) > len(packetData) {
		return
	}

	text := packetData[rowIdx+1 : rowIdx+1+length]

	rowIdx = rowIdx + 1 + length

	r.RowList = append(r.RowList, fmt.Sprintf("%s", text))
	r.row(packetData)
}

func NewSelectInfo() *SelectInfo {
	return &SelectInfo{
		ResultHeader: &ResultHeader{},
		ResultField:  &ResultField{},
	}
}

type SelectInfo struct {
	ResultHeader *ResultHeader
	ResultField  *ResultField
}

type ResultHeader struct {
	NumberOfFields uint64 //字段数量
}

func (s *SelectInfo) ResultSetHeader(data []byte) {
	header := make([]byte, 8)
	copy(header, data)
	s.ResultHeader.NumberOfFields = binary.LittleEndian.Uint64(header)
	//fmt.Printf("ResultSetHeader:%d", binary.LittleEndian.Uint64(header))
}

type ResultField struct {
	DefLen       uint16
	Def          string
	PreFixLen    uint16
	Database     string
	TableLen     uint16
	TableName    string //操作的虚拟表名
	OrgTableLen  uint16
	OrgTableName string //操作的物理表名
	FieldLen     uint16
	FieldName    string //操作的虚拟字段名
	OrgFieldLen  uint16
	OrgFieldName string //操作的物理字段名
}

func (s *SelectInfo) ResultSetField(data []byte, fieldsInfo *FieldsInfo) {
	var idx uint16 = 1
	defLen := make([]byte, 2)
	copy(defLen, data[:idx])

	s.ResultField.DefLen = binary.LittleEndian.Uint16(defLen)

	idx = idx + s.ResultField.DefLen
	s.ResultField.Def = string(data[1:idx])

	//------------------------------------------
	preFixLen := make([]byte, 2)

	copy(preFixLen, data[idx:idx+1])
	s.ResultField.PreFixLen = binary.LittleEndian.Uint16(preFixLen)
	idx++
	s.ResultField.Database = string(data[idx : idx+s.ResultField.PreFixLen])

	//------------------------------------------
	tableLen := make([]byte, 2)
	idx = idx + s.ResultField.PreFixLen
	copy(tableLen, data[idx:idx+1])

	s.ResultField.TableLen = binary.LittleEndian.Uint16(tableLen)
	idx++
	s.ResultField.TableName = string(data[idx : idx+s.ResultField.TableLen])

	//------------------------------------------
	orgTableLen := make([]byte, 2)
	idx = idx + s.ResultField.TableLen
	copy(orgTableLen, data[idx:idx+1])

	s.ResultField.OrgTableLen = binary.LittleEndian.Uint16(orgTableLen)
	idx++
	s.ResultField.OrgTableName = string(data[idx : idx+s.ResultField.OrgTableLen])

	//------------------------------------------
	fieldLen := make([]byte, 2)
	idx = idx + s.ResultField.OrgTableLen
	copy(fieldLen, data[idx:idx+1])

	s.ResultField.FieldLen = binary.LittleEndian.Uint16(fieldLen)
	idx++
	s.ResultField.FieldName = string(data[idx : idx+s.ResultField.FieldLen])

	//------------------------------------------
	orgFieldLen := make([]byte, 2)
	idx = idx + s.ResultField.FieldLen
	copy(orgFieldLen, data[idx:idx+1])

	s.ResultField.OrgFieldLen = binary.LittleEndian.Uint16(orgFieldLen)
	idx++
	s.ResultField.OrgFieldName = string(data[idx : idx+s.ResultField.OrgFieldLen])

	fieldsInfo.FieldMap = append(fieldsInfo.FieldMap, s.ResultField.FieldName)
	//fmt.Println(s.ResultField.OrgFieldName)
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

type Eof struct {
	ResponseCode []byte
	EofMarker    uint16
	ServerStatus uint16
	Payload      uint16
}

func NewEof() *Eof {
	return &Eof{}
}
func (e *Eof) Eof(packet []byte) {
	e.ResponseCode = packet[0:1]

	eofMarker := make([]byte, 2)
	copy(eofMarker, e.ResponseCode)
	e.EofMarker = binary.LittleEndian.Uint16(eofMarker)

	e.ServerStatus = binary.LittleEndian.Uint16(packet[1:3])

	e.Payload = binary.LittleEndian.Uint16(packet[3:5])
	//fmt.Println(e)
}
