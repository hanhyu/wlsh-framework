<?php
declare(strict_types=1);

namespace App\Models\Mongo;

use Yaf\Registry;
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractMongo
{
    /**
     * @var \MongoDB\Client
     */
    protected $col;

    /**
     * php7中mongodb扩展会自动释放连接
     * AbstractMongo constructor.
     *
     * @param string $database
     * @param string $col
     */
    public function __construct(string $database, string $col)
    {
        $db        = new \MongoDB\Client(
            Registry::get('config')->log->mongo,
            [
                'username'   => Registry::get('config')->log->username,
                'password'   => Registry::get('config')->log->pwd,
                'authSource' => Registry::get('config')->log->database,
            ]);
        $this->col = $db->selectCollection($database, $col);
    }

}