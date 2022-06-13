## Docker入门

### Reference

+ [B站-超哥带你学Docker](https://www.bilibili.com/video/BV1aB4y1X7x9)
+ [Docker 从入门到实战](https://yeasy.gitbook.io/docker_practice)

### 序言

+ **Docker** 是基于 Linux 内核的 Cgroup、NameSpace，以及 Union FS 等技术，对进程进行封装隔离，属于操作系统层面的虚拟化技术。
+ 由于隔离的进程独立于宿主机和其他隔离的进程，也被称为 **容器**。
+ 容器内的应用程序直接运行在宿主机的内核上，容器内没有自己的内核，也没有对硬件进行虚拟，因此容器比起虚拟机更为轻便。

#### 关键概念

+ **镜像-Images** ：将应用程序所需的环境打包问镜像文件，用于构建容器。
+ **容器-Containers**：容器可以看作是镜像的一个实例，应用程序运行其中。
+ **数据卷-Data Volumes**：数据卷是一个可供一个或多个容器使用的特殊目录。
+ **网络-Networks**：镜像的网络
+ **镜像仓库-dockerhub**：提供上传、下载、保存镜像功能的仓库。
+ **Dockerfile**：可以理解为镜像构建脚本，其中记录了构建一个镜像的一系列操作。



#### 快速安装

```bash
# CentOs7.2 下安装依赖包
sudo yum install -y yum-utils
# 添加 yum 软件源
sudo yum-config-manager --add-repo https://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo
sudo sed -i 's/download.docker.com/mirrors.aliyun.com\/docker-ce/g' /etc/yum.repos.d/docker-ce.repo
# 安装 docker
sudo yum install docker-ce docker-ce-cli containerd.io
# 第二种方式使用脚本 自动安装
curl -fsSL get.docker.com -o get-docker.sh
sudo sh get-docker.sh --mirror Aliyun
# 启动 docker
sudo systemctl enable docker
sudo systemctl start docker
# 查看 docker 版本
docker --version
# 测试 docker 是否安装成功
docker run --rm hello-world
```

**配置Docker国内镜像加速**

```perl
# 查看是否已经配置过镜像地址
systemctl cat docker | grep '\-\-registry\-mirror'
# 配置镜像加速
vim /etc/docker/daemon.json
# 写入如下
{
  "registry-mirrors": [
    "https://hub-mirror.c.163.com",
    "https://mirror.baidubce.com"
  ]
}

# 重启服务
sudo systemctl daemon-reload
sudo systemctl restart docker
```

**内核流量转发**

如若出现如下问题：

```bash
WARNING: bridge-nf-call-iptables is disabled
WARNING: bridge-nf-call-ip6tables is disabled
```

*解决办法：*

```bash
# 配置内核参数
sudo tee -a /etc/sysctl.conf <<-EOF
net.bridge.bridge-nf-call-ip6tables = 1
net.bridge.bridge-nf-call-iptables = 1
EOF

# 重新加载
sudo sysctl -p
```

### 起步

#### 运行Nginx服务

```bash
# 1. 获取镜像
docker pull nginx
# 在仓库中搜索 nginx
docker search nginx
# 查看本地镜像
docker image ls
docker images
# 删除镜像
docker rmi [镜像ID(IMAGE ID)]

# 2. 运行镜像
# -d 后台运行
# -p 80:80 端口映射 宿主机端口:容器端口
# 返回一个容器ID
docker run -d -p 80:80 nginx
# 查看运行中的容器
docker ps
# 停止容器
docker stop [容器ID(CONTAINER ID)]
# 运行容器
docker start [容器ID(CONTAINER ID)]
```

#### 镜像原理



#### Dockerfile



#### Docker Compose

> Compose 的定位是 [定义和运行多个 Docker 容器的应用]。
> Compose 运行通过一个单独的 `docker-compose.yml` 模板文件来定义一组相关联的应用容器为一个项目。

*Compose 中的俩个重要概念:*
+ 服务(service)：一个应用的容器，实际上可以包括若干运行相同镜像的容器实例。
+ 项目(project)：由一组关联的应用容器组成的一个完整业务单元，在 `docker-compose.yml` 文件中定义。
+ Compose 的默认管理对象是项目，通过子命令对项目中的一组容器进行便捷地生命周期管理。

**Linux安装docker-compose**

```bash
# 这里要注意的是官网文档上的可能版本比较低
sudo curl -L https://github.com/docker/compose/releases/download/v2.6.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose
sudo curl -L https://github.com/docker/compose/releases/download/v2.6.0/docker-compose-linux-x86_64

# 国内用户可以使用以下方式加快下载
sudo curl -L https://download.fastgit.org/docker/compose/releases/download/1.27.4/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose

# 文件权限
sudo chmod +x /usr/local/bin/docker-compose

# bash 补全命令
curl -L https://raw.githubusercontent.com/docker/compose/1.27.4/contrib/completion/bash/docker-compose > /etc/bash_completion.d/docker-compose

# 卸载[二进制包]
sudo rm /usr/local/bin/docker-compose
```

[docker-compose命令说明](https://yeasy.gitbook.io/docker_practice/compose/commands)



#### Compose 模板文件







#### docker/lnmp

```bash
# git 安装
git clone --depth=1 https://gitee.com/khs1994-docker/lnmp.git



sudo ln -s $LNMP_PATH/cli/completion/bash/lnmp-docker /etc/bash_completion.d/lnmp-docker


sudo ln -s $LNMP_PATH/lnmp-docker /etc/bash_completion.d/lnmp-docker


sudo ln -s /data/web/lnmp/lnmp-docker /usr/bin/lnmp-docker

export LNMP_PATH=/data/web/lnmp
export PATH=$LNMP_PATH:$LNMP_PATH/bin:$PATH



server {
        listen        80;
        server_name  www.lifewonder.com;
        root   "/app//data/web/lnmp/app/lifewonder/public";
        location / {
            try_files $uri $uri/ /index.php?$query_string;
            index index.php index.html error/index.html;
            include /app//data/web/lnmp/app/lifewonder/public/nginx.htaccess;
            autoindex  off;
        }
        location ~ \.php(.*)$ {
            fastcgi_pass   php7:9000;
            fastcgi_index  index.php;
            fastcgi_split_path_info  ^((?U).+\.php)(/?.+)$;
            include        fastcgi_params;
        }
}


```





### 其他

+ 安装 vim ：`yum install vim`
+ 安装 git ：`yum install git`



#### docker-composer 补全

`vim /etc/bash_completion.d/docker-compose`

*复制以下内容：*

```txt
#!/bin/bash
#
# bash completion for docker-compose
#
# This work is based on the completion for the docker command.
#
# This script provides completion of:
#  - commands and their options
#  - service names
#  - filepaths
#
# To enable the completions either:
#  - place this file in /etc/bash_completion.d
#  or
#  - copy this file to e.g. ~/.docker-compose-completion.sh and add the line
#    below to your .bashrc after bash completion features are loaded
#    . ~/.docker-compose-completion.sh

__docker_compose_previous_extglob_setting=$(shopt -p extglob)
shopt -s extglob

__docker_compose_q() {
	docker-compose 2>/dev/null "${top_level_options[@]}" "$@"
}

# Transforms a multiline list of strings into a single line string
# with the words separated by "|".
__docker_compose_to_alternatives() {
	local parts=( $1 )
	local IFS='|'
	echo "${parts[*]}"
}

# Transforms a multiline list of options into an extglob pattern
# suitable for use in case statements.
__docker_compose_to_extglob() {
	local extglob=$( __docker_compose_to_alternatives "$1" )
	echo "@($extglob)"
}

# Determines whether the option passed as the first argument exist on
# the commandline. The option may be a pattern, e.g. `--force|-f`.
__docker_compose_has_option() {
	local pattern="$1"
	for (( i=2; i < $cword; ++i)); do
		if [[ ${words[$i]} =~ ^($pattern)$ ]] ; then
			return 0
		fi
	done
	return 1
}

# Returns `key` if we are currently completing the value of a map option (`key=value`)
# which matches the extglob passed in as an argument.
# This function is needed for key-specific completions.
__docker_compose_map_key_of_current_option() {
        local glob="$1"

        local key glob_pos
        if [ "$cur" = "=" ] ; then        # key= case
                key="$prev"
                glob_pos=$((cword - 2))
        elif [[ $cur == *=* ]] ; then     # key=value case (OSX)
                key=${cur%=*}
                glob_pos=$((cword - 1))
        elif [ "$prev" = "=" ] ; then
                key=${words[$cword - 2]}  # key=value case
                glob_pos=$((cword - 3))
        else
                return
        fi

        [ "${words[$glob_pos]}" = "=" ] && ((glob_pos--))  # --option=key=value syntax

        [[ ${words[$glob_pos]} == @($glob) ]] && echo "$key"
}

# suppress trailing whitespace
__docker_compose_nospace() {
	# compopt is not available in ancient bash versions
	type compopt &>/dev/null && compopt -o nospace
}


# Outputs a list of all defined services, regardless of their running state.
# Arguments for `docker-compose ps` may be passed in order to filter the service list,
# e.g. `status=running`.
__docker_compose_services() {
	__docker_compose_q ps --services "$@"
}

# Applies completion of services based on the current value of `$cur`.
# Arguments for `docker-compose ps` may be passed in order to filter the service list,
# see `__docker_compose_services`.
__docker_compose_complete_services() {
	COMPREPLY=( $(compgen -W "$(__docker_compose_services "$@")" -- "$cur") )
}

# The services for which at least one running container exists
__docker_compose_complete_running_services() {
	local names=$(__docker_compose_services --filter status=running)
	COMPREPLY=( $(compgen -W "$names" -- "$cur") )
}


_docker_compose_build() {
	case "$prev" in
		--build-arg)
			COMPREPLY=( $( compgen -e -- "$cur" ) )
			__docker_compose_nospace
			return
			;;
		--memory|-m)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--build-arg --compress --force-rm --help --memory -m --no-cache --no-rm --pull --parallel -q --quiet" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services --filter source=build
			;;
	esac
}


_docker_compose_config() {
	case "$prev" in
		--hash)
			if [[ $cur == \\* ]] ; then
				COMPREPLY=( '\*' )
			else
				COMPREPLY=( $(compgen -W "$(__docker_compose_services) \\\* " -- "$cur") )
			fi
			return
			;;
	esac

	COMPREPLY=( $( compgen -W "--hash --help --no-interpolate --quiet -q --resolve-image-digests --services --volumes" -- "$cur" ) )
}


_docker_compose_create() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--build --force-recreate --help --no-build --no-recreate" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_docker_compose() {
	case "$prev" in
		--tlscacert|--tlscert|--tlskey)
			_filedir
			return
			;;
		--file|-f)
			_filedir "y?(a)ml"
			return
			;;
		--log-level)
			COMPREPLY=( $( compgen -W "debug info warning error critical" -- "$cur" ) )
			return
			;;
		--project-directory)
			_filedir -d
			return
			;;
		--env-file)
			_filedir
			return
			;;
		$(__docker_compose_to_extglob "$daemon_options_with_args") )
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "$daemon_boolean_options $daemon_options_with_args $top_level_options_with_args --help -h --no-ansi --verbose --version -v" -- "$cur" ) )
			;;
		*)
			COMPREPLY=( $( compgen -W "${commands[*]}" -- "$cur" ) )
			;;
	esac
}


_docker_compose_down() {
	case "$prev" in
		--rmi)
			COMPREPLY=( $( compgen -W "all local" -- "$cur" ) )
			return
			;;
		--timeout|-t)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --rmi --timeout -t --volumes -v --remove-orphans" -- "$cur" ) )
			;;
	esac
}


_docker_compose_events() {
	case "$prev" in
		--json)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --json" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_exec() {
	case "$prev" in
		--index|--user|-u|--workdir|-w)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "-d --detach --help --index --privileged -T --user -u --workdir -w" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_running_services
			;;
	esac
}


_docker_compose_help() {
	COMPREPLY=( $( compgen -W "${commands[*]}" -- "$cur" ) )
}

_docker_compose_images() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --quiet -q" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}

_docker_compose_kill() {
	case "$prev" in
		-s)
			COMPREPLY=( $( compgen -W "SIGHUP SIGINT SIGKILL SIGUSR1 SIGUSR2" -- "$(echo $cur | tr '[:lower:]' '[:upper:]')" ) )
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help -s" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_running_services
			;;
	esac
}


_docker_compose_logs() {
	case "$prev" in
		--tail)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--follow -f --help --no-color --tail --timestamps -t" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_pause() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_running_services
			;;
	esac
}


_docker_compose_port() {
	case "$prev" in
		--protocol)
			COMPREPLY=( $( compgen -W "tcp udp" -- "$cur" ) )
			return;
			;;
		--index)
			return;
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --index --protocol" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_ps() {
	local key=$(__docker_compose_map_key_of_current_option '--filter')
	case "$key" in
		source)
			COMPREPLY=( $( compgen -W "build image" -- "${cur##*=}" ) )
			return
			;;
		status)
			COMPREPLY=( $( compgen -W "paused restarting running stopped" -- "${cur##*=}" ) )
			return
			;;
	esac

	case "$prev" in
		--filter)
			COMPREPLY=( $( compgen -W "source status" -S "=" -- "$cur" ) )
			__docker_compose_nospace
			return;
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--all -a --filter --help --quiet -q --services" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_pull() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --ignore-pull-failures --include-deps --no-parallel --quiet -q" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services --filter source=image
			;;
	esac
}


_docker_compose_push() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --ignore-push-failures" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_restart() {
	case "$prev" in
		--timeout|-t)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --timeout -t" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_running_services
			;;
	esac
}


_docker_compose_rm() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--force -f --help --stop -s -v" -- "$cur" ) )
			;;
		*)
			if __docker_compose_has_option "--stop|-s" ; then
				__docker_compose_complete_services
			else
				__docker_compose_complete_services --filter status=stopped
			fi
			;;
	esac
}


_docker_compose_run() {
	case "$prev" in
		-e)
			COMPREPLY=( $( compgen -e -- "$cur" ) )
			__docker_compose_nospace
			return
			;;
		--entrypoint|--label|-l|--name|--user|-u|--volume|-v|--workdir|-w)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--detach -d --entrypoint -e --help --label -l --name --no-deps --publish -p --rm --service-ports -T --use-aliases --user -u --volume -v --workdir -w" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_scale() {
	case "$prev" in
		=)
			COMPREPLY=("$cur")
			return
			;;
		--timeout|-t)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --timeout -t" -- "$cur" ) )
			;;
		*)
			COMPREPLY=( $(compgen -S "=" -W "$(__docker_compose_services)" -- "$cur") )
			__docker_compose_nospace
			;;
	esac
}


_docker_compose_start() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services --filter status=stopped
			;;
	esac
}


_docker_compose_stop() {
	case "$prev" in
		--timeout|-t)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help --timeout -t" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_running_services
			;;
	esac
}


_docker_compose_top() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_running_services
			;;
	esac
}


_docker_compose_unpause() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--help" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services --filter status=paused
			;;
	esac
}


_docker_compose_up() {
	case "$prev" in
		=)
			COMPREPLY=("$cur")
			return
			;;
		--exit-code-from)
			__docker_compose_complete_services
			return
			;;
		--scale)
			COMPREPLY=( $(compgen -S "=" -W "$(__docker_compose_services)" -- "$cur") )
			__docker_compose_nospace
			return
			;;
		--timeout|-t)
			return
			;;
	esac

	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--abort-on-container-exit --always-recreate-deps --attach-dependencies --build -d --detach --exit-code-from --force-recreate --help --no-build --no-color --no-deps --no-recreate --no-start --renew-anon-volumes -V --remove-orphans --scale --timeout -t" -- "$cur" ) )
			;;
		*)
			__docker_compose_complete_services
			;;
	esac
}


_docker_compose_version() {
	case "$cur" in
		-*)
			COMPREPLY=( $( compgen -W "--short" -- "$cur" ) )
			;;
	esac
}


_docker_compose() {
	local previous_extglob_setting=$(shopt -p extglob)
	shopt -s extglob

	local commands=(
		build
		config
		create
		down
		events
		exec
		help
		images
		kill
		logs
		pause
		port
		ps
		pull
		push
		restart
		rm
		run
		scale
		start
		stop
		top
		unpause
		up
		version
	)

	# Options for the docker daemon that have to be passed to secondary calls to
	# docker-compose executed by this script.
	local daemon_boolean_options="
		--skip-hostname-check
		--tls
		--tlsverify
	"
	local daemon_options_with_args="
		--context -c
		--env-file
		--file -f
		--host -H
		--project-directory
		--project-name -p
		--tlscacert
		--tlscert
		--tlskey
	"

	# These options are require special treatment when searching the command.
	local top_level_options_with_args="
		--log-level
	"

	COMPREPLY=()
	local cur prev words cword
	_get_comp_words_by_ref -n : cur prev words cword

	# search subcommand and invoke its handler.
	# special treatment of some top-level options
	local command='docker_compose'
	local top_level_options=()
	local counter=1

	while [ $counter -lt $cword ]; do
		case "${words[$counter]}" in
			$(__docker_compose_to_extglob "$daemon_boolean_options") )
				local opt=${words[counter]}
				top_level_options+=($opt)
				;;
			$(__docker_compose_to_extglob "$daemon_options_with_args") )
				local opt=${words[counter]}
				local arg=${words[++counter]}
				top_level_options+=($opt $arg)
				;;
			$(__docker_compose_to_extglob "$top_level_options_with_args") )
				(( counter++ ))
				;;
			-*)
				;;
			*)
				command="${words[$counter]}"
				break
				;;
		esac
		(( counter++ ))
	done

	local completions_func=_docker_compose_${command//-/_}
	declare -F $completions_func >/dev/null && $completions_func

	eval "$previous_extglob_setting"
	return 0
}

eval "$__docker_compose_previous_extglob_setting"
unset __docker_compose_previous_extglob_setting

complete -F _docker_compose docker-compose docker-compose.exe

```



