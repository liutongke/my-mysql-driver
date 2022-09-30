package server

import (
	"bufio"
	"fmt"
	"os"
	"strings"
)

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

type sql struct {
	Sql  string
	head uint8
}

func UserInput() *sql {
	str := InputCmd()
	list := strings.Split(str, " ")
	if list[0] == "exit" {
		fmt.Println("bye bye")
		os.Exit(0)
	}
	head := GetOrder(list[0])
	return &sql{
		Sql:  str,
		head: head,
	}
}
