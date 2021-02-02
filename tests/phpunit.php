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
Swoole\Coroutine::set(['hook_flags' => SWOOLE_HOOK_ALL]);
Swoole\Coroutine\run(static function () {
    try {
        require_once getcwd() . '/../vendor/bin/phpunit';
    } catch (Throwable) {
    }
});
Swoole\Event::wait();

