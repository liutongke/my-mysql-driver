## 主从复制配置

配置文件在`/etc/mysql/conf.d/`目录中文件名`docker.cnf`

```shell
docker run --name mysql-v3 -p 3303:3306 -v C:\Users\keke\dev\docker\lnmp\apiswoole-core\mysql-driver\back:/var/www -e MYSQL_ROOT_PASSWORD=xCl5QUb9ES2YfkvX -d mysql:5.7

change master to master_host='192.168.0.107',master_user='root',master_password='root',master_log_file='mysql-bin.000001',master_log_pos=1720;

start slave;

show master status;

show slave status\G;

cp /var/www/主库配置.cnf /etc/mysql/conf.d/
```


## 主库查看从库连接情况

```shell
show processlist;

SHOW SLAVE HOSTS;
```

参考网址:

[MySQL主从配置详解](https://www.jianshu.com/p/b0cf461451fb)