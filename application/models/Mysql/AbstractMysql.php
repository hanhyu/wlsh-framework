<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use Exception;
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

    /**
     * User: hanhyu
     * Date: 19-7-9
     * Time: 上午10:40
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $args)
    {
        $mysql_pool_obj = Registry::get('mysql_pool');

        try {

            $this->db = $mysql_pool_obj->get();

            $data = call_user_func_array([$this, $method], $args);

        } catch (\PDOException $e) {
            co_log($e->getMessage(), "mysql数据连接异常", 'alert');

            /**
             * 判断此空闲连接是否已被断开，已断开就重新请求连接，
             * 当检查连接不可用时，就丢弃此连接（pop消息时连接池就没了此连接对象）并重新建立一个新的连接对象，
             * 此功能依赖于mysql的wait_timeout与interactive_timeout两个参数值。
             */
            if (!empty($e->errorInfo) AND ($e->errorInfo[1] == 2006 OR $e->errorInfo[1] == 2013)) {
                $this->db = $mysql_pool_obj->connect();
                $data     = call_user_func_array([$this, $method], $args);
            } else {
                throw new Exception('mysql数据连接异常', 500);
            }
        }

        $mysql_pool_obj->put($this->db);

        return $data;
    }

    /*
     * 当new一个数据对象时，construct方式时qps为8863,call方式时qps8242,连接池都是占用12个
     * 当同时new十个数据对象时，construct方式时2835（占用池子84个）,call方式时还是跟一个对象一样8170(占用池子12个)
     *

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
    */

}