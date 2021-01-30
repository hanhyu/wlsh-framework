<?php
declare(strict_types=1);

namespace App\Models\Redis;

use App\Library\AbstractRedis;
use RedisException;

/**
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
    protected static int $db_index = 1;

    /**
     * UserDomain: hanhyu
     * Date: 19-6-23
     * Time: 上午9:14
     *
     * @param string $key
     *
     * @return bool|string
     * @throws RedisException
     */
    public function getKey(string $key): bool|string
    {
        return self::getDb()->get($key);
    }

    public function setKey(string $key, string $value): bool
    {
        return self::getDb()->set($key, $value);
    }

}
