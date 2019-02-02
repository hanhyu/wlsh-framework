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
     * @var \MongoDB\Driver\Manager
     */
    protected $db;

    /**
     * php7中mongodb扩展会自动释放连接
     * AbstractModel constructor.
     */
    public function __construct()
    {
        $this->db = new \MongoDB\Driver\Manager(\Yaf\Registry::get('config')->log->mongo);
    }

}