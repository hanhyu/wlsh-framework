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
     * 使用单例可以最大化在协程内利用pdo连接池对象
     *
     * User: hanhyu
     * Date: 2021/1/30
     * Time: 下午3:08
     *
     * @param string $di_db_schema 数据库对象池名称
     *
     * @return Redis
     * @throws RedisException
     */
    public static function getDb(string $di_db_schema = 'redis_pool_obj'): Redis
    {
        $_class_name = static::class;
        $_cid        = Coroutine::getCid();
        if (!isset(static::$instance[$_class_name]['redis'][$_cid])) {
            /** @var $_pool_obj RedisPool */
            $_pool_obj = DI::get($di_db_schema);
            $_instance = static::$instance[$_class_name]['redis'][$_cid] = $_pool_obj->get();
        } else {
            $_instance = static::$instance[$_class_name]['redis'][$_cid];
        }

        defer(static function () use ($_class_name, $_cid) {
            unset(static::$instance[$_class_name]['redis'][$_cid]);
        });

        $_instance->select(static::$db_index);

        return $_instance;
    }

}
