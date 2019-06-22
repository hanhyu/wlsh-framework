<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Mongo\Monolog;

class MongoFactory
{
    /**
     * @var Monolog
     */
    private static $monolog;

    public static function monolog(string $database, string $col)
    {
        if (!self::$monolog) {
            self::$monolog = new Monolog($database, $col);
        }
        return self::$monolog;
    }

}