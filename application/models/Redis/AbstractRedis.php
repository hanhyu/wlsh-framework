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

class AbstractRedis
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

    /*
     * 协程模式中不建议使用单例对象
     * public function __construct()
    {
        try {
            $this->db = Registry::get('redis_pool')->get();
            $this->db->select(static::$dbindex);
        } catch (Exception $e) {
            co_log($e->getMessage(), "redis数据连接异常", 'alert');
            throw new Exception('redis数据连接异常', 500);
        }
    }*/


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
        try {
            $this->db = Registry::get('redis_pool')->get();
            $this->db->select(static::$dbindex);
        } catch (Exception $e) {
            co_log($e->getMessage(), "redis数据连接异常", 'alert');
            throw new Exception('redis数据连接异常', 500);
        }

        $data = call_user_func_array([$this, $method], $args);

        return $data;
    }

}
