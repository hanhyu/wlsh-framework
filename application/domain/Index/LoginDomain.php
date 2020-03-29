<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-22
 * Time: 下午9:25
 */

namespace App\Domain\Index;

use App\Models\Redis\LoginRedis;

class LoginDomain
{
    public function getKey(string $key): ?string
    {
        return LoginRedis::getInstance()->getKey($key);
    }

}
