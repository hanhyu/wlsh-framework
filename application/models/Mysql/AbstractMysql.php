<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use Medoo\Medoo;
use Yaf\Registry;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractMysql
{
    /**
     * @var Medoo
     */
    protected $db;

    /*public function __call($method, $args)
    {
        try {
            $this->db = Registry::get('mysql_pool')->get();
        } catch (\PDOException $e) {
            co_log($e->getMessage(), "mysql数据连接异常", 'alert');
            throw new \Exception('mysql数据连接异常', 500);
        }

        $data = call_user_func_array([$this, $method], $args);

        return $data;
    }*/

    /*
     * 当new一个数据对象时，construct方式时qps为8863,call方式时qps8242,连接池都是占用12个
     * 当同时new十个数据对象时，construct方式时2835（占用池子84个）,call方式时还是跟一个对象一样8170(占用池子12个)
     *
     */
    public function __construct()
    {
        //从数据库连接池中获取连接对象
        try {
            $this->db = Registry::get('mysql_pool')->get();
        } catch (\PDOException $e) {
            co_log($e->getMessage(), "mysql数据连接异常", 'alert');
            throw new \Exception('mysql数据连接异常', 500);
        }
    }



}