<div align=center><img src="https://raw.githubusercontent.com/duiying/Knower/master/storage/img/logo.png"></div>

### 1、简介

**Knower（知者）**：一个实用的的开源知识库管理平台。  

基于 <a href="https://github.com/hyperf/hyperf">Hyperf</a> 实现，集成了权限管理、第三方登录（GitHub、QQ）、企业微信自建应用通知等功能，亦可作为 <a href="https://github.com/hyperf/hyperf">Hyperf</a> 的开发脚手架。  

不追求过多的技术栈，尽可能做到**实用、稳定**。  

**前台线上地址：** http://duiying.vip  
**后台线上地址：** http://duiying.vip/admin 邮箱：`demo@gmail.com` 密码：`123456`  

后台预览：  

<div align=center><img src="https://raw.githubusercontent.com/duiying/Knower/master/storage/img/admin.png" width="800"></div>  

### 2、技术栈

只使用了必需的技术：  

1. **Hyperf**：提供 HTTP 服务
2. **MySQL**：提供数据持久化功能
3. **Redis**：提供缓存、会话管理、接口防重放等功能
4. **ElasticSearch**：提供知识搜索功能

如果你像我一样，使用了 Docker 来作容器化部署，那么你会看到以下 4 个容器：  

- `knower-php`
- `knower-mysql`
- `knower-redis`
- `knower-elasticsearch`

```sh
[root@VM-24-17-centos ~]# docker ps
CONTAINER ID        IMAGE                                                           COMMAND                  CREATED             STATUS              PORTS                                            NAMES
b33b0e3cb5d2        registry.cn-beijing.aliyuncs.com/duiying/hyperf-php8:1.0        "php bin/hyperf.ph..."   6 weeks ago         Up About an hour    0.0.0.0:80->9501/tcp                             knower-php
82bfcf3ae48c        daocloud.io/library/mysql:5.6                                   "docker-entrypoint..."   6 weeks ago         Up 6 weeks          0.0.0.0:3306->3306/tcp                           knower-mysql
28a5fdad9b3e        daocloud.io/library/redis                                       "docker-entrypoint..."   6 weeks ago         Up 6 weeks          0.0.0.0:6397->6379/tcp                           knower-redis
321366347e2e        registry.cn-beijing.aliyuncs.com/duiying/elasticsearch-ik:1.0   "/tini -- /usr/loc..."   6 weeks ago         Up 6 weeks          0.0.0.0:9200->9200/tcp, 0.0.0.0:9300->9300/tcp   knower-elasticsearch
```

### 3、如何安装？

1、下载  

```sh
git clone https://github.com/duiying/Knower.git
```

2、安装  

```sh
# 1、进入目录
$ cd /home/work/Knower

# 2、安装 Composer 包
# 如果你没有装 Swoole、Redis 扩展，composer install 会报错，这怎么办呢？
# 那就不要 composer install 了，我把 vendor 目录打包好了，直接解压 vendor.zip 吧
$ composer install

# 3、容器化部署
$ cd /home/work/Knower/docker
$ docker-compose up -d
```

容器启动之后，连接 MySQL 服务，导入 [SQL](https://github.com/duiying/Knower/blob/master/knower.sql) 文件：  

- Host：`127.0.0.1`
- Port：`3360`（注意是 3360 不是 3306）
- User：`root`
- Password：`wyx**WYX123` 

最后，就可以访问了：  

- 前台：http://127.0.0.1:9501
- 运营后台：http://127.0.0.1:9501/admin 邮箱：`admin@gmail.com` 密码：`123456`