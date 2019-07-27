<?php
declare(strict_types=1);

namespace App\Models;

use Swoole\Coroutine;
use App\Models\Mysql\{SystemBackup, SystemMenu, SystemMsg, SystemRouter, SystemUser, SystemUserLog, UserLogView};

class MysqlFactory
{
    private static $system_user = [];
    private static $system_user_log = [];
    private static $userLogView = [];
    private static $systemRouter = [];
    private static $systemMsg = [];
    private static $systemMenu = [];
    private static $systemBackup = [];

    public static function systemUser(): SystemUser
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$system_user[$cid])) {
            self::$system_user[$cid] = new SystemUser();
        }

        defer(function () use ($cid) {
            unset(self::$system_user[$cid]);
        });

        return self::$system_user[$cid];
    }

    public static function systemUserLog(): SystemUserLog
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$system_user_log[$cid])) {
            self::$system_user_log[$cid] = new SystemUserLog();
        }

        defer(function () use ($cid) {
            unset(self::$system_user_log[$cid]);
        });

        return self::$system_user_log[$cid];
    }

    public static function userLogView(): UserLogView
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$userLogView[$cid])) {
            self::$userLogView[$cid] = new UserLogView();
        }

        defer(function () use ($cid) {
            unset(self::$userLogView[$cid]);
        });

        return self::$userLogView[$cid];
    }

    public static function systemRouter(): SystemRouter
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemRouter[$cid])) {
            self::$systemRouter[$cid] = new SystemRouter();
        }

        defer(function () use ($cid) {
            unset(self::$systemRouter[$cid]);
        });

        return self::$systemRouter[$cid];
    }

    public static function systemMsg(): SystemMsg
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemMsg[$cid])) {
            self::$systemMsg[$cid] = new SystemMsg();
        }

        defer(function () use ($cid) {
            unset(self::$systemMsg[$cid]);
        });

        return self::$systemMsg[$cid];
    }

    public static function systemMenu(): SystemMenu
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemMenu[$cid])) {
            self::$systemMenu[$cid] = new SystemMenu();
        }

        defer(function () use ($cid) {
            unset(self::$systemMenu[$cid]);
        });

        return self::$systemMenu[$cid];
    }

    public static function systemBackup(): SystemBackup
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$systemBackup[$cid])) {
            self::$systemBackup[$cid] = new SystemBackup();
        }

        defer(function () use ($cid) {
            unset(self::$systemBackup[$cid]);
        });

        return self::$systemBackup[$cid];
    }

}