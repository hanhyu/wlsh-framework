<?php
declare(strict_types=1);


namespace App\Models;


use App\Models\Redis\Login;
use Swoole\Coroutine;

/**
 * 不建议在协程框架中使用单例写法
 * Class RedisFactory
 * @package App\Models
 */
class RedisFactory
{
    /**
     * @var Login
     */
    private static $login = [];

    /**
     * User: hanhyu
     * Date: 19-7-8
     * Time: 下午8:27
     * @return Login
     * @throws \Exception
     */
    public static function login(): Login
    {
        $cid = Coroutine::getCid();
        //单例对象协程隔离
        if (!isset(self::$login[$cid])) {
            self::$login[$cid] = new Login();
        }

        defer(function () use ($cid) {
            unset(self::$login[$cid]);
        });

        return self::$login[$cid];
    }

}