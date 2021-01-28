<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-22
 * Time: 下午9:25
 */

namespace App\Domain\Index;

use App\Models\Redis\LoginRedis;
use App\Models\Redis\UserRedis;

class LoginDomain
{
    public function getKey(string $key): bool|string
    {
        return LoginRedis::getInstance()->getKey($key);
    }

    public function existToken(array $data): ?bool
    {
        return UserRedis::getInstance()->existToken($data);
    }
}
