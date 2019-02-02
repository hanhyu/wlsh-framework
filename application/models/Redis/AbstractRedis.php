<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-10-28
 * Time: 下午3:47
 */

namespace App\Models\Redis;

class AbstractRedis
{
    /**
     * @var \Redis
     */
    protected $db;

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        try {
            $this->db = \Yaf\Registry::get('redis_pool')->get();
        } catch (\Exception $e) {
            co_log($e->getMessage(), "redis数据连接异常", 'alert');
            throw new \Exception('redis数据连接异常', 500);
        }

        $data = call_user_func_array([$this, $method], $args);

        if (!empty($this->db)) {
            \Yaf\Registry::get('redis_pool')->put($this->db);
        }

        return $data;
    }

}
