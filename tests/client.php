<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhui
 * Date: 17-12-16
 * Time: 下午2:23
 */

/**
 * 测试文件使用方法
 * 进入tests目录
 * 在命令行中执行： php client.php TestClient  websocket  index/index   进行websocket客户端测试
 * 在命令行中执行： php client.php TestClient  http       index/index   进行http客户端测试
 * 执行命令参数说明：第1个为文件路径 第2个为类名  第3个为方法  第4个为路由URL
 * 第一个文件路径参数必须为目录结构. 如: php /var/www/wlsh-framework/tests/client.php TestClient http index/index
 */

$class = $argv[1];
$action = $argv[2];
if(!isset($argv[3])) $argv[3] = '/';
$url = $argv[3];

//拼出类文件路径,引入该文件
require 'http/' . $class . '.php';

//实例化类
if(isset($url) && !empty($url)){
    $controller = new $class($url);
} else {
    $controller = new $class;
}

//调用该方法
$controller->$action();
