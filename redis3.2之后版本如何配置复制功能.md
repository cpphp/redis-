## Redis3.2之后版本如何配置复制功能

1. 关闭主服务器的protected-mode模式

	因为在redis3.2之后加入了protected-mode特性，默认情况下只允许本机连接redis服务器。但是配置复制功能需要从服务器向主服务器发送一个sync命令。如果主服务器的protected-mode模式不管吧，从服务器也就无法向主服务器发送sync命令了。

	
   具体操作(在主服务器下)：

		//这是我的主服务器redis运行时使用的配置文件
		$ vim /usr/local/redis-3.2.8/redis.conf 

			将bind 127.0.0.1 注释掉

			修改daemonize yes 为  daemonize no

			关闭保护模式

				protected-mode yes 改为 protected no

		//重启redis
		$ /usr/local/bin/redis-server /usr/local/redis-3.2.8/redis.conf  &


2. 配置主从

关闭了主库的保护模式后，就可以开始配置主从。在redis从服务器的配置文件中加入：

	slaveof 192.168.17.99 6379

另外一种方法是在redis从服务器的命令行中用slaveof命令，输入主服务器的IP和端口，然后同步就会开始：

	redis> slaveof 192.168.17.99 6379


当然， 你需要将代码中的 192.168.17.99 和 6379 替换成你的主服务器的 IP 和端口号。


		

	