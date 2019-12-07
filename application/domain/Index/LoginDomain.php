<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-22
 * Time: 下午9:25
 */

namespace App\Domain\Index;

use App\Models\Redis\LoginModel;

class LoginDomain
{
    /**
     * @var LoginModel
     */
    private LoginModel $login;

    public function __construct()
    {
        $this->login = new LoginModel();
    }

    public function getKey(string $key): ?string
    {
        return $this->login->getKey($key);
    }

}
