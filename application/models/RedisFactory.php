<?php
declare(strict_types=1);


namespace App\Models;


use App\Models\Redis\Login;

class RedisFactory
{
    /**
     * @var Login
     */
    private static $login;

    /**
     * User: hanhyu
     * Date: 19-7-8
     * Time: 下午8:27
     * @return Login
     * @throws \Exception
     */
    public static function login(): Login
    {
        if (!self::$login) {
            self::$login = new Login();
        }
        return self::$login;
    }

}