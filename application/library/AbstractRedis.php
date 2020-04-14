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
use Exception;
use Swoole\Coroutine;

abstract class AbstractRedis
{
    private static array $instance = [];
    protected Redis $db;
    /**
     * 此处使用静态延迟绑定，实现选择不同的数据库
     * @var int
     */
    protected static int $dbindex = 0;

    /**
     * 在协程中单例模式下使用
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     * @throws Exception
     *
     */
    public function __call(string $method, array $args)
    {
        $redis_pool_obj = DI::get('redis_pool_obj');

        try {

            $this->db = $redis_pool_obj->get();

            $this->db->select(static::$dbindex);

            $data = call_user_func_array([$this, $method], $args);

        } catch (Exception $e) {
            co_log($e->getMessage(), 'redis服务端断开连接', 'alert');

            if ($redis_pool_obj->ping($this->db)) {
                sleep(3);
                $this->db = $redis_pool_obj->connect();
            }
            $this->db->select(static::$dbindex);

            $data = call_user_func_array([$this, $method], $args);
        }

        $redis_pool_obj->put($this->db);

        return $data;
    }

    public static function getInstance()
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

}
