package server

import (
	"fmt"
	"os"
	"strings"
)

func GetCommand(idx string) uint8 {
	m := map[string]uint8{
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

	return m[strings.ToLower(idx)]
}

// SetChart 设置字符
func (conn *Mysql) SetChart() []byte {
	var bytes = []byte{0x03}
	conn.Write(append(bytes, []byte("SET NAMES utf8;")...), 0)
	return nil
}

func (conn *Mysql) Query(InputInfo *InputInfo) []byte {
	if strings.Compare(InputInfo.InputText, "exit") == 0 || strings.Compare(InputInfo.InputText, "quit") == 0 {
		return conn.Quit()
	}
	var bytes = []byte{InputInfo.head}
	conn.Write(append(bytes, []byte(InputInfo.InputText)...), 0)
	return nil
}

// Quit 退出
func (conn *Mysql) Quit() []byte {
	var bytes = []byte{0x01}
	conn.Write(bytes, 0)
	fmt.Println("bye bye")
	os.Exit(0)
	return nil
}

func Ping(conn *Mysql) {
	var bytes = []byte{0x0E}
	conn.Write(bytes, 0)
	fmt.Println("ping")
	return
}
