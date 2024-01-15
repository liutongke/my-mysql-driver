package MySQLPackets

import (
	"fmt"
	"net"
)

// MySQLConnection 包含 MySQL 连接信息
type MySQLConnection struct {
	TCPConnection net.Conn
	Username      string
	Password      string
}

// NewMySQLConnection 创建一个新的 MySQL 连接
func NewMySQLConnection(username, password, ip, port string) (*MySQLConnection, error) {
	// 参数验证
	if username == "" || password == "" || ip == "" || port == "" {
		return nil, fmt.Errorf("username, password, ip, and port must not be empty")
	}

	// 建立连接
	conn, err := net.Dial("tcp", fmt.Sprintf("%s:%s", ip, port))
	if err != nil {
		return nil, fmt.Errorf("failed to establish connection, err: %v", err)
	}

	// 返回连接实例
	return &MySQLConnection{TCPConnection: conn, Username: username, Password: password}, nil
}

// Close 关闭 MySQL 连接
func (m *MySQLConnection) Close() error {
	if m.TCPConnection != nil {
		return m.TCPConnection.Close()
	}
	return nil
}
