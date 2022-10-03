package server

import (
	"bufio"
	"fmt"
	"os"
	"strings"
)

type InputInfo struct {
	InputText string
	head      uint8
}

func UserInput() *InputInfo {
	str := InputCmd()
	list := strings.Split(str, " ")
	head := GetCommand(list[0])
	return &InputInfo{
		InputText: str,
		head:      head,
	}
}

func InputCmd() string {
	f := bufio.NewReader(os.Stdin) //读取输入的内容
	fmt.Print("mysql cli>")
	var Input string
	Input, _ = f.ReadString('\n') //定义一行输入的内容分隔符。

	Input = strings.Replace(Input, "\n", "", -1)
	Input = strings.Replace(Input, "\r", "", -1)

	var str string
	for i := 0; i < len(Input); i++ {
		str = str + string(Input[i])
	}
	return str
}
