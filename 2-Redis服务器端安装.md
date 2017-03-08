## Redis服务器端安装
### 环境说明

1. 系统： CentOS6.5

### 安装步骤

1. 下载Redis

	地址：  http://redis.io/download

	Linux命令下载：

		wget http://download.redis.io/releases/redis-3.2.8.tar.gz

2. 移到解压后的Redis到/usr/local/redis-3.2.8

		tar -zxvf redis-3.2.8.tar.gz
   		mv redis-3.2.8 /usr/local/

3. 编译以及安装

    	cd /usr/local/redis-3.2.8
    	make
		make install

4. 将Redis加入自启动

   		vim /etc/rc.local
	
			/usr/local/bin/redis-server &


## 启动Redis

	# redis-server
   	//Redis服务器默认会使用6379端口，通过 --port参数可以自动以端口号：

	# redis-server --port 6378

## 停止Redis

	# redis-cli SHUTDOWN

    或者在redis命令行中：

	# redis-cli

	redis> shutdown

## 小实例

   进入Redis命令行

	# redis-cli

	//将a这个值存放到n这个键中
	127.0.0.1:6379> set n a
	OK

	//get命令拿出刚刚的值
	127.0.0.1:6379> get n
	"a"

	//exit退出redis命令行
	127.0.0.1:6379> exit

   
