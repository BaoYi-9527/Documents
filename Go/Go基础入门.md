## Go语言入门

### Reference

1. [Go语言基础语法](https://www.bilibili.com/video/BV1x94y1d7pq?p=2)
2. [golang 编程规范-项目目录结构](https://makeoptim.com/golang/standards/project-layout)
3. [Golang 报错 package xxx is not in GOROOT or GOPATH 或者 cannot find package “xxx“ in any of](https://junhaideng.github.io/2020/07/11/golang/fundamental/import-package/)

### 1.前言

#### 1.1 核心特性

##### 并发编程
1. Go语言的并发执行单元是一种称为 goroutine 的协程。
1. 协程又称微线程，比线程更轻量、开销更小、性能更高。
1. Go在语言级别上提供 `go` 关键字用于启动协程，并且在同一台机器上可以启动成千上万个协程。
1. 协程间一般由应用程序进行显示调度上下文切换，无需下到内核层，高效不少。
1. 协程之间的通信靠独有的 `channel` 机制实现。

##### 内存回收(GC)

1. 内存自动回收，再也不需要开发人员管理内存。



> GC过程：先 `stop the world` 扫描所有对象判活，将可回收对象在一段 `bitmap` 区中标记下来，紧接着立即 `start the world` 恢复服务，同时启用一个专门 `goroutine` 回收内存到空闲 `list` 中以备复用，不进行物理释放，物理释放由专门线程定期来执行。GC的瓶颈在于每次都要扫描所有对象来判活，带收集的对象数目越多，速度就越慢，GC性能会随着版本不断更新而不断优化。

##### 内存分配

1. 先分配一块大内存区域。
2. 大内存被切分成各个大小等级的块，放入不同的空闲 `list` 中。
3. 对象分配空间时从空闲 `list` 中取出大小合适的内存块。
4. 内存回收时，会把不用的内存重新放回到空闲 `list`。
5. 空闲内存会按照一定的策略进行合并，以减少碎片。

##### 网络编程

1. `socket` 用 `net.Dial` (基于 `TCP/UDP` ，封装了传统的 `connect、listen、accept`  等接口)。
2. `http` 用 `http.Get/Post()`。
3. `rpc` 用 `client.Call('className.methodName', args, &reply)`。

##### 函数多返回值

1. 允许函数返回多个值，在某些场景下，可以有效的简化编程。
2. Go语言推荐的编程风格，是函数返回的最后一个参数为 `error` 类型。

##### 语言交互性

Go 语言可以和 C 程序进行交互，即调用C语言编译的库。

##### 异常处理

*三个重要关键字：* `defer、panic、recover`

1. `defer` 是函数结束后执行，先进先出。
2. `panic` 是程序无法修复的错误时使用，但会让 `defer` 执行完毕。
3. `recover` 会修复错误，不至于程序终止。当不确定函数不会错时使用 `defer + recover`。

#### 1.2 目录结构

```
├── api[项目对外提供和依赖的API文件]
├── assets[项目中使用到的其他资源(图像、logo等)]
├── build[通用应用程序目录：打包和持续集成所需的文件]
│   ├── ci[存放持续集成的配置和脚本，如果持续集成平台对配置文件有路径要求，则可将其link到指定位置]
│   └── package[存放AMI、Docker、系统包(deb、rpm、pkg)的配置和脚本等]
├── cmd[当前项目的可执行文件]						
│   └── _your_app_
├── configs[配置文件模板或默认配置]
├── deployments[IaaS、PaaS，系统和容器编排部署配置和模板，某些存储库中也叫 /deploy]
├── docs[设计和用户文档]
├── examples[应用程序和公共库的示例程序]
├── githooks[Git钩子]
├── init[系统初始化(systemd、upstart、sysv)和进程管理(runit、supervisord)配置]
├── internal[私有的应用程序代码库]
│   ├── app
│   │   └── _your_app_
│   └── pkg
│       └── _your_private_lib_
├── pkg[外部程序可以使用的库代码]
│   └── _your_public_lib_
├── scripts[用于执行各种构建、安装、分析等操作的脚本]
├── test[外部测试应用程序和测试数据]
├── third_party[外部辅助工具、fork的代码和其他第三方工具(Swagger UI)]
├── tools[此项目支持的工具]
├── vendor[应用程序的依赖关系]
├── web[Web 网络程序特定组件：静态Web资源，服务器端模板和单页应用]
│   ├── app
│   ├── static
│   └── template
├── website
├── .gitignore
├── LICENSE.md
├── Makefile
├── README.md
└── go.mod
```

> 有一些 Go 项目确实包含 `src` 文件夹，但通常只有在开发者是从 Java（这是 Java 中一个通用的模式）转过来的情况下才会有。如果可以的话请不要使用这种 Java 模式。你肯定不希望你的 Go 代码和项目看起来向 Java。
>
> 
>
> 不要将项目级别的 `/src` 目录与 Go 用于其工作空间的 `/src` 目录混淆，就像 [How to Write Go Code](https://golang.org/doc/code.html)中描述的那样。`$GOPATH`环境变量指向当前的工作空间（默认情况下指向非 Windows 系统中的$HOME/go）。此工作空间包括顶级 `/pkg`，`/bin` 和 `/src` 目录。实际的项目最终变成 `/src` 下的子目录，因此，如果项目中有 `/src` 目录，则项目路径将会变成：`/some/path/to/workspace/src/your_project/src/your_code.go`。请注意，使用 Go 1.11，可以将项目放在 `GOPATH` 之外，但这并不意味着使用此布局模式是个好主意。

#### 1.3 代码结构

```golang
package main	// 声明文件所在的包 每个Go文件必须有归属的包

import "fmt"	// 引入程序中需要用到的包，为了使用包下函数

func main()  {	// main 主函数 -> 程序的入口
	fmt.Println("Hello World!!!")	// 控制台输出
}
```

#### 1.4 编译与执行

```bash
// 编译
go build test.go
// 编译&执行
go run test.go
// 执行编译后文件名
go build -o hello.exe test.go
```

#### 1.5 语法注意事项

1. 源文件以 `go` 为扩展名
2. 程序的执行入口是 `main()` 函数
3. 严格区分大小写
4. 方法是由一条条语句构成，每个语句后不需分号(Go语言会在每行后自动加分号)，体现了Golang的简洁性
5. 定义的变量或者 `import` 的包如果没有使用到，代码不能编译通过
6. 大括号都是成对出现的，缺一不可，且函数体的 `{` 不不可独立一行
7. 块注释：`/**/` 单行注释：`//`

#### 1.6 代码风格

1. 注意缩进，可以通过命令格式化代码 `gofmt -w test.go`
2. 运算符俩边加空白符
3. 注释：推荐行注释
4. package 的命名尽量保证和目录一致，尽量选用有意义的包名，简短、有意义，不要和标准库冲突
5. 变量名、函数名、常量名：采用驼峰命名
6. 如果变量名、函数名、常量名首字母大写，则可以被其他的包访问，小写则只能在本包中使用

#### 1.7 标准库

***

### 2.入门

#### 2.1 变量

```golang
func main()  {
	// 1. 变量声明
	var age int
	// 2. 变量的赋值
	age = 18
	// 3. 变量的使用
	fmt.Println("age = ", age)
	// 变量声明+赋值
	var age2 int = 19
	fmt.Println("age2 = ",age2)
}

```

```golang
package main

import "fmt"

// 定义在函数外的变量为 全局变量
var (
	n9 = 100
	n10 = 3.14
)

func main()  {
	// 变量的四种使用方式
	// ① 声明+赋值->使用
	var num int = 18
	fmt.Println(num)
	// ② 指定变量类型 使用0值
	var num2 int
	fmt.Println(num2)
	// ③ 不指定类型 Go会进行类型推导
	var num3 = 10
	fmt.Println(num3)
	// ④ 省略 var 关键字
	sex := "male"
	fmt.Println(sex)
	// 一次性声明多个变量
	var n1, n2, n3 int
	fmt.Println(n1, n2, n3)

	var n4, name, n5 = 10, "jack", 9
	fmt.Println(n4, name, n5)

	var n6, n7, n8 = 10, 11, 9
	fmt.Println(n6, n7, n8)

	fmt.Println(n9, n10)
}

```

#### 2.2 数据类型

+ 基本数据类型
  + 数值型
    + 整数类型 `int、int8、int16、int32、int64、uint、uint8、uint16、uint32、uint64、byte`
    + 浮点类型 `float32、float64`
  + 字符型 `byte`
  + 布尔类型 `bool:true/false`
  + 字符串类型 `string`
+ 派生数据类型/复杂数据类型
  + 指针 `pointer`
  + 数组 `array`
  + 结构体 `struct`
  + 管道 `channel`
  + 函数 `func`
  + 切片 `slice`
  + 接口 `interface`
  + map 

```golang
func main()  {
	// 字符串的表示形式
	// ① 如果字符串里没有特殊，字符串的表示用双引号
	// ② 如果字符串中有特殊字符，字符串的表示形式可以使用反引号 ``
	var s1 string = "Hello Golang!!!"
	fmt.Println(s1)
	var s2 string = `
	package main
	import "fmt"
	func main()  {
		// 1. 变量声明
		var age int
		// 2. 变量的赋值
		age = 18
		// 3. 变量的使用
		fmt.Println("age = ", age)
		// 变量声明+赋值
		var age2 int = 19
		fmt.Println("age2 = ",age2)
	}
	`
	fmt.Println(s2)
	// ③ 字符串的拼接
	var s5 string = "abc" + "def"
	s5 += "hij"
	fmt.Println(s5)
	// ④ 字符串过长
	var s6 string = "abc" + "def" + "abc" + "def" + "abc" + "def" + "abc" + "def" + "abc" + "def" + "abc" + "def" +
		"abc" + "def" + "abc" + "def" + "abc" + "def" + "abc" + "def"
	fmt.Println(s6)
}
```

*Golang中的数据类型都有一个默认值：*

| 数据类型   | 默认值  |
| ---------- | ------- |
| 整数类型   | `0`     |
| 浮点类型   | `0`     |
| 布尔类型   | `false` |
| 字符串类型 | `""`    |

*基本数据类型转换为 string 类型：*

```golang
func main()  {
	var n1 int = 19
	var n2 float32 = 3.78
	var n3 bool = false
	var n4 string = "aaa"

	// Sprintf根据 format 参数生成格式化的字符串并返回该字符串。
	var s1 string = fmt.Sprintf("%d", n1)
	fmt.Printf("s1对应的类型是: %T,值是: %v \n", s1, s1)	// s1对应的类型是: string,值是: 19

	var s2 string = fmt.Sprintf("%f", n2)
	fmt.Printf("s2对应的类型是: %T,值是: %q \n", s2, s2)	// s2对应的类型是: string,值是: "3.780000"

	var s3 string = fmt.Sprintf("%t", n3)
	fmt.Printf("s3对应的类型是: %T,值是: %q \n", s3, s3)	// s3对应的类型是: string,值是: "false"

	var s4 string = fmt.Sprintf("%s", n4)
	fmt.Printf("s4对应的类型是: %T,值是: %q \n", s4, s4)	// s4对应的类型是: string,值是: "aaa"

	// strconv包实现了基本数据类型和其字符串表示的相互转换。
	var s5 string = strconv.FormatInt(int64(n1), 10)
	fmt.Printf("s5对应的类型是: %T,值是: %q \n", s5, s5)	// s5对应的类型是: string,值是: "19"

	var s6 string = strconv.FormatFloat(float64(n2),'f', 9, 64)
	fmt.Printf("s6对应的类型是: %T,值是: %q \n", s6, s6)	// s6对应的类型是: string,值是: "3.779999971"

	var s7 string = strconv.FormatBool(n3)
	fmt.Printf("s7对应的类型是: %T,值是: %q \n", s7, s7)	// s7对应的类型是: string,值是: "false"
}
```

*string 类型转换为基本数据类型：*

```golang
func main()  {
	var s1 string = "true"
	var s2 string = "10086"
	var s3 string = "3.14"
	var s4 string = "Golang"

	// string -> bool
	var b bool
	b, _ = strconv.ParseBool(s1)
	fmt.Printf("b对应的类型是: %T,值是: %v \n", b, b)	// b对应的类型是: bool,值是: true

	// string -> int64
	var c int64
	c, _ = strconv.ParseInt(s2, 10, 64)
	fmt.Printf("c对应的类型是: %T,值是: %v \n", c, c)	// c对应的类型是: int64,值是: 10086

	// string -> float32
	var  d float64
	d, _ = strconv.ParseFloat(s3, 64)
	fmt.Printf("d对应的类型是: %T,值是: %v \n", d, d)	// d对应的类型是: float64,值是: 3.14
		
    // string 转换为其他类型时，一定要确保 string 类型能够转换为成有效的数据类型
	// 否则最后得到的结果就是该类型的默认值输出
	var f bool
	f, _ = strconv.ParseBool(s4)
	fmt.Printf("f对应的类型是: %T,值是: %v \n", f, f)	// f对应的类型是: bool,值是: false
}
```

##### 指针

```golang
func main()  {
	var age int = 18
	// &+变量名即可获取变量的内存地址
	fmt.Println(&age)	// 变量内存地址 0xc0000ac058

	// 定义一个指针变量
	// *int 是一个指针类型 (指向int类型的指针)
	// &age 内存地址， 即ptr变量的具体值为 age 变量的内存地址
	// 指针就是内存地址
	var ptr *int = &age
	fmt.Println(ptr)	// 0xc0000ac058
	fmt.Println("ptr的内存地址: ", &ptr)	// ptr的内存地址:  0xc0000d8020

	// 通过指针获取其指向的值
	fmt.Println("ptr指向的内存地址中的值:", *ptr)	// ptr指向的内存地址中的值: 18
}

```

```golang
func main()  {
	// 指针的四个细节
	// ① 可以通过指针改变指向的值
	var num int = 10
	fmt.Println(num)	// 10

	var ptr *int = &num
	*ptr = 20
	fmt.Println(num)	// 20

	// ② 指针变量接收的一定是地址值 不可以将变量直接赋给指针变量
	//var ptr *int = num	// 错误写法

	// ③ 指针变量的地址不可用不匹配
	//var ptr *float32 = &num	// 错误写法

	// ④ 基本数据类型(又叫值类型)，都有对应的指针类，形式为 *数据类型

}
```

##### 数组

1. 数组的地址数连续的。
2. 数组每个空间占用的字节数取决于数组类型。
3. 数组的长度属于其类型的一部分 `[3]int` 。
4. 数组属于值类型，在默认情况下是值传递，因此会进行值拷贝。
5. 如果想在其他函数中，修改原来的数组，可以使用应用传递(指针方式)。

```go
func array01() {
	// 定义一个数组
	var scores [5] int

	scores[0] = 95
	scores[1] = 92
	scores[2] = 93
	scores[3] = 91
	scores[4] = 90

	sum := 0
	for i := 0; i < 5; i++ {
		sum += scores[i]
	}
	// 平均数
	avg := sum / len(scores)
	fmt.Println(sum, avg)
}


func array02()  {
	// 定义一个数组
	var arr [3] int16
	// 数组的初始长度
	fmt.Println(len(arr))	// 3
	// 数组的初始值
	fmt.Println(arr)		// [0 0 0]
	// 证明 arr 中存储的是地址值
	fmt.Printf("arr 的地址为: %p \n", &arr)		// arr 的地址为: 0xc00000a098
	// 下标地址
	fmt.Printf("arr[0] 的地址为: %p \n", &arr[0])	// arr[0] 的地址为: 0xc00000a098
	fmt.Printf("arr[1] 的地址为: %p \n", &arr[1])	// arr[1] 的地址为: 0xc00000a09a
	fmt.Printf("arr[2] 的地址为: %p \n", &arr[2])	// arr[2] 的地址为: 0xc00000a09c
}


func array03() {
	var scores [5]int
	for i := 0; i < len(scores); i++ {
		fmt.Printf("请录入第%d个学生的成绩:", i + 1)
		fmt.Scanln(&scores[i])
	}

	sum := 0
	for i := 0; i < len(scores); i++ {
		sum += scores[i]
	}
	// 平均数
	avg := sum / len(scores)
	fmt.Println(sum, avg)

	fmt.Println("----------------------------")

	for key, val := range scores {
		fmt.Printf("key:%d value:%d \n", key, val)
	}
}
```

*数组的初始化方式：*

```go
func array04()  {
	// 数组初始化方式
	// ①
	var arr1 [3]int = [3]int{3, 6, 9}
	fmt.Println(arr1)	// [3 6 9]
	// ②
	var arr2 = [3]int{1, 4, 7}
	fmt.Println(arr2)	// [1 4 7]
	// ③
	var arr3 = [...]int{4, 5, 6, 7}
	fmt.Println(arr3)	// [4 5 6 7]
	// ④
	var arr4 = [...]int{2:66, 0:33, 4:55}
	fmt.Println(arr4)	// [33 0 66 0 55]
}

```

*数组的值传递：*

```go
func array05()  {
	var arr = [3]int{3, 6, 9}
	fmt.Println(arr)				// [3 6 9]
	fmt.Printf("%T \n", arr)		//  [3]int
	test(&arr)
	fmt.Println(arr)				// [7 6 9]
}

func test(arr *[3]int)  {
	arr[0] = 7
}
```

*二维数组内存分析：*

```go
func array06()  {
	// 定义一个二维数组
	var arr [2][3]int16
	fmt.Println(arr)	// [[0 0 0] [0 0 0]]
	fmt.Println(arr[0])
	fmt.Println(arr[0][1])
	fmt.Printf("arr 的地址: %p \n", &arr)	// arr 的地址: 0xc00000a0c0
	fmt.Printf("arr[0] 的地址: %p \n", &arr[0])	// arr[0] 的地址: 0xc00000a0c0
	fmt.Printf("arr[0][0] 的地址: %p \n", &arr[0][0])	// arr[0][0] 的地址: 0xc00000a0c0

	fmt.Printf("arr[1] 的地址: %p \n", &arr[1])	// arr[1] 的地址: 0xc00000a0c6
	fmt.Printf("arr[1][0] 的地址: %p \n", &arr[1][0])	// arr[1][0] 的地址: 0xc00000a0c6

	var arr1 = [2][3]int{{1, 4, 7}, {2, 5, 8}}
	fmt.Println(arr1)	// [[1 4 7] [2 5 8]]

	var arr2 = [3][3]int{{1, 4, 7}, {2, 5, 8}, {3, 6, 9}}
	fmt.Println(arr2)

	for key, items := range arr2 {
		for k, v := range items {
			fmt.Printf("arr[%d][%d]: %d \n", key, k, v)
		}
	}
}

```

##### 切片

1. 切片 `slice` 是对数组一个连续片段的引用，所以切片是一个引用类型。
2. 这个片段可以是整个数组，也可以是有起始和终止索引标识的一些项的集合。
3. 终止索引标识的项不包括在切片内，切片提供了一个相关数组的动态窗口。
4. 切片的底层是一个结构体，切片结构体包含三个部分 ①指向底层数组的指针；②切片长度；③切片容量。
5. 切片在使用时，不能越界。
6. 可以对切片进行再次切片。
7. 切片可以动态增长。

```go
func slice01()  {
	var arr = [6]int {1, 4, 7, 3, 6, 9}
	// 切片构建在数组上
	var slice []int = arr[1:3]
	fmt.Println(slice)	// [4 7]
	fmt.Println("slice元素个数:", len(slice))	// slice元素个数: 2
	fmt.Println("slice容量:", cap(slice))	// slice容量: 5
}

// slice02 切片内存分析
func slice02()  {
	var arr = [6]int {1, 4, 7, 3, 6, 9}
	// 切片构建在数组上
	var slice []int = arr[1:3]
	// 切片 底层是一个结构体
	// 切片结构体包含三个部分 ①指向底层数组的指针；②切片长度；③切片容量
	fmt.Printf("arr[1] 地址：%p \n", &arr[1])	// arr[1] 地址：0xc0000c2068
	// 切片的地址
	fmt.Printf("slice 地址: %p \n", slice)	// slice 地址: 0xc0000c2068
	fmt.Printf("slice 地址: %p \n", &slice)	// slice 地址: 0xc000098060
	fmt.Printf("slice[0] 地址: %p \n", &slice[0])	// slice 地址: 0xc0000c2068
}
```

*切片的初始化：*

```go
// slice03 切片的初始化
func slice03()  {
	// 直接定义一个切片 参数列表: 切片类型、切片长度、切片容量
	// make 在底层创建了一个数组，该数组对外不可见，无法对数组进行直接操作
	slice := make([]int, 4, 20)
	fmt.Println(slice)	// [0 0 0 0]
	fmt.Printf("切片的长度: %d \n", len(slice))	// 切片的长度: 4
	fmt.Printf("切片的容量: %d \n", cap(slice))	// 切片的容量: 20
	slice[0] = 88
	slice[1] = 66
	fmt.Println(slice)	// [88 66 0 0]

	// 直接定义一个切片
	slice2 := []int{1, 4, 7}
	fmt.Println(slice2)	// [1 4 7]
	fmt.Printf("切片的长度: %d \n", len(slice2))	// 切片的长度: 3
	fmt.Printf("切片的容量: %d \n", cap(slice2))	// 切片的容量: 3 
}

// slice04 切片的遍历
func slice04()  {
	slice := []int{66, 88, 99, 100}
	for k, v := range slice {
		fmt.Printf("slice[%v]:%v \n", k, v)
	}
}
```

*切片的扩容：*

```go
func slice06()  {
	var arr = [6]int {1, 4, 7, 3, 6, 9}
	slice := arr[1:4]
	fmt.Println(len(slice))	// 3
	fmt.Println(cap(slice))	// 5
	// 底层追加元素时 实际是对数组进行扩容 将原有数组中的值复制到新数组中 然后再追加新元素
	slice1 := append(slice, 99, 88)
	fmt.Println(slice1)	// [4 7 3 99 88]
	fmt.Printf("slice1 type: %T \n", slice1)	// slice1 type: []int

	slice = append(slice, 88, 90)
	fmt.Println(slice)	// [4 7 3 88 90]

	slice2 := arr[:3]
	slice = append(slice1, slice2...)
	fmt.Println(slice)	// [4 7 3 88 90 1 4 7]
}

```

*切片的拷贝：*

```go
func slice07()  {
	var slice = []int{1, 4, 7, 3, 6, 9}
	var slice2 = make([]int, 10)
	copy(slice2, slice)	// 将 slice 对应数组的元素复制到 slice2 对应的数组中
	fmt.Println(slice)	// [1 4 7 3 6 9]
	fmt.Println(slice2)	// [1 4 7 3 6 9 0 0 0 0]
}
```

##### map

1. 映射，它将键值对进行关联，类似于其他语言的集合。
2. `map` 集合使用前一定要 `make`。
3. `map` 的 key-value 是无序的。
4. key 是不可以重复的，后续对重复的key进行赋值会覆盖已有的key对应的值。
5. `make` 函数的第二个参数 size 可以省略，默认分配一个内存。
6. `delete(map, key)` 删除map中的一个元素。
7. `len()` 可用于获取 `map` 的长度。
8. `map` 只可使用 `for...range...` 的形式进行遍历。

```go
func map01()  {
	// 定义 map
	// ①
	var a map[int]string
	// 只声明map是不会分配内存空间的
	// 必须通过 make 函数初始化 才会分配内存空间
	a = make(map[int]string, 10)
	a[1] = "Jack"
	a[2] = "Rose"
	a[3] = "Bob"
	fmt.Println(a)	// map[1:Jack 2:Rose 3:Bob]

	// ②
	b := make(map[int]string)
	b[1] = "张三"
	b[2] = "李四"
	fmt.Println(b)	// map[1:张三 2:李四]

	// ③
	c := map[int]string{
		1 : "Hello",
		2 : "World",
		3 : "!",
	}
	fmt.Println(c)	// map[1:Hello 2:World 3:!]
}
```

*map的增删改查：*

```go
func map02()  {
	b := make(map[int]string)
	b[1] = "张三"
	b[2] = "李四"
	fmt.Println(b)	// map[1:张三 2:李四]
	// 修改
	b[1] = "王五"
	fmt.Println(b)	// map[1:王五 2:李四]
	// 删除
	delete(b, 1)
	fmt.Println(b)	// map[2:李四]
	// 查看元素
	value, flag := b[1]
	fmt.Printf("value: %T \n", value)	// string
	fmt.Println(value, flag)	// '', false

	// 清空一个 map
	// ① 变量删除
	// ② make 一个新的 map
}
```



#### 2.3 运算符

1. 算术运算符：`+ - * / % ++ --`
2. 赋值运算符：`= += -= /= %=`
3. 关系运算符：`== != > < >= <=`
4. 逻辑运算符：`&& || !`
5. 位运算符：`& | ^`
6. 其他运算符：`& *`

#### 2.4 流程控制

**if 条件语句**

```go
func main()  {
	var count int
	fmt.Println("请输入 count 的值:")
	_, err := fmt.Scanln(&count)
	if err != nil {
		return
	}
	if count < 20 {
		fmt.Println("存量不足")
	} else {
		fmt.Println("存量充足")
	}
	// if 后可以并列加入变量的定义
	if rate := 1.25;int(float64(count) * rate) > 25 {
		fmt.Println("存量充足")
	} else if int(float64(count)*rate) < 25 {
		fmt.Println("存量不足")
	} else {
		fmt.Println("存量紧张")
	}
}

```

**switch条件语句**

```go
func main()  {
	// switch 后是一个 表达式(即：常量值、变量、一个有返回值的函数都可以)
	// case 后的各个值的数据结构，必须和 switch 的表达式数据类型一致
	// case 后可以带多个表达式，使用逗号间隔
	// case 后面的表达式如果是常量值(字面量)，则要求不能重复
	// case 后面不需要带 break 程序匹配到一个case后会自动执行 break，匹配不到则执行 default
	// default 语句不是必须的
	// switch 后也可以不带表达式 当做 if 分支来
	// switch 后也可以直接声明/定义一个变量，分号结束，不推荐
	// switch 穿透，利用 fallthrough 关键字，如果 case 语句块后增加了 fallthrough，则会继续执行下一个 case
	var score int
	fmt.Println("请输入 count 的值:")
	_, err := fmt.Scanln(&score)
	if err != nil {
		fmt.Println(err.Error())
		return
	}
	level := score / 10
	switch level {
	case 10:
		fmt.Println("完美")
	case 9:
		fmt.Println("卓越")
	case 8:
		fmt.Println("优秀")
	case 7:
		fmt.Println("良好")
	case 6, 5, 4:
		fmt.Println("努力")
		fallthrough
	case 3:
		fmt.Println("加油")
	default:
		fmt.Println("异常")
	}
}
```

**for循环**

```go
func main()  {
	var sum int
	// for 的初始表达式不能使用 var 的方式定义变量 需使用 :=
	for i := 1; i <= 5; i++ {
		sum += i
	}
	fmt.Println(sum)

	// 格式灵活的 for 循环
	j := 1
	for j < 5 {
		fmt.Println(j)
		j++
	}
	// 死循环
	var score int
	for  {
		fmt.Println("请输入 count 的值:")
		_, err := fmt.Scanln(&score)
		if err != nil {
			fmt.Println(err.Error())
			return
		}
		if score > 10 {
			fmt.Println("死循环结束")
			break
		}
	}
    
    // for-range 结构是Go语言特有的一种迭代结构
	// for-range 可以遍历数组、切片、字符串、map以及通道
	// for-range 语法上类似于其他语言的 foreach 语句
	var str string = "Hello Golang!"
	for key, val := range str {
		fmt.Printf("key:%d, value:%c \n", key, val)
	}
}
```

#### 2.5 关键字

1. `break`：跳出当前循环
2. `continue`：结束本次循环，进入下一轮循环，也可以结合 `label` 使用
3. `goto`：结合 `label` 进行程序跳转，不建议使用
4. `return`：结束当前函数，返回返回值

```go
func main()  {
	// break 结合 label 跳出多重循环
	label1:
		for i := 1; i <= 5; i++ {
			label2:
				for j := 1; j <= 5; j++ {
					fmt.Printf("i: %v, j: %v", i, j)
					if i == 2 && j == 2 {
						break label1
					} else if i == 3 {
						break label2
					}
				}
		}
}
```

##### defer

*在函数中，为了保证函数执行完毕后及时的释放资源，Go提供了 defer 关键字。*

1. `defer` 关键字后的语句在被压入栈中时，同时会将相关的值拷贝入栈，不会因为后续函数的变化而变化。
2. `defer` 的延迟执行机制，可以非常简洁的处理资源释放。

```go
func main()  {
	fmt.Println(add(30, 60))	// 230
}

func add(num1 int, num2 int) int  {
	// Go语言中 defer 关键字后的语句不会立即执行
	// 而是会被压入一个栈(先进后出)中 函数执行完毕后 依次出栈
	defer fmt.Println("num1=", num1)	// 60
	defer fmt.Println("num2=", num2)	// 30

	num1 += 90
	num2 += 50

	var sum int = num1 + num2
	fmt.Println("sum=", sum)	// 230
	return sum
}
```

##### defer-recover 错误处理机制

> 内建函数 `recover` 允许程序管理恐慌过程中的Go程。在 `defer` 的函数中，执行 `recover` 调用会取回传至 `panic` 调用的错误值，恢复正常执行，停止恐慌过程。若 `recover` 在 `defer` 的函数之外被调用，它将不会停止恐慌过程序列。在此情况下，或当该Go程不在恐慌过程中时，或提供给 `panic` 的实参为 `nil` 时，`recover` 就会返回 `nil` 。



> 内建函数 `panic` 停止当前Go程的正常执行。当函数F调用 `panic` 时，F的正常执行就会立刻停止。F中 `defer` 的所有函数先入后出执行后，F返回给其调用者G。G如同F一样行动，层层返回，直到该Go程中所有函数都按相反的顺序停止执行。之后，程序被终止，而错误情况会被报告，包括引发该恐慌的实参值，此终止序列称为恐慌过程。





```go
func main()  {
	//test(10, 0)	// panic: runtime error: integer divide by zero
	//test(10, 2)
	division(10, 0)	// 当前错误已捕获: runtime error: integer divide by zero

}

func division(num1,num2 int) {
	// 捕获错误
	defer func() {
		err := recover()
		// 没有捕获到错误返回 nil
		if err != nil {
			fmt.Println("当前错误已捕获:", err)
		}
	}()

	result := num1 / num2
	fmt.Println(result)
}
```

```go
func main()  {
	//test(10, 0)	// panic: runtime error: integer divide by zero
	//test(10, 2)
	err := test(10, 0)	// 当前错误已捕获: runtime error: integer divide by zero
	if err != nil {
		fmt.Println("自定义错误:", err)	// 自定义错误: 除数不能为 0
		panic(err)	// 后续程序不会执行
	}
}

func test(num1,num2 int) error {
	// 抛出自定义错误
	if num2 == 0{
		return errors.New("除数不能为 0")
	} else {
		result := num1 / num2
		fmt.Println(result)
		return nil
	}
}
```



#### 2.6 函数

1. 函数可提高代码的复用性，减少代码的冗余，提高代码的维护性。
2. 函数是为了完成某一功能的程序指令(语句)的集合。
3. Go语言中函数不支持重载。
4. Go语言支持可变参数。
5. 基本数据类型和数组默认是值传递，即进行值拷贝。在函数内修改，不会影响原来的值。
6. 以值传递方式的数据类型，如果希望在函数内的变量能够修改函数外的变量，可以传入变量的地址 `&`， 函数以指针的方式操作变量。从何效果上来看类似引用传递。
7. Go语言中函数也是一种数据类型，可以赋值给一个变量，则该变量就是一个函数类型的变量。通过该变量可以对函数进行调用。
8. 函数既然是一种数据类型，因此在Go中，函数可以作为形参，并且调用。
9. 为了简化数据类型定义，Go支持自定义数据类型。
10. Go语言支持对函数返回值命名。



*函数遵循标识符命名规范：*

1. 首字母不能是数字。
2. 首字母大写该函数可以被本包文件和其他包文件使用 `public`。
3. 首字母小写只能被本包文件使用，其他包文件不能使用 `private`。





```go
func main()  {
	// 调用数值交换函数
	var num1, num2 int = 10, 20
	fmt.Printf("交换前:num1-%v, num2-%v \n", num1, num2)	// 交换前:num1-10, num2-20
	exchangeNum(num1, num2)
	fmt.Printf("交换后:num1-%v, num2-%v \n", num1, num2)	// 交换后:num1-10, num2-20
}

func exchangeNum(num1 int, num2 int)  {
	num1, num2 = num2, num1
}
```

```go
func main()  {
	test()
	test(1, 2, 3)
	test(1, 3, 4)
}

// test 定义一个函数，其参数为可变参数：...
// args..int 支持传入任意多个数量的 int 类型的数据
// 函数内部处理可变参数时，将其当做切片来处理
func test(args...int)  {
	for i := 0; i < len(args); i++ {
		fmt.Println(args[i])
	}
}

```

```go
// 引用传递
func main()  {
	var num int = 10
	changeNum02(&num)
	fmt.Println("main:", num)	// main: 20
}

func changeNum02(num *int)  {
	*num = 20
	fmt.Println("test:", num)	// test: 0xc000014088
}
```

```go
// 函数可赋值给变量且可作为形参
func main()  {
	a := funcType
	fmt.Printf("a 的类型是:%T \n", a)	// a 的类型是:func(int)
	fmt.Printf("funcType 函数的类型是:%T \n", funcType)	// funcType 函数的类型是:func(int)
	a(10086)	
	funcParams(10, 20, funcType)	// uncParams
}

func funcType(num int)  {
	fmt.Println(num)	// 10086
}

func funcParams(num1 int, num2 int, funcParams func(int2 int))  {
	fmt.Println("funcParams")
}

```

```go
// 自定义类型
func main()  {
	type myInt int	// 相当于给 int 类型取别名
	var num1 myInt = 30
	fmt.Println("num1:", num1)	// num1: 30
	fmt.Printf("num1 的类型:%T \n", num1)	// um1 的类型:main.myInt
}

type myFunc func(int)

func functionType(num1, num2 int, testFunc myFunc)  {

}

```

```go
// 命名函数返回值
func main()  {
	res1, res2 := defineRes(10, 20)
	fmt.Println(res1, res2)	// 30 10
}

func defineRes(num1, num2 int) (res1, res2 int)  {
	res1 = num1 + num2
	res2 = num2 - num1
	return res1, res2
}
```

##### init 函数

1. 初始化函数，类似于其他语言的 `construct` 函数；
2. 每个源文件都可以包含一个 `init` 函数，该含税会在 `main` 函数执行前被 Go 调用；
3. 包的执行顺序：①全局变量的定义；② `init` 函数的调用；③`main`函数的调用。
4. 多个源文件有 `init` 函数时，导包的时候该包的全局变量和 `init` 函数会先后执行。

```go
package main

import (
	"fmt"
	"goCode/Chapter01/basicGrammer/09function/testInit"
)

var num int = test()

func test() int  {
	fmt.Println("Here is test function...")
	return 0
}

func init()  {
	fmt.Println("Here is init function...")
}

func main()  {
	fmt.Println("Here is main function...")
	fmt.Printf("Name:%v", testInit.Name)
}

/*
Here is test init function...
Here is test function...
Here is init function...
Here is main function...
Name:Liming
*/
```

```go
package testInit

import "fmt"

var Name string

func init()  {
	fmt.Println("Here is test init function...")
	Name = "Liming"
}

```

##### 匿名函数

1. Go 支持匿名函数，当函数仅需使用一次时，可以考虑使用匿名函数；
2. 匿名函数的使用方式：①在定义匿名函数时直接调用；②将匿名函数赋个一个变量(匿名变量)；
3. 将一个匿名函数赋给一个全局变量即可让该匿名函数在整个程序有效。

```go
package main

import "fmt"

var subResult = func(num1 int, num2 int) int{
	return num2 - num1
}

func main()  {
	result := func(num1 int, num2 int) int{
		return num1 + num2
	}(10, 20)

	fmt.Println(result)	// 30

	// 调用匿名函数
	subRes := subResult(10, 20)
	fmt.Println(subRes)	// 10
}
```

##### 闭包

*闭包就是一个函数与其相关的引用环境组合的一个整体。*

1. 闭包的本事依旧是一个匿名函数，只是这个函数引入了外界的变量/参数；
2. 匿名函数中引入的变量会一直保存在内存中，可以一直使用；
3. 闭包返回的是一个匿名函数，但是这个匿名函数引入了函数外的变量/参数。
4. 匿名函数使用的变量会一直保存在内存中，所以闭包不可滥用。



```go
package main

import "fmt"

// 函数功能：求和
// 函数的返回值为一个函数，这个函数的有一个int类型的参数 返回值也是 int 类型
func getSum() func(int) int  {
	var sum int
	return func(num int) int {
		sum = sum + num
		return sum
	}
}

func main()  {
	f := getSum()
	fmt.Println(f(1))	// 1
	fmt.Println(f(2))	// 3
	fmt.Println(f(3))	// 6
}
```

##### 常用系统函数

**字符串函数**

| 常用字符串函数                                               | 释义                                                |
| ------------------------------------------------------------ | --------------------------------------------------- |
| `len(str)`                                                   | 统计字符串的长度，按字节进行统计。                  |
| `r := []rune(str)`                                           | 字符串遍历                                          |
| `n, err := strconv.Atoi("66")`                               | 字符串转整数                                        |
| `strconv.Itoa(66)`                                           | 整数转字符串                                        |
| `strings.Contains("PHP&JAVA&GO", "GO")`                      | 查找子串是否在指定的字符串中                        |
| `strings.Count("JAVA", "A")`                                 | 统一一个字符串有几个指定的子串                      |
| `strings.EqualFold("go", "GO")`                              | 部分大小写进行字符串比较                            |
| `strings.Index("java", "a")`                                 | 返回子串在字符串第一次出现的索引值，没有则返回 `-1` |
| `strings.Replace("goalngjavago", "go", "golang", -1)`        | 字符串的替换 `-1` 表示全部替换                      |
| `strings.Split("Go-PHP-JAVA-Python", "-")`                   | 按照指定的某个字符对字符串进行分隔                  |
| `strings.ToLower("GOLANG")` `strings.ToUpper("golang")`      | 将字符串的字母进行大小写的转换                      |
| `strings.TrimSpace("  Golang and Java ")`                    | 将字符串左右的空格进行过滤                          |
| ` strings.Trim("~Golang~", "~")` `strings.TrimRight("~Golang~", "~")` `strings.TrimLeft("~Golang~", "~")` | 将字符串左右俩边的指定字符去掉                      |
| `strings.HasPrefix("https://baidu.com", "http")`             | 判断字符串是否以指定的字符串开头                    |
| `strings.HasSuffix("head.jpg", "jpg")`                       | 判断字符串是否以指定的字符串结束                    |



**日期时间函数**

| 常用日期时间函数                    | 释义                                 |
| ----------------------------------- | ------------------------------------ |
| `now := time.Now()`                 | 获取当前时间                         |
| `now.Year()`                        | 年                                   |
| `now.Month()`                       | 月                                   |
| ` now.Weekday()`                    | 周                                   |
| `now.Day()`                         | 日                                   |
| `now.Hour()`                        | 时                                   |
| `now.Minute()`                      | 分                                   |
| `now.Second()`                      | 秒                                   |
| `now.Format("2006-01-02 15:04:05")` | 日期时间格式化 `2006-01-02 15:04:05` |



##### 内置函数

*内置函数无需导包，可以直接使用。*

| 常用内置函数 | 释义                                                         |
| ------------ | ------------------------------------------------------------ |
| `len()`      | 获取相关类型的长度(数组、数组指针、切片、映射、字符串、通道) |
| `new()`      | 用于分配内存(主要分配值类型：int、float、bool、string、数组和结构体struct)，其第一个实参为类型，而非值。其返回值为指向该类型的新分配的零值的指针。 |
| `make()`     | 分配并初始化一个类型为切片、映射、或通道的对象。其第一个实参为类型，而非值。`make`的返回类型预期参数相同，而非指向它的指针。其具体结果取决于具体的类型。 |



#### 2.7 包的引入

1. 一个目录/包下不能有同名函数(Go不支持函数重载)；
2. 包名可以和目录名不一致，但建议一致；
3. 一个目录下的同级文件同属于一个包，可以理解为一个目录即为一个包；
4. 包允许使用别名 `import print "fmt"`



```go
// package 进行包的申明 包名建议与所在文件夹一致
// main 包是程序的入口包 一般 main 函数会放在这个包下
package main

// 包名时从 $GOPATH/src/ 后开始计算的 使用 / 进行路径分隔
//import "fmt"
//import "goCode/Chapter01/basicGrammer/08package/dbutils"

// 一次性导入多个包
import (
	"fmt"
	"goCode/Chapter01/basicGrammer/08package/dbutils"
)

// main 函数必须得放在 main 包下，否则无法编译执行
func main()  {
	fmt.Println("Hello, here is main package!")
	// 调用外部包时需先引入
	// 且函数名首字母大写 才能被外部包引用/导入
	dbutils.GetConn()
}

```

```go
package dbutils

import "fmt"

func GetConn()  {
	fmt.Println("Hello, you will get the database connection...")
}

```

**本地包的导入：**

1. `import` 导入语句通常放在文件开头包声明语句下方。
2. 导入的包名需要使用双引号 `""` 包裹起来。
3. 包名是从 `$GOPATH/src/` 后开始计算的，使用 `/` 进行路径分隔。

> 1. go1.16.6版本下设置 `GOPATH` 后引入本地包报错 `... is not in GOROOT`；解决办法：执行  `go env -w GO111MODULE=off`



#### 2.8 扩展

**获取用户终端输入**

```go
func main()  {
	// ① Scanln 类似Scan，但会在换行时才停止扫描。最后一个条目后必须有换行或者到达结束位置。
	// 录入数据时 类型必须匹配 Go语言会对类型进行片段
	var age int
	//fmt.Println("请录入学生的年龄：")
	//fmt.Scanln(&age)	// 通过 Scanln 对 age 进行赋值
	//
	var name string
	//fmt.Println("请录入学生的姓名：")
	//fmt.Scanln(&name)	// 通过 Scanln 对 name 进行赋值
	//
	var score float32
	//fmt.Println("请录入学生的成绩：")
	//fmt.Scanln(&score)	// 通过 Scanln 对 score 进行赋值
	//
	var isVip bool
	//fmt.Println("请录入学生是否为VIP：")
	//fmt.Scanln(&isVip)	// 通过 Scanln 对 isVip 进行赋值
	//
	//// 输出结果
	//fmt.Printf("姓名：%v;年龄为：%v;成绩：%v;是否Vip：%v", name, age, score, isVip)

	// ② Scanf 从标准输入扫描文本，根据 format 参数指定的格式将成功读取的空白分隔的值保存进成功传递给本函数的参数。返回成功扫描的条目个数和遇到的任何错误。
	fmt.Println("请录入学生的姓名、年龄、成绩、是否为VIP，使用空格进行分隔：")
	_, err := fmt.Scanf("%s %d %f %t", &name, &age, &score, &isVip)
	if err != nil {
		fmt.Println(err.Error())
		return
	}

	// 输出结果
	fmt.Printf("姓名：%v;年龄为：%v;成绩：%v;是否Vip：%v", name, age, score, isVip)
}

```




