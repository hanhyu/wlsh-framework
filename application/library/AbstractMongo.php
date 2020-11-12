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
    protected static string $col;
    private static array $instance = [];
    /**
     * @var Collection
     */
    protected Collection $db;

    private function __construct()
    {
    }

    public static function getInstance(): AbstractMongo
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

    public function __call(string $method, array $args)
    {
        $log_arr = DI::get('config_arr')['log'];
        try {
            $mongo = new Client($log_arr['mongo'],
                [
                    'username'   => $log_arr['username'],
                    'password'   => $log_arr['pwd'],
                    'authSource' => $log_arr['database'],
                ]);

            $col = static::$col ?? $log_arr['collection'];

            $this->db = $mongo->selectCollection($log_arr['database'], $col);
            $data     = call_user_func_array([$this, $method], $args);
        } catch (Exception $e) {
            co_log($e->getMessage(), '连接mongodb服务端失败。', 'alert');
            throw new RuntimeException('mongodb连接失败', 500);
        }

        return $data;
    }

    /**
     * php7中mongodb扩展会自动释放连接
     */
    public function getDb(): void
    {
    }

}
