
> 本文将对一些常见的PHP概念进行解释，比如CGI/FPM等等。
> 这些概念经常性的出现在一些框架手册/面试题中，对于这些我总是一知半解的状态，今天花了点时间做一个整理，如有后续再补充。
> 此外，欢迎指正内容不严谨或有误的地方！

#### Reference
1. [PHP-FPM+Nginx通信原理](https://mp.weixin.qq.com/s/8IIzbqtRrVRV1gVfL94uxg)
2. [全面了解CGI、FastCGI、PHP-FPM](https://mp.weixin.qq.com/s/FdVGNKYyMDBaeeLq4HE9Hw)

*所有的Web服务器在设计之初都只是为了给用户提供静态资源。*

1. 对于静态资源请求，Web服务器只需要找到文件并响应给客户端即可；
2. 但如果不是静态资源请求(配置文件中会对非静态资源文件的请求进行配置)，Web服务器就会将请求转发给对应的解析器(如PHP解析器)进行处理；

##### 1.概念解释

1. **CGI：** 通用网关接口，Web服务器与Web应用之间*数据交换的一种协议*；
2. **FastCGI：** 同 CGI ，也是一种通信协议，但对比 CGI 在效率上做了优化；
3. **PHP-CGI：** PHP(Web应用)对Web服务器提供的 CGI 协议接口程序（即PHP实现的CGI协议）；
4. **PHP-FPM：** PHP(Web应用)对Web服务器提供的 FastCGI 协议的接口程序，此外附带一些智能化的任务处理（即PHP实现的FastCGI协议）；

*Web服务器一般有Apache、Nginx、Tomcat等，Web应用则有PHP、Java等。*

##### 2.CGI

**PHP资源请求(CGI模式)：**
1. 当Web服务器接收到 `index.php` 请求后，会启动对应的 `CGI` 程序，即PHP解析器；
2. 然后PHP解析器会解析 `php.ini` 文件，初始化执行环境，然后处理PHP请求，再以CGI规定的格式返回处理结果，最后退出进程；
3. Web服务器接收到结果后响应给客户端，便是一次完整的PHP请求流程。

**CGI的优点：**
独立于服务器，相当于一个中间件。协议只是规定了数据的传输格式，减少了服务器和应用之间的关联性，使俩者更加独立。
**CGI的缺点：**
CGI的缺点在于每一次Web请求都会有启动和退出的过程，即`fork-and-exxcute`模式，这样的处理方式很难应对大规模并发请求。

##### 3.FastCGI

FastCGI 则可以看作一个常驻型(long-live)的CGI，它可以一直处于执行状态，激活后不会每次要花费时间去 `fork` 一次。

> FastCGI是与语言无关、可伸缩架构的CGI开放扩展，其主要行为是将CGI解释器保持在内存中，以此获得较高的性能。而CGI解析器的反复加载作为CGI性能低下主要原因，所以如果CGI解释器可以常驻在内存中，并接受FastCGI进行管理器的调度，就可以提供良好的性能、伸缩性、Fail-Over等；

*PHP资源请求(FastCGI模式)：*
1. FastCGI会先启动一个`master`，解析配置文件，初始化执行环境，然后再启动多个`worker`；
2. 当请求过来时，`master` 会分发给一个 `worker`，然后立即接收下一个请求；
3. 当请求过多时，`master` 会根据配置项预先启动几个 `worker` 等待；当请求较少时，则停掉一些 `worker` 从而节约资源，提高性能；

*CGI和FastCGI的对比：*
1. 对于CGI来说，每一个Web请求都会要求PHP解析器重新解析 `php.ini`、重新载入全部PHP扩展，并重新初始化全部数据结构；而使用FastCGi，这些过程仅需要在进程启动时执行一次(例如：数据库的持续连接)。
2. 由于FastCGI是多进程的，所以比CGI多线程消耗更多的服务器内存，PHP-CGI解释器每个进程消耗 *7-25M* 内存。

##### 4.PHP-FPM

*FastCGI仅是一个协议，而PHP-FPM则是实现了这个协议的PHP程序。*

PHP-FPM 是 FastCGI的实现，并提供了进程管理的功能。FastCGI进程包含 `master` 进程和 `worker` 进程俩种进程。`master` 进程只有一个，主要负责监听端口和接收Nginx请求，而 `worker` 进程一般则有多个(可配置)，每个进程内部都嵌入了一个PHP解释器，是PHP代码真正执行的地方。

1. 对于 PHP-CGI 来说，其仅仅是一个实现了CGI协议的PHP程序，只具备解析PHP请求，返回结果的能力，是不具备进程管理的能力的；而 PHP-FPM 即是能够调度 PHP-CGI 的进程管理程序；
2. 由于 PHP-CGI 在启动后再变更 `php.ini` 文件，需要重启才会生效，无法平滑重启；而 PHP-FPM 解决了这一点，PHP-FPM 可以对新的 `worker` 使用变更后的配置环境，已经存在的 `worker` 则在处理完当前请求后进行销毁，从而达到平滑过渡。

*常见的高性能PHP Web服务器搭建方式：Apache/Nginx + FastCGI + PHP-FPM(+PHP-CGI)。*

##### 5.PHP-FPM和Nginx通信方式

**Nginx正向代理：**

1. 正向代理代理的是客户端，常见的VPN的就是一种正向代理服务器。简单来说就是，当客户端无法访问到一个远程的服务器时，可以将请求发送至一个可以访问该服务器的中间代理服务器代为转发请求和响应。
2. 正向代理一般用于访问无法访问的资源、缓存(加速访问资源)、客户端访问授权(上网认证)、记录用户访问记录(上网行为管理)。

**Nginx反向代理：**

1. 反向代理代理的是服务器端，最常见的就是服务器集群分布式部署、负载均衡。
2. 也就是为了应对大流量用户的访问，一台服务器往往是无法承担重任的，所以将请求分发至多台反向代理服务器进行处理，最后在返回给服务器进行响应等方式。
3.反向代理一般用于保护内网安全、负载均衡。

**PHP-FPM和Nginx通信：**

1. Nginx 接收到HTTP动态请求后，将会根据Nginx配置初始化 FastCGI 环境。
2. Nginx 将请求采用 `socket` 的方式转发给 FastCGI 主进程。
3. FastCGI 主进程选择一个空闲的 `worker` 进程连接，然后 Nginx 将CGI环境变量和标准输入发送到该 `worker`进程(PHP-CGI)；
4. `worker` 进程处理完成后将标准输出和错误信息从*同一socket连接*返回给Nginx，然后 `worker` 进程关闭连接，等待下一个连接；

*Nginx解析PHP请求的常见配置*
```nginx
    # 1.TCP socket通信
    fastcgi_pass    127.0.0.1:9000
    # 2.Unix socket通信
    fastcgi_pass    unix:/tmp/php_cgi.sock
```

**fastcgi_pass：**

1. Nginx和PHP-FPM的进程间通信有俩种方式，一种是 *TCP Socket*，一种是 *Unix Socket*。
2. *TCP Socket* 的方式是IP加端口，可以跨服务器；而*Unix Socket*不经过网络，只能用于Nginx和PHP-FPM在同一服务器的场景；

*TCP Socket 方式配置：*
```nginx
    // nginx.conf
    fastcgi_pass 127.0.0.1:9000
    // php-fpm.conf
    listen=127.0.0.1:9000
``` 

*Unix Domain Socket方式配置：*
```nginx
    // nginx.conf
    fastcgi_pass unix:/tmp/php-fpm.sock
    // php-fpm.conf(php-fpm.sock是一个文件，由php-fpm生成)
    listen=/tmp/php-fpm.sock
```

*俩种方式的通信过程比较：*

```text
//TCP Socket
Nginx <=> socket <=> TCP/IP <=> socket <=> PHP-FPM
//Unix Socket
Nginx <=> socket <=> PHP-FPM
```


