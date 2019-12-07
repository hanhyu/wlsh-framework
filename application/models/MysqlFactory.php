<?php
declare(strict_types=1);

namespace App\Models;

use Swoole\Coroutine;
use App\Models\Mysql\{SystemBackupModel,
    SystemMenuModel,
    SystemMsgModel,
    SystemRouterModel,
    SystemUserModel,
    SystemUserLogModel,
    UserLogViewModel
};

class MysqlFactory
{
    private static array $system_user = [];
    private static array $system_user_log = [];
    private static array $userLogView = [];
    private static array $systemRouter = [];
    private static array $systemMsg = [];
    private static array $systemMenu = [];
    private static array $systemBackup = [];

    public static function systemUser(): SystemUserModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$system_user[$cid])) {
            self::$system_user[$cid] = new SystemUserModel();
        }

        defer(static function () use ($cid) {
            unset(self::$system_user[$cid]);
        });

        return self::$system_user[$cid];
    }

    public static function systemUserLog(): SystemUserLogModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$system_user_log[$cid])) {
            self::$system_user_log[$cid] = new SystemUserLogModel();
        }

        defer(static function () use ($cid) {
            unset(self::$system_user_log[$cid]);
        });

        return self::$system_user_log[$cid];
    }

    public static function userLogView(): UserLogViewModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$userLogView[$cid])) {
            self::$userLogView[$cid] = new UserLogViewModel();
        }

        defer(static function () use ($cid) {
            unset(self::$userLogView[$cid]);
        });

        return self::$userLogView[$cid];
    }

    public static function systemRouter(): SystemRouterModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemRouter[$cid])) {
            self::$systemRouter[$cid] = new SystemRouterModel();
        }

        defer(static function () use ($cid) {
            unset(self::$systemRouter[$cid]);
        });

        return self::$systemRouter[$cid];
    }

    public static function systemMsg(): SystemMsgModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemMsg[$cid])) {
            self::$systemMsg[$cid] = new SystemMsgModel();
        }

        defer(static function () use ($cid) {
            unset(self::$systemMsg[$cid]);
        });

        return self::$systemMsg[$cid];
    }

    public static function systemMenu(): SystemMenuModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemMenu[$cid])) {
            self::$systemMenu[$cid] = new SystemMenuModel();
        }

        defer(static function () use ($cid) {
            unset(self::$systemMenu[$cid]);
        });

        return self::$systemMenu[$cid];
    }

    public static function systemBackup(): SystemBackupModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemBackup[$cid])) {
            self::$systemBackup[$cid] = new SystemBackupModel();
        }

        defer(static function () use ($cid) {
            unset(self::$systemBackup[$cid]);
        });

        return self::$systemBackup[$cid];
    }

}
