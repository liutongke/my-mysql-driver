package main

import (
	"fmt"
)

func input(prompt string) string {
	var text string
	fmt.Print(prompt)
	fmt.Scan(&text)
	return text
}

func main() {
	StartWs("ws://192.168.0.105:9500?token=1d643ebcf63ffd2f79b2f755e1060fda")

	var msg = "{\"id\":\"123123123\",\"path\":\"/sendMsgToUser\",\"data\": {\"uid\":999,\"msg\":\"This is a cross server message\"}}"
	for {
		a := input("我的：")

		fmt.Println(a)
		if a == "1" {
			GetWsClient().SendMsg(msg)
		}
	}
}
