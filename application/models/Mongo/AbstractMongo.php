<?php
declare(strict_types=1);

namespace App\Models\Mongo;
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
        $db        = new \MongoDB\Client(\Yaf\Registry::get('config')->log->mongo);
        $this->col = $db->selectCollection($database, $col);
    }

}