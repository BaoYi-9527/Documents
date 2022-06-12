## PHPStorm 配置 Xdebug 调试

### Reference

+ [使用 PHPStorm 进行调试（PHPStorm 官方手册 2021.1版本）](https://www.jetbrains.com/help/phpstorm/2021.2/debugging-with-phpstorm-ultimate-guide.html)
+ [Xdebug 下载安装指南（官网地址）](https://xdebug.org/docs/install)
+ [Xdebug 下载地址 ](https://xdebug.org/download)
+ [Xdebug 配置相关](https://xdebug.org/docs/all_settings)

### 环境

+ `PHPStorm 2021.1.4`
+ `PHP 7.3.4`
+ `Xdebug 3.1.5`

### 配置步骤

**查看PHP相关信息**

```php
<?php
	
echo phpinfo();
```

*PS：所有的文件路径都为了隐藏一些不必要的信息将根目录写为了 `..` 的格式，不要忘记改为自己的了*



①主要关注俩个信息：`PHP Version: 7.3.4`、`Thread Safety：disabled` 。这个俩个信息决定了我们 `Xdebug` 的下载版本，PHP版本就不说了，64位还是32位的问题也请自行分辨，主要是 `Thread Safety` 为 `disabled` 就选择 `NTS` 版本，`enabled` 就选择 `TS` 版本。这里的版本对应相当重要！！！这里有个奇淫巧技是可以直接将 `phpinfo()` 打印出来的网页源码放到 [xdebug 网站](https://xdebug.org/wizard) 上去解析出对应的 `xdebug` 版本。



②将下载的 `php_xdebug-3.1.5-7.3-vc15-nts-x86_64.dll` 文件移动到对应的PHP版本的扩展目录下 `..\php\php7.3.4nts\ext`。



③编辑 `..\php\php7.3.4nts\php.ini` 文件，拉到最下面新增 `zend_extension=xdebug-3.1.5-7.3-vc15-nts-x86_64`

这里可以关注几个信息(仅仅关注，不一定需要改)

+ PHP扩展目录地址 `extension_dir="..\php\php7.3.4nts\ext"` 是否和放置 Xdebug dll 文件的地址一致
+ 可以看到 `ini` 文件中引入扩展的方法都是 `extension=curl` 的形式（前面没有注释 `;`），而扩展文件的命名为 `php_curl.dll`，于是扩展文件的具体引入方式我们大概就了解了，引入 Xdebug 的方式也就是添加 `zend_extension=xdebug-3.1.5-7.3-vc15-nts-x86_64` ，至于为什么是 `zend_extension` 而不是 `extension` 是因为 Xdebug 是基于 zend 引擎的，而不是 PHP 引擎的扩展。

最后我在 `php.ini` 添加了如下配置：

```ini
;Xdebug 相关配置
[xdebug]
# 这里是放置到 ext 中的扩展文件名，我这里更改了文件名，不改的话就是
# xdebug-3.1.5-7.3-vc15-nts-x86_64
zend_extension=xdebug-3.1.5		
xdebug.client_host=localhost
;默认是9003
xdebug.client_port=9100
xdebug.mode=debug
;日志的配置相当重要 这决定了你在遇到问题时去哪里寻找答案
xdebug.log=..\php\xdebug.log
```



④查看 Xdebug 是否成功 安装

+ 命令行方式

  ```php
  $> php -v
  PHP 7.3.4 (cli) (built: Apr  2 2019 21:57:22) ( NTS MSVC15 (Visual C++ 2017) x64 )
  Copyright (c) 1997-2018 The PHP Group
  Zend Engine v3.3.4, Copyright (c) 1998-2018 Zend Technologies
      with Xdebug v3.1.5, Copyright (c) 2002-2020, by Derick Rethans	# 可以看到 Xdebug 扩展已经成功加载
  ```

+ `echo phpinfo();` 直接查看有没有 Xdebug 相关的信息，注意要重启 Nginx 服务



⑤ PHPStorm 配置 `设置->PHP->CLI 解释器` 设置为对应的解释器， 这里是 `..\php\php7.3.4nts\php.exe`。`设置->PHP->调试->Xdebug:调试端口` 设置为和上面的 `xdebug.client_port` 一致即可，值得注意的是在 `设置->PHP->调试->预调试` 处，你是可以验证 xdebug 调试是否可以在 PHPstorm 中正常运行的。



### 避坑

①如果你是使用的接口进行测试/部署的，那么请在更改 `php.ini` 文件后务必重启 Nginx 服务以更新PHP配置，至于为什么请自行了解PHP和Nginx是怎么交互的。

②可能会出现端口被占用的情况，例如 `9000 is too busy` 之类的情况，这时候更改一下 `xdebug.client_port` 的值是一个很好的选择。

③可能会遇到一切都配置无误，但是打断点后没有反应的情况，此时就是日志起作用的时候了：

```tex
[13716] Log opened at 2022-06-12 12:59:50.547344
[13716] [Config] INFO: Trigger value for 'XDEBUG_TRIGGER' not found, falling back to 'XDEBUG_SESSION'
[13716] [Config] INFO: Trigger value for 'XDEBUG_SESSION' not found, so not activating
[13716] Log closed at 2022-06-12 12:59:50.799754
```

出现这个问题是因为我们配置的 `xdebug.mode=debug` 是 debug 模式。查看 [Xdebug 配置：start_with_request](https://xdebug.org/docs/all_settings#start_with_request) 关于 `xdebug.start_with_request` 的介绍可以发现，当 `xdebug.mode=debug` 时，`xdebug.start_with_request=trigger`， 而 `trigger` 仅在请求开始时存在特定触发器时才被激活。因此此时需要设置 `xdebug.start_with_request=yes` 进行调试。

























