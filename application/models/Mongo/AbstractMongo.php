<?php
declare(strict_types=1);

namespace App\Models\Mongo;

use App\Library\DI;
use MongoDB\Client;
use MongoDB\Collection;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractMongo
{
    /**
     * @var Collection
     */
    protected Collection $col;

    /**
     * php7中mongodb扩展会自动释放连接
     * AbstractMongo constructor.
     *
     * @param string $database
     * @param string $col
     */
    public function __construct(string $database, string $col)
    {
        $log_arr   = DI::get('config_arr')['log'];
        $db        = new Client($log_arr['mongo'],
            [
                'username'   => $log_arr['username'],
                'password'   => $log_arr['pwd'],
                'authSource' => $log_arr['database'],
            ]);
        $this->col = $db->selectCollection($database, $col);
    }

}
