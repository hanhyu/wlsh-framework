<?php
/**
 * 发送邮件配置
 * User: hanhyu
 * Date: 19-1-24
 * Time: 下午4:52
 */

return [
    //开发环境
    'develop' => [
        'host'    => 'smtp.qq.com',
        'port'    => '25',
        'uname'   => 'hanhyu@qq.com',
        'pwd'     => '123456',
        'subject' => 'wlsh-baseFrame程序-开发环境（异常日志）',
        'from'    => 'hanhyu@qq.com',
        'to'      => 'hanhyu@qq.com',
    ],
    //测试环境
    'test'    => [],
    //生产环境
    'product' => [],
];

