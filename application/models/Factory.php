<?php
declare(strict_types=1);

namespace App\Models;


use App\Models\Mysql\SystemUser;

class Factory
{
    /**
     * @var SystemUser
     */
    private static $system_user;

    public static function systemUser()
    {
        if (!self::$system_user) {
            self::$system_user = new SystemUser();
        }
        return self::$system_user;
    }
}