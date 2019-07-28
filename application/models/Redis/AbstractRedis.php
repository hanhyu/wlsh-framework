<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-10-28
 * Time: 下午3:47
 */

namespace App\Models\Redis;

use Yaf\Registry;
use Exception;

abstract class AbstractRedis
{
    /**
     * @var \Redis
     */
    protected $db;
    /**
     * 此处使用静态延迟绑定，实现选择不同的数据库
     * @var int
     */
    protected static $dbindex = 0;

    /**
     * 在协程中单例模式下使用
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws Exception
     *
     */
    public function __call($method, $args)
    {
        $redis_pool_obj = Registry::get('redis_pool');

        try {

            $this->db = $redis_pool_obj->get();

            $this->db->select(static::$dbindex);

            $data = call_user_func_array([$this, $method], $args);

        } catch (Exception $e) {
            co_log($e->getMessage(), "redis数据连接异常", 'alert');

            if ($redis_pool_obj->ping($this->db)) {
                $this->db = $redis_pool_obj->connect();
                $this->db->select(static::$dbindex);

                $data = call_user_func_array([$this, $method], $args);
            } else {
                throw new Exception('redis数据连接异常', 500);
            }
        }

        $redis_pool_obj->put($this->db);

        return $data;
    }

}
