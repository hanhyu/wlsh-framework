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
    private static array $model = [];

    public static function systemUser(): SystemUserModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$model['system_user'][$cid])) {
            self::$model['system_user'][$cid] = new SystemUserModel();
        }

        defer(static function () use ($cid) {
            unset(self::$model['system_user'][$cid]);
        });

        return self::$model['system_user'][$cid];
    }

    public static function systemUserLog(): SystemUserLogModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$model['system_user_log'][$cid])) {
            self::$model['system_user_log'][$cid] = new SystemUserLogModel();
        }

        defer(static function () use ($cid) {
            unset(self::$model['system_user_log'][$cid]);
        });

        return self::$model['system_user_log'][$cid];
    }

    public static function userLogView(): UserLogViewModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$model['userLogView'][$cid])) {
            self::$model['userLogView'][$cid] = new UserLogViewModel();
        }

        defer(static function () use ($cid) {
            unset(self::$model['userLogView'][$cid]);
        });

        return self::$model['userLogView'][$cid];
    }

    public static function systemRouter(): SystemRouterModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$model['systemRouter'][$cid])) {
            self::$model['systemRouter'][$cid] = new SystemRouterModel();
        }

        defer(static function () use ($cid) {
            unset(self::$model['systemRouter'][$cid]);
        });

        return self::$model['systemRouter'][$cid];
    }

    public static function systemMsg(): SystemMsgModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$model['systemMsg'][$cid])) {
            self::$model['systemMsg'][$cid] = new SystemMsgModel();
        }

        defer(static function () use ($cid) {
            unset(self::$model['systemMsg'][$cid]);
        });

        return self::$model['systemMsg'][$cid];
    }

    public static function systemMenu(): SystemMenuModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$model['systemMenu'][$cid])) {
            self::$model['systemMenu'][$cid] = new SystemMenuModel();
        }

        defer(static function () use ($cid) {
            unset(self::$model['systemMenu'][$cid]);
        });

        return self::$model['systemMenu'][$cid];
    }

    public static function systemBackup(): SystemBackupModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$model['systemBackup'][$cid])) {
            self::$model['systemBackup'][$cid] = new SystemBackupModel();
        }

        defer(static function () use ($cid) {
            unset(self::$model['systemBackup'][$cid]);
        });

        return self::$model['systemBackup'][$cid];
    }

}
