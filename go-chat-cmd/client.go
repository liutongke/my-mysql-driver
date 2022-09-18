package main

import (
	"fmt"
	"github.com/gorilla/websocket"
	"log"
)

func receiveHandler(connection *websocket.Conn) {
	for {
		_, msg, err := connection.ReadMessage()
		if err != nil {
			log.Println("Error in receive:", err)
			return
		}
		fmt.Printf("%s:Received\n", msg)
	}
}

type Ws struct {
	socketUrl string
	Conn      *websocket.Conn
}

func NewWs(socketUrl string) *Ws {
	Conn := connWs(socketUrl)
	return &Ws{socketUrl: socketUrl, Conn: Conn}
}

var client *Ws

func StartWs(socketUrl string) {
	client = NewWs(socketUrl)

	go receiveHandler(client.Conn)
}

func connWs(socketUrl string) (conn *websocket.Conn) {
	conn, _, err := websocket.DefaultDialer.Dial(socketUrl, nil)
	if err != nil {
		fmt.Println("Error connecting to Websocket Server:", err)
	}
	return
}

func GetWsClient() *Ws {
	return client
}

// SendMsg 发送消息
func (w *Ws) SendMsg(sendMsg string) {
	err := w.Conn.WriteMessage(websocket.TextMessage, []byte(sendMsg))
	if err != nil {
		fmt.Println("SendMsg err:", err)
	}
}
