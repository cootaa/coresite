# coresite-api Back-end

Version:`beta 1.0`

[Chinese Version](./README_zh-CN.md)

## Introduce

「coresite」Back-end Interfaces,Developed using ThinkPHP 6.0, MySQL, Redis, and other technologies.

## System Requirements

* PHP 7.4+
* MYSQL 5.7+
* Redis

## Install

### ThinkPHP 6.0

Official Documentation  [ThinkPHP6.0完全开发手册](https://static.kancloud.cn/manual/thinkphp6_0/1037479)
```
composer create-project topthink/think tp
```

### Redis

* [Redis Install](http://redis.io/download)

### aliyun OSS

```
composer require aliyuncs/oss-sdk-php
```

### JWT

```
composer require firebase/php-jwt
```
## PHP extension


## Directory Structure

```
├─app
│  │  common.php //using language based on the request header
│  │
│  ├─controller //controller class
│  │      Chat.php //Chat class file
│  │      Common.php //Common class file
│  │      Discussion.php //Discussion class file (workplace)
│  │      File.php //File class file
│  │      Folder.php //Folder class file
│  │      Group.php //Group class file
│  │      Notice.php //Notice class file
│  │      Project.php //Project class file
│  │      User.php //User class file 
│  │
│  ├─lang // language
│  │      en-us.php  //English language 
│  │      zh-cn.php //Chinese language
│  │
│  ├─middleware //middleware
│  │      CheckToken.php //Check token
│  │      CrossDomain.php //Handling cross-origin
│
│
├─config 
│      cache.php //Redis Config
│      database.php //MYSQL Config
│      lang.php //language Config
├─route
│      app.php //route,middleware token validation
```


## Env Configuration

- MYSQL Configuration

```
[DATABASE]
TYPE = mysql
HOSTNAME = Localhost
DATABASE = DATABASE
USERNAME = MYSQL USERNAME
PASSWORD = MYSQL Password
HOSTPORT = Port
CHARSET = Character Set
DEBUG = true
```

- Redis Configuration

```
[REDIS]
TYPE = redis
HOSTNAME = Localhost
PASSWORD = Password
SELECT = DB
HOSTPORT = Port
TIMEOUT = Timeout
DEBUG = true
```

- Generate Avatar

```
[AVATAR_URL]
AVATAR_BASE_URL=URL
```
