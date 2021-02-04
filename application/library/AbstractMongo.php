<?php
declare(strict_types=1);

namespace App\Library;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Exception\Exception;
use Swoole\Coroutine;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractMongo
{
    /**
     * @var string 此处使用静态延迟绑定，实现选择不同的集合（数据表）
     */
    protected static string $col;
    private static array $instance = [];

    private function __construct()
    {
    }

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

    /**
     * php7中mongodb扩展会自动释放连接
     * mongodb一个连接就是一个连接池
     *
     * @param string $db_schema
     *
     * @return Collection
     */
    public static function getDb(string $db_schema = 'log'): Collection
    {
        $log_arr = DI::get('config_arr')[$db_schema];
        try {
            $mongo = new Client($log_arr['mongo'],
                [
                    'username'   => $log_arr['username'],
                    'password'   => $log_arr['pwd'],
                    'authSource' => $log_arr['database'],
                ]);

            $col = static::$col ?? $log_arr['collection'];
            return $mongo->selectCollection($log_arr['database'], $col);
        } catch (Exception $e) {
            task_monolog(DI::get('server_obj'), $e->getMessage(), '连接mongodb服务端失败。', level: 'alert');
            throw new RuntimeException('mongodb连接失败', 500);
        }
    }

}
