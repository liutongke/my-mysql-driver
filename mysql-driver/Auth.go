package main

import (
	"crypto/sha1"
	"encoding/binary"
	"encoding/hex"
	"fmt"
)

func test() {
	//password := "secret"
	//AllowNativePasswords = false
	//
	//authData := []byte{70, 114, 92, 94, 1, 38, 11, 116, 63, 114, 23, 101, 126,
	//	103, 26, 95, 81, 17, 24, 21}
	//plugin := "mysql_native_password"

	//unused()
	//encodePass("root")
	//bytes, _ := hex.DecodeString("4b9d5c36cafbf59426ffd1180364a69927539695")
	//fmt.Println(bytes)
	////两个盐值相加
	//decodeString1, _ := hex.DecodeString("4b79225e1f0a2915")
	//fmt.Println(decodeString1, len(decodeString1))
	//decodeString2, _ := hex.DecodeString("2a657679524b0d5e6c254b54")
	//fmt.Println(decodeString2, len(decodeString2))
	//
	//arr3 := append(decodeString1, decodeString2...)
	//fmt.Println(scramblePassword(arr3, "root"))
}
func GetAuthPacket(scramble []byte) []byte {
	arr := append(clientCapabilities(), extendedClientCapabilities()...)
	arr1 := append(arr, MAXPacket()...)
	arr2 := append(arr1, CharsetSet()...)
	arr3 := append(arr2, unused()...)
	str1 := hex.EncodeToString(arr3)
	userName := string(encodeUserName())
	pwd := hex.EncodeToString(encodePass(scramble, "root"))
	sprintf := fmt.Sprintf("%s%s%s", str1, userName, pwd)
	fmt.Println(sprintf, len(sprintf))
	decodeString, err := hex.DecodeString(sprintf)
	if err != nil {
		return nil
	}

	var testBytes = make([]byte, 4)
	binary.LittleEndian.PutUint16(testBytes, uint16(len(decodeString)))
	testBytes[3] = 1

	return append(testBytes, decodeString...)
}

// 0xa685 协议协商
func clientCapabilities() []byte {
	decodeString, err := hex.DecodeString("85a6")
	if err != nil {
		return nil
	}
	return decodeString
}

// 0x0003 扩展的协议
func extendedClientCapabilities() []byte {
	decodeString, err := hex.DecodeString("0300")
	if err != nil {
		return nil
	}
	return decodeString
}

// 1073741824 消息最长长度
func MAXPacket() []byte {
	decodeString, err := hex.DecodeString("00000040")
	if err != nil {
		return nil
	}
	return decodeString
}

// 字符编码
func CharsetSet() []byte {
	decodeString, err := hex.DecodeString("21")
	if err != nil {
		return nil
	}
	return decodeString
}

// 保留字节，长度23
func unused() []byte {
	data, _ := hex.DecodeString("0000000000000000000000000000000000000000000000")
	return data
}
func encodeUserName() []byte {
	username := []byte("root")
	username = append(username, 00)
	hexUsername := hex.EncodeToString(username)
	return []byte(hexUsername + "14")
}

func encodePass(scramble []byte, pwd string) []byte {
	//bytes, _ := hex.DecodeString("4b9d5c36cafbf59426ffd1180364a69927539695")
	//fmt.Println(bytes)
	//两个盐值相加
	//decodeString1, _ := hex.DecodeString("4b79225e1f0a2915")
	//fmt.Println(decodeString1, len(decodeString1))
	//decodeString2, _ := hex.DecodeString("2a657679524b0d5e6c254b54")
	//fmt.Println(decodeString2, len(decodeString2))

	//arr3 := append(decodeString1, decodeString2...)
	pass := scramblePassword(scramble, pwd)
	//fmt.Println(pass, hex.EncodeToString(pass))
	return pass
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
