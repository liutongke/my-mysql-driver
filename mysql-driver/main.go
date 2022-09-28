package main

import (
	"encoding/binary"
	"encoding/hex"
	"fmt"
)

func main() {
	decodeString, err := hex.DecodeString("3a000001")
	if err != nil {
		return
	}
	fmt.Println(decodeString)
	var testBytes = make([]byte, 4)
	binary.LittleEndian.PutUint16(testBytes, uint16(58))
	testBytes[3] = 1
	fmt.Println("int32 to bytes:", testBytes)
}
