<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Mysql\{SystemBackup, SystemMenu, SystemMsg, SystemRouter, SystemUser, SystemUserLog, UserLogView};

class MysqlFactory
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
     * @var SystemMenu
     */
    private static $systemMenu;
    /**
     * @var SystemBackup
     */
    private static $systemBackup;

    public static function systemUser(): SystemUser
    {
        if (!self::$system_user) {
            self::$system_user = new SystemUser();
        }
        return self::$system_user;
    }

    public static function systemUserLog(): SystemUserLog
    {
        if (!self::$system_user_log) {
            self::$system_user_log = new SystemUserLog();
        }
        return self::$system_user_log;
    }

    public static function userLogView(): UserLogView
    {
        if (!self::$userLogView) {
            self::$userLogView = new UserLogView();
        }
        return self::$userLogView;
    }

    public static function systemRouter(): SystemRouter
    {
        if (!self::$systemRouter) {
            self::$systemRouter = new SystemRouter();
        }
        return self::$systemRouter;
    }

    public static function systemMsg(): SystemMsg
    {
        if (!self::$systemMsg) {
            self::$systemMsg = new SystemMsg();
        }
        return self::$systemMsg;
    }

    public static function systemMenu(): SystemMenu
    {
        if (!self::$systemMenu) {
            self::$systemMenu = new SystemMenu();
        }
        return self::$systemMenu;
    }

    public static function systemBackup(): SystemBackup
    {
        if (!self::$systemBackup) {
            self::$systemBackup = new SystemBackup();
        }
        return self::$systemBackup;
    }

}