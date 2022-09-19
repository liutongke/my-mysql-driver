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
	welcome()
	StartWs("ws://192.168.0.105:9500?token=3b6894772cd40711db90f911df62ae40")

	var msg = "{\"id\":\"123123123\",\"path\":\"/sendMsgToUser\",\"data\": {\"uid\":999,\"msg\":\"This is a cross server message\"}}"
	for {
		a := input("我的：")

		fmt.Println(a)
		if a == "1" {
			GetWsClient().SendMsg(msg)
		}
	}
}

func welcome() {
	fmt.Println("1 - 列出在线用户")
	fmt.Println("2 - 选择聊天用户(例：2-用户id)")
}
