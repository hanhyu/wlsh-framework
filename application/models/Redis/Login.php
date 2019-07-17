<?php
declare(strict_types=1);

namespace App\Models\Redis;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-22
 * Time: 下午9:05
 */
class Login extends AbstractRedis
{
    /**
     * 此处使用静态延迟绑定，实现选择不同的数据库,如不设置默认为0
     * @var int
     */
    protected static $dbindex = 1;

    /**
     * User: hanhyu
     * Date: 19-6-23
     * Time: 上午9:14
     *
     * @param string $key
     *
     * @return string|null
     */
    protected function getKey(string $key): ?string
    {
        $datas = $this->db->get($key);
        if ($datas == false) $datas = null;
        return $datas;
    }

    protected function setKey(string $key, string $value): bool
    {
        return $this->db->set($key, $value);
    }

}