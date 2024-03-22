# 心界 后端

版本:`beta 1.0`

[English Version](./README_en-US.md)

## 介绍

「心界」后端接口开发,基于ThinkPHP6.0、MYSQL、Redis等开发。

## 系统需求

* PHP 7.4+
* MYSQL 5.7+
* Redis

## 安装

### ThinkPHP 6.0

官方文档  [ThinkPHP6.0完全开发手册](https://static.kancloud.cn/manual/thinkphp6_0/1037479)

```
composer create-project topthink/think tp
```

### Redis

* [Redis 安装](http://redis.io/download)

### 阿里云 OSS

```
composer require aliyuncs/oss-sdk-php
```

### JWT

```
composer require firebase/php-jwt
```
## PHP扩展



## 目录结构

```
├─app
│  │  common.php //请求头判断采用语言包
│  │
│  ├─controller //控制类
│  │      Chat.php //聊天类
│  │      Common.php //公共类
│  │      Discussion.php //讨论类(工作台)
│  │      File.php //文件类
│  │      Folder.php //文件夹类
│  │      Group.php //组织类
│  │      Notice.php //通知类
│  │      Project.php //项目类
│  │      User.php //用户类
│  │
│  ├─lang //语言包
│  │      en-us.php  //英文语言包
│  │      zh-cn.php //中文语言包
│  │
│  ├─middleware //中间件
│  │      CheckToken.php //token验证
│  │      CrossDomain.php //处理跨域
│
│
├─config 
│      cache.php //Redis配置
│      database.php //数据库配置
│      lang.php //语言包配置
├─route
│      app.php //路由,经过中间件token验证
```


## Env配置

- 数据库配置

```
[DATABASE]
TYPE = mysql
HOSTNAME = 主机地址
DATABASE = 数据库名
USERNAME = 数据库账号
PASSWORD = 数据库密码
HOSTPORT = 端口号
CHARSET = 字符集
DEBUG = true
```

- Redis配置

```
[REDIS]
TYPE = redis
HOSTNAME = 主机地址
PASSWORD = 密码
SELECT = 区块
HOSTPORT = 端口号
TIMEOUT = 超时时间
DEBUG = true
```

- 生成用户头像

```
[AVATAR_URL]
AVATAR_BASE_URL=生成地址
```
