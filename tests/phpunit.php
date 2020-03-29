<?php
/**
 * Created by PhpStorm.
 * UserDomain: yf
 * Date: 2019-01-06
 * Time: 21:49
 */

//php phpunit.php -c phpunit.xml phpunit/domain/UserTest.php
//php phpunit.php phpunit/domain/UserTest.php
//php phpunit.php

Swoole\Coroutine\run(static function () {
    try {
        require_once getcwd() . '/../vendor/bin/phpunit';
    } catch (Swoole\Exception $e) {
        print_r($e->getMessage());
    }
});

