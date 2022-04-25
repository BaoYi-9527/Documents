### Composer 

#### 引用地址

1. [Composer 中文文档 - learnku](https://learnku.com/docs/composer/2018)


#### 1.引言

Composer 是一个用于PHP依赖管理的工具，该工具可以帮助我们声明项目所依赖的库，并帮助我们完成安装/更新过程。

**系统要求：**
1. PHP版本 5.3.2 以上
2. 需要对PHP做一些设置和编译标志

##### 安装过程

#### 2.基本使用

使用Composer的项目会有一个 `composer.json` 文件，该文件描述了项目的依赖关系和其他元数据。

##### 2.1 require 键

1. `require` 键会告诉composer该项目所依赖的包有哪些；
2. `require` 获取一个包名称和其映射的版本约束的json对象；
3. 包名称由供应商名称和项目名称组成；
4. 包版本约束：`1.0.*` 表示将匹配 `1.0 ~ 1.1` 的版本

> composer 会使用 `require` 信息，首先从 `repositories` 键中所指定的仓库去寻找合适的文件，若没有指定则会在 Packagist 默认库中寻找。

```json
{
    "require": {
        "monolog/monolog": "1.0.*"
    }
}
```

##### 2.2 安装依赖关系

使用 `install` 命令可以为该项目安装已经定义好的依赖关系：

```bash
php composer.phar install
```

该命令下的，`composer` 会根据情况通过以下俩种方式中的一种进行安装：

*①非 composer.lock 安装*

1. 此前从未运行过 composer 就不会出现 `composer.lock` 文件；
2. 该情况下 composer 会解析在 `composer.json` 文件中列出的依赖关系并且下载最近版本到项目下的 `vendor` 目录中（`vendor` 是项目中存放第三方代码的常规目录）；
3. 当 composer 完成安装后，其会将所有下载的包和其确切的版本信息写入到 `composer.lock` 文件，以此来锁定项目中第三方包的版本；
4. 建议将 `composer.lock` 文件放在项目仓库中，以方便所有项目成员都能锁定相同的包版本。

*②使用 composer.lock 文件安装*

1. 此前已经使用过 `install` 命令，或者项目其他成员使用过该命令导致项目中已存在 `composer.lock` 文件；
2. 该情况下 composer 同样会解析并安装 `composer.json` 文件中锁列出的依赖，区别在于composer会严格使用 `composer.lock` 文件中列出的版本来确保项目中所有成员所安装的版本是一致的；
3. `composer.json` 文件中列出的所有文件，但是与此同时它们可能不是最新的可用版本。

*提交 composer.lock 文件至版本控制工具*

1. 将该文件提交至 `VC` 是非常必要的，其可以保证所有项目成员使用的是完全一致的依赖；

##### 2.3 更新依赖到最新版本

1. 一般情况下 `composer.lock` 文件会阻止项目获取自动获取最新的依赖版本；
2. 若需要更新依赖到最新版本，可以使用 `update` 命令（该命令会获取最新匹配的版本并将新版本更新至 `composer.lock` 文件）；

```bash
php composer.phar update
# 安装或更新部分依赖
php composer.phar update monolog/monolog [...]
```

##### 2.4 Packagist

`Packagist` 是composer的主要资源库，相当于一个公共的官方仓库。

##### 2.5 平台包

composer 将那些已经安装在系统上，但并不是由 composer 安装的包视为虚拟的平台包（包括PHP本身、PHP扩展和一些系统库）：

1. `php` 代表使用的 PHP 版本要求，允许应用限制：`"php": ">=7.0"`、`"php": "^7.1"`、`"php": "php-64bit"`；
2. `hhvm` 代表 HHVM 运行环境的版本，并且允许应用限制：`"hhvm": "^2.3"`；
3. `ext-<name>` 允许依赖 PHP 扩展（包括核心扩展）。通常PHP扩展的版本可能是不一致的，一般将其版本约束为 `*`：`"ext-gd": "*"`；
4. `lib-<name>` 允许对 PHP 库的版本进行限制：`curl`、`iconv`、`icu`、`libxml`、`openssl`、`pcre`、`uuid`、`xsl`；

##### 2.6 自动加载

为了描述包的自动加载信息，composer 会生成一个 `vendor/autoload.php` 文件，在项目中可以简单的 `include` 该文件，并无需其他额外工作的情况下就可以使用这些包所提供能的类：

```php
require __DIR__.'vendor/autoload.php';

// your code...
$log = new MongoLog\Logger('name');
```

*此外，还可以在 composer.json 中添加一个 autoload 指令，来添加自己的自动加载声明：*

```json
{
    "autoload": {
        "psr-4": {"Acme\\": "src/"}
    } 
}
```

1. composer 会为 `Acme` 命令空间注册一个 PSR-4 的自动加载。
2. 其定义了一个命名空间指向目录的映射（`vendor`目录同级的 `src` 目录将成为项目的根目录）；
3. 添加 autoload 指令后，必须重新运行 `composer dump-autoload` 来重新生成 `vendor/autoload.php` 文件；

#### 3.创建扩展包

##### 3.1 每个项目都是一个扩展包

1. 项目目录下拥有 `composer.json` 文件，则该目录为一个包；
2. 当该该项目添加 `require` 依赖时，相当于正在创建一个依赖于其他包的包；
3. 项目和扩展包的区别在于，项目是一个没有名称的包；
4. 为了使该包可安装，可为项目指定一个名称，在 `composer.json` 中添加 `name` 属性；

```json
{
    "name": "acme/hello-world",
    "require": {
        "monolog/monolog": "1.0.*"
    }
}
```

##### 3.2 库（包）版本

1. 大多数情况下，使用版本控制系统（git、svn、hg...）来维护库（包）;
2. 版本控制系统下，composer 会从`VCS`中推断出版本，所以不应该在 `composer.json` 中将版本写死；
3. 但若时手动维护，则需要通过在 `composer.json` 文件中添加一个 `version` 值来明确指定版本；

```json
{
    "version": "1.0.0"
}
```

*VCS版本：*

1. composer 使用的 VCS 分支和标记分支可以解决在 `require` 字段中指定的版本约束；
2. 当有可用版本时，composer 会查看所有的标记和分支，并将名称转换为内部可选列表，然后根据提供的版本约束进行匹配；

#### 4.Composer 命令使用

*命令简单概览：*[composer命令简单概览思维导图](https://www.processon.com/view/link/6135e5a55653bb2d6d2b7c6a)

**常用命令：**
```bash
# init 简便创建扩展包：
php composer.phar init
# install 该命令会读取当前目录下 composer.json 文件，解决依赖关系，并把它们安装到 vendor 文件下。
php composer.phar install
# update 获取最新版本的依赖以升级 composer.lock 文件
php composer.phar update 
# require 将新的依赖添加到当前目录的 composer.json 文件中 
php composer.phar require ...
# remove 移除 composer.json 中的扩展(依赖)
php composer.phar remove ...
# search 搜索当前项目下的包仓库
php composer.phar search ...
# show 列出是所有可用包(扩展)
php composer.phar show ...
# outdated 列出已安装的扩展包是否可更新，包括当前版本和最新版本
php composer.phar outdated | php composer.phar show -lo
# validate 检查 composer.json 文件是否合法
php composer.phar validate
# status 检查依赖是否有改变
php composer.phar status
# self-update 升级 composer 到最新版本
php composer.phar self-update
# create-project 使用 composer 从已存在的项目创建新的项目
php composer.phar create-project
# dump-autoload 为 classmap 中新的类名生成自动加载
php composer.phar dump-autoload
# clear-cache 从 composer 的缓存目录中删除一切
php composer.phar clear-cache
```

#### 5.composer.json 文件完全解析

##### 5.1 属性

```json
{
    "name": "名称，包的名称，由作者名称和项目名称组成，使用 / 分隔",
    "description": "包的简短描述",
    "version": "包的版本，大多数情况下非必要",
    "type": "类型，默认类型为 library(库)，另有 project（项目）、metapackage（空包）、composer-plugin",
    "keyword": "一组用于搜索与筛选的与包相关的关键字",
    "homepage": "项目网站的URL地址",
    "readme": "README文档的绝对路径",
    "time": "版本发布日期",
    "license": "包的许可证，常见许可证：Apache-2.0、MIT...",
    "authors": "包的作者，包含属性：name、email、homepage、role",
    "support": "获取对项目支持的信息对象"
}
```

##### 5.2 包链接

1. 通过版本限制将包名称映射到包的版本上；
2. `require`和`require-dev` 支持指定项目成功运行所需的 PHP 版本和PHP扩展；
3. `require`:必须安装的依赖包列表；`require-dev`：开发或运行测试时的依赖包列表；

```json
{
    "require": {
        "monolog/monolog": "1.0.*",
        "php": "^5.5 || ^7.0",
        "ext-mbstring": "*"        
    }     
}
```

##### 5.3 autoload

PHP自动加载的映射，支持 `PSR-4` 和 `PSR-0`自动加载，`class` 映射和 `files`引用。

1. **PSR-4：** 使用该类型的自动加载，可以自定义从命名空间到路径的映射；
2. **PSR-0：** 使用该类型的自动加载，可以自定义相对于包的根目录，命名空间到路径的映射；
3. **classmap：** 在安装或更新的时候，`classmap` 被组合成单个键值对数组的形式，这个数组可以在 `vendor/composer/autoload_classmap.php`中找到。这个映射是通过扫描给定的 `文件夹/文件` 中所有的 `.php` 和 `.inc` 来生成的。可以通过 `classmap` 生成的类映射来加载不遵循 PSR-0/4 的库。
4. **files：** 若你想在每个请求中都引入某个文件，可以使用 `files` 自动加载机制(帮助函数的实现)；
5. **exclude-from-classmap：** 类映射生成器将忽略此处配置的路径中的所有文件夹。

##### 5.4 其他属性

1. **config：** 一组配置项，仅用于项目。
2. **script：** composer 允许在安装过程的各个部分中执行脚本。
3. **extra：** scripts 使用的任意扩展数据