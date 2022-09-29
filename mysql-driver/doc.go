package main

func GetOrder(idx string) uint8 {
	m := map[string]uint8{
		"exit":            01,
		"use":             03,
		"select":          03,
		"insert":          03,
		"delete":          03,
		"update":          03,
		"create database": 05,
		"create table":    99,
	}
	return m[idx]
}
