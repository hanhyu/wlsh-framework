<?php
//本地开发环境配置
return [

    //指定Access-Control-Allow-Origin的值
    'origin' => [
        'domain' => 'https://127.0.0.1:9772',
    ],

    'mysql' => [
        'driver'    => 'mysql',
        'host'      => 'wlsh-mysql',
        'port'      => 3306,
        'database'  => 'baseFrame',
        'username'  => 'root',
        'password'  => 'wlsh_mysql',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8_general_ci',
        'prefix'    => '',
        'strict'    => false,
    ],

    'redis' => [
        'host' => 'wlsh-redis-master',
        'port' => 6379,
        'auth' => 'wlsh_redis',
    ],

    'log' => [
        'mongo'      => 'mongodb://wlsh-mongo:27017',
        'username'   => 'test',
        'pwd'        => 'test',
        'database'   => 'baseFrame',
        'collection' => 'monolog',
    ],

    'page' => [
        'title' => '本地开发-运维平台',
    ],

    'backup' => [
        'path'    => ROOT_PATH . '/backup',
        'downUrl' => 'http://127.0.0.1:9770/',
    ],

];
