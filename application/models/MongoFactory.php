<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Mongo\Monolog;
use Swoole\Coroutine;

class MongoFactory
{
    /**
     * @var Monolog
     */
    private static $monolog = [];

    public static function monolog(string $database, string $col)
    {
        $cid = Coroutine::getCid();
        if (!isset(self::$monolog[$cid])) {
            self::$monolog[$cid] = new Monolog($database, $col);
        }

        defer(function () use ($cid) {
            unset(self::$monolog[$cid]);
        });

        return self::$monolog[$cid];
    }

}