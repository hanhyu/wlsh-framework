<?php declare(strict_types=1);

namespace App\Library;

use Envms\FluentPDO\Query;
use Swoole\Coroutine;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractPdo
{
    private static array $instance = [];

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
     * Time: 上午10:19
     *
     * @param string $di_db_schema 数据库对象池名称
     *
     * @return string|Query
     */
    public function getDb($di_db_schema = 'mysql_pool_obj'): string|Query
    {
        /** @var $mysql_pool_obj PdoPool */
        $mysql_pool_obj = DI::get($di_db_schema);
        if (!$mysql_pool_obj->available) {
            return '';
        }
        return new Query($mysql_pool_obj->get());
    }

    //todo 需要添加一个连接池快速回收与重连机制的注解类

}
