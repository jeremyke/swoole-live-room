利用swoole扩展在ThinkPHP5框架上开发
===============
> ThinkPHP5的运行环境要求PHP5.6以上。


## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│
├─public                WEB目录（对外访问目录）
│  ├─index.php          入口文件
│  ├─router.php         快速测试文件
│  └─.htaccess          用于apache的重写
│
│
├─vendor                第三方类库目录（Composer依赖库）
~~~

## 功能介绍

- 登录
- 短信
- 图文直播
