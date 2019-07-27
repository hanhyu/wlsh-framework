<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-22
 * Time: 下午9:25
 */

namespace App\Domain\Index;

use App\Models\Redis\Login as LoginModel;

class Login
{
    /**
     * @var LoginModel
     */
    private $login;

    public function __construct()
    {
        $this->login = new LoginModel();
    }

    public function getKey(string $key): ?string
    {
        return $this->login->getKey($key);
    }

}