### Connection refused

最近使用 `git` 拉取 Github 上的项目时老是出现 `Connection refused` ：

```bash
$ git pull origin master
ssh: connect to host github.com port 22: Connection refused
fatal: Could not read from remote repository.

Please make sure you have the correct access rights
and the repository exists.
```

**网上的解决办法**

*reference:[解决ssh: connect to host github.com port 22: Connection refused](https://blog.csdn.net/qq_34258344/article/details/124674209)*

```bash
vim ~/.ssh/config
```

```text
Host github.com  
User xxxxx@xx.com  
Hostname ssh.github.com  
PreferredAuthentications publickey  
IdentityFile ~/.ssh/id_rsa  
Port 443
```

但是再使用了这个方法后依然无法解决：
```bash
$ git pull origin master
ssh: connect to host ssh.github.com port 443: Connection refused
fatal: Could not read from remote repository.

Please make sure you have the correct access rights
and the repository exists.
```

然后看见隔壁工位上大哥的搜索栏上赫然也是这个问题，交流一番应该是最近墙比较敏感的问题。
解决方法也从大哥那里找到了，不得不说大哥YYDS，我还是太年轻了：
1. 首先到 [ipaddress](https://www.ipaddress.com/) 输入 `github.com` 查找到其IP地址
2. 将查到的IP地址和网址映射放到你的本地 `hosts` 文件中即可，例子：`140.82.112.4 github.com`
3. PS: windows 下 `host` 默认地址： `C:\Windows\System32\drivers\etc`


