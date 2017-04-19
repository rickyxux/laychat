<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    'connector' => 'Redis',   //Redis驱动
    'expire' => 60,   //任务过期时间，默认为６０秒，
    'default' => 'default',   //默认的队列名称
    'host' => '127.0.0.1',   //reids 主机ip
    'port' => 6379,   //reids端口
    'password' => '',   //redis密码
    'select' => 0,   //使用哪一个db,默认为db0
    'timeout' => 0,   //redis链接的超时时间
    'persistent' => false,   //是否是长连接
];
