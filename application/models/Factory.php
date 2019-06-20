<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Mongo\Monolog;
use App\Models\Mysql\{SystemMenu, SystemMsg, SystemRouter, SystemUser, SystemUserLog, UserLogView};

class Factory
{
    /**
     * @var SystemUser
     */
    private static $system_user;
    /**
     * @var SystemUserLog
     */
    private static $system_user_log;
    /**
     * @var UserLogView
     */
    private static $userLogView;
    /**
     * @var SystemRouter
     */
    private static $systemRouter;
    /**
     * @var SystemMsg
     */
    private static $systemMsg;
    /**
     * @var Monolog
     */
    private static $monolog;
    /**
     * @var SystemMenu
     */
    private static $systemMenu;

    public static function systemUser()
    {
        if (!self::$system_user) {
            self::$system_user = new SystemUser();
        }
        return self::$system_user;
    }

    public static function systemUserLog()
    {
        if (!self::$system_user_log) {
            self::$system_user_log = new SystemUserLog();
        }
        return self::$system_user_log;
    }

    public static function userLogView()
    {
        if (!self::$userLogView) {
            self::$userLogView = new UserLogView();
        }
        return self::$userLogView;
    }

    public static function systemRouter()
    {
        if (!self::$systemRouter) {
            self::$systemRouter = new SystemRouter();
        }
        return self::$systemRouter;
    }

    public static function systemMsg()
    {
        if (!self::$systemMsg) {
            self::$systemMsg = new SystemMsg();
        }
        return self::$systemMsg;
    }

    public static function monolog(string $database, string $col)
    {
        if (!self::$monolog) {
            self::$monolog = new Monolog($database, $col);
        }
        return self::$monolog;
    }

    public static function systemMenu()
    {
        if (!self::$systemMenu) {
            self::$systemMenu = new SystemMenu();
        }
        return self::$systemMenu;
    }

}