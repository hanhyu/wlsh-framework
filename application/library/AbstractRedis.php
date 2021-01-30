<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-10-28
 * Time: 下午3:47
 */

namespace App\Library;

use Redis;
use RedisException;
use Swoole\Coroutine;

abstract class AbstractRedis
{
    private static array $instance = [];
    /**
     * 此处使用静态延迟绑定，实现选择不同的数据库
     * @var int
     */
    protected static int $db_index = 0;

    public static function getInstance(): static
    {
        $class_name = static::class;
        $cid        = Coroutine::getCid();
        if (!isset(static::$instance[$class_name][$cid])) {
            //new static()与new static::class一样，但为了IDE友好提示类中的方法，需要用new static()
            $_instance = static::$instance[$class_name][$cid] = new static();
        } else {
            $_instance = static::$instance[$class_name][$cid];
        }

        defer(static function () use ($class_name, $cid) {
            unset(static::$instance[$class_name][$cid]);
        });

        //为了IDE代码提示功能
        return $_instance;
    }

    private function __construct()
    {
    }

    /**
     * User: hanhyu
     * Date: 2021/1/30
     * Time: 下午3:08
     *
     * @param string $di_db_schema 数据库对象池名称
     *
     * @return Redis
     * @throws RedisException
     */
    public static function getDb($di_db_schema = 'redis_pool_obj'): Redis
    {
        /** @var $redis_pool_obj RedisPool */
        $redis_pool_obj = DI::get($di_db_schema);

        $db = $redis_pool_obj->get();
        $db->select(static::$db_index);
        return $db;
    }

}
