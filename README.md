<h1 align="center">
    Knower
</h1>

### 简介

**Knower（知者）**：一个实用的的开源知识库管理平台。  

基于 <a href="https://github.com/hyperf/hyperf">Hyperf</a> 实现，集成了权限管理、第三方登录（GitHub、QQ），亦可作为 <a href="https://github.com/hyperf/hyperf">Hyperf</a> 的开发脚手架。不追求过多的技术栈，尽可能做到**实用、稳定**。

**前台线上地址：** http://duiying.vip  
**运营后台线上地址：** http://duiying.vip/admin 邮箱：`demo@gmail.com` 密码：`123456`

### 技术栈

只使用了必需的技术：  

1. **Hyperf**：提供 HTTP 服务
2. **MySQL**：提供数据持久化功能
3. **Redis**：提供缓存、会话管理、接口防重放等功能
4. **ElasticSearch**：提供知识搜索功能

如果你像我一样，使用了 Docker 来作容器化部署，那么你会看到以下 4 个容器（`knower-php`、`knower-mysql`、`knower-redis`、`knower-elasticsearch`）：  

```sh
[root@VM-24-17-centos ~]# docker ps
CONTAINER ID        IMAGE                                                           COMMAND                  CREATED             STATUS              PORTS                                            NAMES
b33b0e3cb5d2        registry.cn-beijing.aliyuncs.com/duiying/hyperf-php8:1.0        "php bin/hyperf.ph..."   6 weeks ago         Up About an hour    0.0.0.0:80->9501/tcp                             knower-php
82bfcf3ae48c        daocloud.io/library/mysql:5.6                                   "docker-entrypoint..."   6 weeks ago         Up 6 weeks          0.0.0.0:3306->3306/tcp                           knower-mysql
28a5fdad9b3e        daocloud.io/library/redis                                       "docker-entrypoint..."   6 weeks ago         Up 6 weeks          0.0.0.0:6397->6379/tcp                           knower-redis
321366347e2e        registry.cn-beijing.aliyuncs.com/duiying/elasticsearch-ik:1.0   "/tini -- /usr/loc..."   6 weeks ago         Up 6 weeks          0.0.0.0:9200->9200/tcp, 0.0.0.0:9300->9300/tcp   knower-elasticsearch
```

### 如何安装？

请先确保你的 PHP 环境已经安装了 `Swoole`、`Redis` 扩展。  

1、下载  

```sh
git clone https://github.com/duiying/Knower.git
```

2、安装  

```sh
$ cd /home/work/Knower

# 安装 Composer 包
$ composer install

$ cd /home/work/Knower/docker

# 容器化部署
$ docker-compose up -d
```

容器启动之后，连接 MySQL 服务（HOST：`127.0.0.1`、PORT：`3360`（注意是 3360 不是 3306）、用户名：`root`、密码：`wyx**WYX123` ），导入 [SQL](https://github.com/duiying/Knower/blob/master/knower.sql) 文件。  

最后，就可以访问了：  

- 前台：http://127.0.0.1:9501
- 运营后台：http://127.0.0.1:9501/admin 邮箱：`admin@gmail.com` 密码：`123456`