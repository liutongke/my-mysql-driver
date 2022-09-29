package main

import (
	"bufio"
	"fmt"
	"os"
	"strings"
)

func InputCmd() string {
	//参考 version 2 https://my.oschina.net/zengsai/blog/3719
	f := bufio.NewReader(os.Stdin) //读取输入的内容
	fmt.Print("mysql cli>")
	var Input string
	Input, _ = f.ReadString('\n') //定义一行输入的内容分隔符。

	for i := 0; i < len(Input); i++ {
		if i >= len(Input)-2 { //最后一个字符,输出数字
			//fmt.Print(Input[i])
		} else {
			//fmt.Print(string(Input[i]))
		}
	}
	//windows平台操作
	//分隔符'\n' Input是 xxx\r\n    编码是1310
	//分隔符'\r' Input是 xxx\r  编码是13
	Input = strings.Replace(Input, "\n", "", -1)
	Input = strings.Replace(Input, "\r", "", -1)
	//fmt.Println("")
	//fmt.Println("字符串长度", len(Input))
	var str string
	for i := 0; i < len(Input); i++ {
		str = str + string(Input[i])
	}
	return str
}

type sql struct {
	Sql  string
	head uint8
}

func UserInput(packetData []byte) *sql {
	NewPacket().Handler(packetData)

	str := InputCmd()
	list := strings.Split(str, " ")
	head := GetOrder(list[0])
	return &sql{
		Sql:  str,
		head: head,
	}
}
