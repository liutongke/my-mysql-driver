package server

import "strings"

func GetOrder(idx string) uint8 {
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

func (conn *Mysql) SetChart() []byte {
	var bytes = []byte{0x03}
	conn.Write(append(bytes, []byte("SET NAMES utf8;")...), 0)
	return nil
}

func (conn *Mysql) Query(typeSql *sql) []byte {
	var bytes = []byte{typeSql.head}
	conn.Write(append(bytes, []byte(typeSql.Sql)...), 0)
	return nil
}
