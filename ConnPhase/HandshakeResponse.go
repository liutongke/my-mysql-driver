package ConnPhase

//https://dev.mysql.com/doc/dev/mysql-server/latest/page_protocol_connection_phase_packets_protocol_handshake_response.html
import (
	"crypto/sha1"
	"encoding/hex"
	"fmt"
)

// Auth Packet

// GenerateHandshakeResponse 生成HandshakeResponse41响应
func GenerateHandshakeResponse(h *HandshakePacket, username, password string) []byte {

	scramble := append(h.Salt1, h.Salt2...)
	userName := string(encodeUserName(username))

	str := hex.EncodeToString(append(append(append(append(clientCapabilities(), extendedClientCapabilities()...), mAXPacket()...), charsetSet()...), unused()...))

	sprintf := fmt.Sprintf("%s%s%s", str, userName, encodePass(scramble, password))

	decodeString, err := hex.DecodeString(sprintf)
	if err != nil {
		return nil
	}
	return decodeString
}

// 0xa685 协议协商
func clientCapabilities() []byte {
	decodeString, err := hex.DecodeString("85a6")
	if err != nil {
		panic("auth err:" + err.Error())
	}
	return decodeString
}

// 0x0003 扩展的协议
func extendedClientCapabilities() []byte {
	decodeString, err := hex.DecodeString("0300")
	if err != nil {
		panic("auth err:" + err.Error())
	}
	return decodeString
}

// 1073741824 消息最长长度
func mAXPacket() []byte {
	decodeString, err := hex.DecodeString("00000040")
	if err != nil {
		panic("auth err:" + err.Error())
	}
	return decodeString
}

// 字符编码
func charsetSet() []byte {
	decodeString, err := hex.DecodeString("21")
	if err != nil {
		panic("auth err:" + err.Error())
	}
	return decodeString
}

// 保留字节，长度23
func unused() []byte {
	decodeString, err := hex.DecodeString("0000000000000000000000000000000000000000000000")
	if err != nil {
		panic("auth err:" + err.Error())
	}
	return decodeString
}

func encodeUserName(username string) []byte {
	hexUsername := hex.EncodeToString(append([]byte(username), 0x00))
	return []byte(hexUsername + "14")
}

func encodePass(scramble []byte, pwd string) (pass string) {
	pass = hex.EncodeToString(scramblePassword(scramble, pwd))
	return
}

// Hash password using 4.1+ method (SHA1)
func scramblePassword(scramble []byte, password string) []byte {
	if len(password) == 0 {
		return nil
	}

	// stage1Hash = SHA1(password)
	crypt := sha1.New()
	crypt.Write([]byte(password))
	stage1 := crypt.Sum(nil)

	// scrambleHash = SHA1(scramble + SHA1(stage1Hash))
	// inner Hash
	crypt.Reset()
	crypt.Write(stage1)
	hash := crypt.Sum(nil)

	// outer Hash
	crypt.Reset()
	crypt.Write(scramble)
	crypt.Write(hash)
	scramble = crypt.Sum(nil)

	// token = scrambleHash XOR stage1Hash
	for i := range scramble {
		scramble[i] ^= stage1[i]
	}
	return scramble
}
