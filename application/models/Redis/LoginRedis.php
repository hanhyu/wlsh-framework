<?php
declare(strict_types=1);

namespace App\Models\Redis;

use App\Library\AbstractRedis;

/**
 * @property null|string getKey
 * @property bool        setKey
 *
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-22
 * Time: 下午9:05
 */
class LoginRedis extends AbstractRedis
{
    /**
     * 此处使用静态延迟绑定，实现选择不同的数据库,如不设置默认为0
     * @var int
     */
    protected static int $dbindex = 1;

    /**
     * UserDomain: hanhyu
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
        if (false === $datas) {
            $datas = null;
        }
        return $datas;
    }

    protected function setKey(string $key, string $value): bool
    {
        return $this->db->set($key, $value);
    }

}
