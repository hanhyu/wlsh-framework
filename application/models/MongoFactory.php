<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Mongo\MonologModel;
use Swoole\Coroutine;

class MongoFactory
{
    /**
     * @var MonologModel
     */
    private static $monolog = [];

    public static function monolog(string $database, string $col): MonologModel
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$monolog[$cid])) {
            self::$monolog[$cid] = new MonologModel($database, $col);
        }

        defer(static function () use ($cid) {
            unset(self::$monolog[$cid]);
        });

        return self::$monolog[$cid];
    }

}
