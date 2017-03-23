# php delayed #

## 环境依赖 ##
-  php>=5.4
-  redis
- [swoole](https://github.com/swoole/swoole-src "swoole")

## 简介 ##

PHP异步高性能延时队列,Timer由swoole定时器实现,过后会加入pcntl版本Timer。延时队列存储可以是redis或者是php数组,如用redis请确保安装扩展。并且所有任务由swoole的worker_task处理不会影响定时器worker。实现原理是一个环形队列,然后一个指针定时扫描环形队列,详细请参考代码实现。

## 使用 ##

如下提供了两个Demo:
- serverRunDemo.php 延时队列启动脚本
- sortSetAddDemo.php 任务投递脚本

**serverRunDemo.php**
```php
$delayedObj = new delayed();//实例化
$delayedObj->setJobEvent(function ($data) {
        var_dump($data);
    })->run(['swoole' => ['time' => 1000],
        'redis' => ['hash_name_prefix' => 'delayed:',
                'host' => '101.200.196.131',
                'port' => 7009]]);
```
直接把包放入你的代码,实例化delayed类即可。方法setJobEvent为注入你要执行的函数当延时队列推送时会调用你的函数并$data为你投递的值。run方法为启动一个延时队列你需要在第一个参数传入一个配置数组,swoole、redis配置等当redis配置存在时延时队列会以redis为存储。