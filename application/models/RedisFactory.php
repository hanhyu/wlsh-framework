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

    public static function login(): Login
    {
        if (!self::$login) {
            self::$login = new Login();
        }
        return self::$login;
    }

}