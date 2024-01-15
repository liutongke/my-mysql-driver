package MySQLPackets

import (
	"bufio"
	"fmt"
	"os"
	"strings"
)

type UserInputInfo struct {
	InputText string
	Command   uint8
}

func GetUserInput() *UserInputInfo {
	str := cliInput()
	list := strings.Split(str, " ")

	return &UserInputInfo{
		InputText: str,
		Command:   commandDict(list[0]),
	}
}

func cliInput() string {
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
func commandDict(idx string) uint8 {
	command := map[string]uint8{
		"exit":            01,
		"quit":            01,
		"use":             03,
		"select":          03,
		"insert":          03,
		"delete":          03,
		"update":          03,
		"show":            03,
		"create database": 05,
		"create table":    99,
	}

	return command[strings.ToLower(idx)]
}

// SetChart 设置字符
func (conn *MySQLConnection) SetChart() []byte {
	var bytes = []byte{0x03}
	//m.Write(append(bytes, []byte("SET NAMES utf8;")...), 0)
	SendMsg(conn, append(bytes, []byte("SET NAMES utf8;")...), 0)
	return nil
}

// Query https://dev.mysql.com/doc/dev/mysql-server/latest/page_protocol_com_query.html
func (conn *MySQLConnection) Query(InputInfo *UserInputInfo) []byte {
	if strings.Compare(InputInfo.InputText, "exit") == 0 || strings.Compare(InputInfo.InputText, "quit") == 0 {
		return conn.Quit()
	}
	var bytes = []byte{InputInfo.Command}

	SendMsg(conn, append(bytes, []byte(InputInfo.InputText)...), 0)
	//conn.Write(append(bytes, []byte(InputInfo.InputText)...), 0)
	return nil
}

// Quit 退出
func (conn *MySQLConnection) Quit() []byte {
	var bytes = []byte{0x01}
	//conn.Write(bytes, 0)
	SendMsg(conn, bytes, 0)
	fmt.Println("bye bye")
	os.Exit(0)
	return nil
}

func Ping(conn *MySQLConnection) {
	var bytes = []byte{0x0E}
	//conn.Write(bytes, 0)
	SendMsg(conn, bytes, 0)
	fmt.Println("ping")
	return
}
