## Golang 包管理机制

### Reference

+ [聊聊 Go 的包管理](https://xie.infoq.cn/article/641f6d8632d99e6775e00bc5e)
+ [Go 包管理详解](https://zhuanlan.zhihu.com/p/92992277)

### 1. Go包管理历史

1. 在 `1.5` 版本之前，所有的依赖包都是存放在 `GOPATH` 下，没有版本控制。这个类似 Google 使用单一仓库来管理代码的方式。这种方式的最大的弊端就是无法实现包的多版本控制，比如项目 A 和项目 B 依赖于不同版本的 `package`，如果 `package` 没有做到完全的向前兼容，往往会导致一些问题。
2. `1.5` 版本推出了 `vendor` 机制。所谓 `vendor` 机制，就是每个项目的根目录下可以有一个 `vendor` 目录，里面存放了该项目的依赖的 `package`。`go build` 的时候会先去 `vendor` 目录查找依赖，如果没有找到会再去 `GOPATH` 目录下查找。
3. `1.9` 版本推出了实验性质的包管理工具 `dep`，这里把 `dep` 归结为 `Golang` 官方的包管理方式可能有一些不太准确。关于 `dep` 的争议颇多，比如为什么官方后来没有直接使用 `dep` 而是弄了一个新的 `modules`，具体细节这里不太方便展开。
4. `1.11` 版本推出 `modules 机制`，简称 `mod`。



### 2. 基本使用



#### 2.1 GO111MODULE

在 1.12 版本之前，使用 Go modules 之前需要环境变量 GO111MODULE:

- `GO111MODULE=off`: 不使用 modules 功能，查找 `vendor` 和 `GOPATH` 目录
- `GO111MODULE=on` 使用 `modules` 功能，不会去 `GOPATH` 下面查找依赖包。
- `GO111MODULE=auto`: Golang 自己检测是不是使用 `modules` 功能，如果当前目录不在 `$GOPATH` 并且 当前目录（或者父目录）下有 `go.mod` 文件，则使用 `GO111MODULE`， 否则仍旧使用 `GOPATH`。

```bash
# 查看当前环境中 Golang 所使用的环境变量：
go env
# 查看当前环境变量 GO111MODULE 的值
go env GO111MODULE
# 设置环境变量 GO111MODULE 的值 
go env -w GO111MODULE=on
# 顺便设置 GoPROXY 代理
go env -w GOPROXY=https://goproxy.io,direct
```

**查看 go mod 命令**

```bash
$ go help mod
Go mod provides access to operations on modules.

Note that support for modules is built into all the go commands,
not just 'go mod'. For example, day-to-day adding, removing, upgrading,
and downgrading of dependencies should be done using 'go get'.
See 'go help modules' for an overview of module functionality.

Usage:

        go mod <command> [arguments]

The commands are:

        download    download modules to local cache
        edit        edit go.mod from tools or scripts
        graph       print module requirement graph
        init        initialize new module in current directory
        tidy        add missing and remove unused modules
        vendor      make vendored copy of dependencies
        verify      verify dependencies have expected content
        why         explain why packages or modules are needed

Use "go help mod <command>" for more information about a command.
```

#### 2.2 初始化 go module 环境 

```bash
# 使用 git 的项目
go mod init
# 不使用 git 的项目
go mod init packageName
```

#### 2.3 下载依赖包

```bash
# 只下载依赖包
go mod download
# 只拉取必须模块，移除不需要的模块
go mod tidy
```

**PS:**

1. 如果 `tag` 对应内容由更新，需要删除 `pkg` 中的缓存内容：

   ```bash
   cd $GOPATH/pkg/mod
   rm -rf *
   ```

2. `go get/run/build` 也会自动下载依赖。



#### 2.4 添加新依赖包

+ 方法一：直接修改 `go.mod`，然后执行 `go mod download`；

+ 方法二：使用 `go get packageName@1.2.3`，会自动更 `go.mod` 文件；
+ 方法三：`go run/build` 也会自动下载依赖。
+ 将依赖包下载到 `vendor` 目录中，`go vendor` ， 只会下载对应版本。



### 3. 实例

```bash
# 创建一个项目并进入该项目根目录
mkdir GinLearning && cd GinLearning
# 初始化 
go mod init GinLearning
# 获取 Gin 框架
go get -u github.com/gin-gonic/gin
```

*安装完毕后查看项目下 go.mod 文件*

```
module GinLearning

go 1.16

require (
	github.com/gin-gonic/gin v1.7.7 // indirect
	github.com/go-playground/validator/v10 v10.11.0 // indirect
	github.com/golang/protobuf v1.5.2 // indirect
	github.com/json-iterator/go v1.1.12 // indirect
	github.com/mattn/go-isatty v0.0.14 // indirect
	github.com/modern-go/concurrent v0.0.0-20180306012644-bacd9c7ef1dd // indirect
	github.com/ugorji/go v1.2.7 // indirect
	golang.org/x/crypto v0.0.0-20220525230936-793ad666bf5e // indirect
	golang.org/x/sys v0.0.0-20220520151302-bc2c85ada10a // indirect
	google.golang.org/protobuf v1.28.0 // indirect
	gopkg.in/yaml.v2 v2.4.0 // indirect
)

```

+ 会发现还多了一个 `go.sum` 文件，该文件详细罗列了当前项目直接或间接依赖的所有模块版本，并写明了那些模块版本的 SHA-256 哈希值以备 Go 在今后的操作中保证项目所依赖的那些模块版本不会被篡改。
+ 实际上 `go.mod` 文件是启用了 `go modules` 的项目所必须d额最重要的文件，其描述了当前项目(也就是当前模块 `package` )的元信息，每一行都以一个动词开头，目前有以上 5 个动词：
  + `module`：用于定义当前项目的模块路径；
  + `go`：用于设置预期的 Go 版本；
  + `require`：用于设置一个特定的模块版本；
  + `exclude`：用于从使用中排除一个特定的模块版本；
  + `replace`：用于将一个模块版本替换为另外一个模块版本；

+ `indrect`：传递依赖，也就是非直接依赖。



### 4. 错误解决

**①错误提示**

```
go get: module github.com/gin-gonic/gin: Get "https://proxy.golang.org/github.com/gin-gonic/gin/@v/list": dial tcp 172.217.160.81:443: connectex: A connection attempt failed because the connected party did not properly respond after a period of time, or established connection failed because connected host has failed to respond.
```

*解决方法：* 

+ 多半是代理问题，更换代理 `go env -w GOPROXY=https://goproxy.io,direct` 1.13 版本及以上推荐使用 
+ 我之前使用的是 `go env -w GOPROXY=https://goproxy.cn,direct`





