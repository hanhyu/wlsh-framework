<?php
//本地环境配置
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

    'wlsh_log' => [
        'driver'    => 'mysql',
        'host'      => 'wlsh-mysql',
        'port'      => 3306,
        'database'  => 'wlsh_log',
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
        'username'   => 'admin',
        'pwd'        => 'admin',
        'database'   => 'baseFrame',
        'collection' => 'monolog',
    ],

    'es' => [
        'host'     => ['172.17.0.1:9200'],
        'username' => 'elastic',
        'password' => '123456',
    ],

    'kafka' => [
        'host'              => '172.17.0.1:9092',
        'update_brokers'    => true,
        'group_id'          => 'test_group',
        'client_id'         => 'test_custom',
        'group_instance_id' => 'test_custom',
    ],

    'clickhouse' => [
        'host'     => 'wlsh-clickhouse',
        'port'     => '8123',
        'username' => 'default',
        'password' => '123456',
    ],

    'page' => [
        'title' => '本地开发-运维平台',
    ],

    'backup' => [
        'path'    => ROOT_PATH . '/backup',
        'downUrl' => 'http://127.0.0.1:9770/',
    ],

];
