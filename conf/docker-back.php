<?php
//本地开发环境配置
return [

    'origin' => [
        'domain' => 'https://127.0.0.1:9772',
    ],

    'mysql' => [
        'driver'    => 'mysql',
        'host'      => '172.17.0.1',
        'port'      => 3308,
        'database'  => 'baseFrame',
        'username'  => 'root',
        'password'  => 'wlsh_mysql',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8_general_ci',
        'prefix'    => '',
        'strict'    => false,
    ],

    'pgsql' => [
        'driver'    => 'pgsql',
        'host'      => '172.17.0.1',
        'port'      => 5432,
        'database'  => 'baseframe',
        'username'  => 'postgres',
        'password'  => 'root',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => '',
        'strict'    => false,
    ],

    'redis' => [
        'host' => '172.17.0.1',
        'port' => 6381,
        'auth' => 'wlsh_redis',
    ],

    'log' => [
        'mongo'      => 'mongodb://172.17.0.1:27018',
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
        'downUrl' => 'http://127.0.0.1:9773/',
    ],

];
