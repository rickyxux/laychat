<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//$re = new Redis();
//$res = $re->connect('127.0.0.1', 6379);
//var_dump($res);exit;

/*

$sessionpath = session_save_path();
if (strpos ($sessionpath, ";") !== FALSE)
    $sessionpath = substr ($sessionpath, strpos ($sessionpath, ";")+1);

//获取当前session的保存路径
echo 'session的保存路径：'.$sessionpath.'<br>';

//测试session读取是否正常
session_start();

var_dump($_SESSION);

$_SESSION['username'] = "xuxiang";
echo 'session_id:'.session_id().'<br>';

//从Memcache中读取session
$m = new Memcache();
$m->connect('127.0.0.1', 11211);
$session = $m->get(session_id());
echo 'memcache中的数据：'.$session."<br/>";


//echo $_SESSION['password'].'<br>';
//
//$delete = $m->delete(session_id());
//echo $delete;
//

//var_dump($m->get(session_id()));
//
//
//echo 'session_id:'.session_id()."<br/>";
exit;

*/








// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 加载框架引导文
require __DIR__ . '/../thinkphp/start.php';
