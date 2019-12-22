<?php
declare(strict_types=1);

use App\Library\{
    DI,
    PdoPool,
    RedisPool,
    CoMysqlPool
};

//添加路由过滤配置
$router_filter = require CONF_PATH . '/routerFilter.php';
DI::set('router_filter_config_arr', $router_filter);

//发送邮件配置
$email = require CONF_PATH . '/sendEmail.php';
DI::set('email_config_arr', $email);

//添加redis连接池
$redis_pool = new RedisPool();
DI::set('redis_pool_obj', $redis_pool);

//添加mysql数据库连接池
$mysql_pool = new PdoPool('mysql');
DI::set('mysql_pool_obj', $mysql_pool);

//如需主从、读写库请在这里自行配置添加
//$mysql_master = new PdoPool('mysql_master');
//$mysql_slave = new PdoPool('mysql_slave');

//添加pgsql数据库连接池
//$pgsql_pool = new PdoPool('pgsql');
//DI::set('pgsql_pool_obj', $pgsql_pool);

//添加协程mysql数据库连接池
$co_mysql_pool = new CoMysqlPool();
DI::set('co_mysql_pool_obj', $co_mysql_pool);

