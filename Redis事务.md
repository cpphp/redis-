## Redis事务

MULTI/EXEC/DISCARD/WATCH是Redis事务的基础。


事务可以一次执行多个命令，并且带有以下两个重要的保证：

* 事务是一个单独的隔离操作：事务中的所有命令都会序列化、按顺序地执行。事务在执行的过程中，不会被其他客户端发送来的命令请求所打断。

* 事务是一个原子操作：事务中的命令要么全部被执行，要么全部都不执行。


EXEC命令负责触发并执行事务中的所有命令：

	* 如果客户端在使用MULTI开启了一个事务后，却因为断线而没有成功执行EXEC,那么事务中的所有命令都不会被执行。
	* 如果客户端成功在开启事务之后执行EXEC,那么事务中的所有命令都会被执行。


### MULTI 

MULTI命令开启一个事务，它总是返回ok.MULTI执行之后，客户端可以继续向服务器发送任意多条命令，这些命令不会被立即执行，而是被放到一个队列中，当EXEC命令被调用时，所有队列中的命令才会被执行。


### DISCARD

DISCARD命令可以清空事务队列，并放弃执行事务。

执行discard命令，事务会被放弃，事务队列会被清空，并且客户端会从事务状态中退出。



## 事务中的错误

使用事务时可能会遇上以下两种错误：

* 事务在执行exec之前，入队的命令可能会出错。比如：命令可能会产生语法错误，或者其他更加严重的错误，比如内存不足

		127.0.0.1:6379> multi
		OK
		127.0.0.1:6379> set a  b
		QUEUED
		127.0.0.1:6379> lpush a
		(error) ERR wrong number of arguments for 'lpush' command
		127.0.0.1:6379> exec
		(error) EXECABORT Transaction discarded because of previous errors.


* 命令可能在exec调用之后失败。比如，事务中的命令可能处理了错误类型的键。

		127.0.0.1:6379> multi
		OK
		127.0.0.1:6379> incr num
		QUEUED
		127.0.0.1:6379> get num
		QUEUED
		127.0.0.1:6379> get a
		QUEUED
		127.0.0.1:6379> get bbb
		QUEUED
		127.0.0.1:6379> exec

在redis2.6.5开始，服务器会对命令入队失败的情况进行记录，并在客户端调用exec命令时，拒绝执行并自动放弃这个事务。


至于那些在exec命令执行之后产生的错误，并没有对它们进行特别的错误：即使事务中有某个/某些命令在执行时产生了错误，事务中的其他命令会继续执行。

## redis事务不支持回滚

	因为没有任何机制能避免程序员自己造成的错误，并且这类错误通常不会再生产环境中出现，所以Resi选择了简单、快速的无回滚方式来处理事务。


## 使用check-and-set操作实现乐观锁

watch命令可以为redis事务厅check-and-set行为。

被watch的键会监视,并会发觉这些键是否被改动过了。如果有至少一个被监视的键在exec执行之前被修改了，那么整个事务都会被取消，exec返回空多条批量回复(null multi-bulk reply)来表示事务已经失败。


举个例子，假设我们需要原子性地为某个值进行增1操作。

我们可能会这样做(不使用incr命令)：

	val = get mykye

	val = val + 1
	
	set mykey $val

上面的这个实现在只有一个客户端的时候可以执行得很好。但是，当多个客户端同时对同一个键进行这样的操作时，就会产生竞争条件。

举个例子，如果客户端A和B都读取了键原来的值，比如10,那么两个客户端会将键的值设为11,但正确的结果应该是12才对。

有了watch,我们就可以轻松地解决这类问题了：

watch mykey

val = get mykey

val = val + 1

multi

set mykey $val

exec

使用上面的代码，如果在watch执行之后，exec执行之前，有其他客户端修改了mykey的值，那么当前客户端的事务就会失败。程序需要做的，就是不断重试这个操作，直到没有发生碰撞结束。

## 了解watch

watch使得exec命令需要有条件地执行：事务只能在所有被监视键都没有被修改的前提下执行，如果这个前提不能满足的话，事务就不会被执行。

watch命令可以被调用多次。对键的监视从watch执行之后开始生效，直到调用exec为止。

用户还可以在单个watch命令中监视任意多个键：

	redis> watch key1 key2 key3

当exec被调用时，不管事务是否成功执行，对所有键的监视都会被取消。另外，当客户端断开连接时，该客户端对键的监视也会被取消。

使用无参数的unwatch命令可以手动取消对所有键的监视。

## 使用watch实现zpop

watch可以用于创建redis没有的命令。

举个例子，以下代码实现了原创的zpop命令，它可以原子地弹出有序集合中分值最小的元素：

	watch zset
		element = zrange zset 0 0

	multi

		zrem zset element

	exec










