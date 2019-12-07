<?php
declare(strict_types=1);


namespace App\Models;


use App\Models\Redis\LoginModel;
use Swoole\Coroutine;
use Exception;

/**
 * Class RedisFactory
 * @package App\Models
 */
class RedisFactory
{
    /**
     * @var LoginModel
     */
    private static $login = [];

    /**
     * UserDomain: hanhyu
     * Date: 19-7-8
     * Time: 下午8:27
     * @return LoginModel
     * @throws Exception
     */
    public static function login(): LoginModel
    {
        $cid = Coroutine::getCid();
        //单例对象协程隔离
        if (!isset(self::$login[$cid])) {
            self::$login[$cid] = new LoginModel();
        }

        defer(static function () use ($cid) {
            unset(self::$login[$cid]);
        });

        return self::$login[$cid];
    }

}
