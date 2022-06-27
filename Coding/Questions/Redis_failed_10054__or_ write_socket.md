## Redis:send of 37 bytes failed with errno=10054

最近在脚本中使用 Redis 的 `队列` 执行一些简单的队列任务，经常报类似的错误：
```bash
Notice:Redis::lPush():send of 37 bytes failed with errno=10054 ...
```

[处理redis连接数过多](https://blog.csdn.net/u013050593/article/details/56480358)
网上几乎很少有这个问题的相关的解答，查到的答案一般都是与Redis的 `最大连接数` 的配置有关。
> redis服务器默认设置的最大连接数 `maxclients` 是 10000，但是受服务器最大文件数影响，服务器默认最大文件数是 1024，所以 redis 最大连接也为 1024-32=992，
> 由于网络原因或连接未正常关闭导致 redis 服务器连接数接近 990 左右，应用程序连不上 redis。
```bash
# redis 查看最大连接数
127.0.0.1:6379> config get maxclients
1) "maxclients"
2) "10000"
# Redis 设置最大连接数
127.0.0.1:6379> config set maxclients 10000
OK
```

但我这里是本地脚本且只跑了一个脚本，很显然不会是连接数的问题。后续排查到是 Redis 的 `timeout` 默认配置太小。
脚本执行过程中 Redis timeout 后主动断开了链接。
```bash
# redis 查看 timeout
127.0.0.1:6379> config get timeout
1) "timeout"
2) "65"
# redis 设置 timeout
127.0.0.1:6379> config set timeout 3600
OK
```

早上上班又遇到了 Redis 的问题...
接手的一个 `Yii` 的项目排查队列的问题，队列是用 Redis 驱动的，因为下载文件的原因脚本等待时长较长就出现了：
```bash
Exception 'yii\redis\SocketException' with message 'Failed to write to socket. 0 of 47 bytes written.
```

很显然也是客户端与 Redis 的链接断开了，写入这个 `socket` ...

