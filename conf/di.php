<?php
declare(strict_types=1);

use App\Library\{
    DI,
    PdoPool,
    RedisPool,
};

//发送邮件配置
$email = require CONF_PATH . '/sendEmail.php';
DI::set('email_config_arr', $email);

//添加redis连接池
DI::set('redis_pool_obj', new RedisPool());

//添加mysql数据库连接池
DI::set('mysql_pool_obj', new PdoPool('mysql'));

//如需主从、读写库请在这里自行配置添加
//$mysql_master = new PdoPool('mysql_master');
//$mysql_slave = new PdoPool('mysql_slave');

