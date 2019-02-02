<?php
declare(strict_types=1);

namespace App\Models\Mysql;
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractMysql
{
    /**
     * @var \Medoo\Medoo
     */
    protected $db;

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        try {
            $this->db = \Yaf\Registry::get('mysql_pool')->get();
        } catch (\PDOException $e) {
            co_log($e->getMessage(), "mysql数据连接异常", 'alert');
            throw new \Exception('mysql数据连接异常', 500);
        }

        $data = call_user_func_array([$this, $method], $args);

        if (!empty($this->db)) {
            \Yaf\Registry::get('mysql_pool')->put($this->db);
        }

        return $data;
    }

    /*public function __call($method, $args)
    {
        $chan = new \SplQueue();
        go(function () use ($chan) {
            try{
                $chan->push(\Yaf\Registry::get('mysql_pool')->get());
            } catch (\PDOException $e){
                co_log($e->getMessage(), "mysql数据连接异常");
                $chan->push($e->getMessage());
            }

        });

        while (1) {
            if (count($chan) > 0) {
                $data = $chan->pop();
                if (is_string($data)){
                    throw new \Exception($data);
                }
                $this->db = $data;
                break;
            }
        }

        $data = call_user_func_array([$this, $method], $args);

        go(function () {
            \Yaf\Registry::get('mysql_pool')->put($this->db);
        });

        return $data;
    }*/

    /*
     * 当new一个数据对象时，construct方式时qps为8863,call方式时qps8242,连接池都是占用12个
     * 当同时new十个数据对象时，construct方式时2835（占用池子84个）,call方式时还是跟一个对象一样8170(占用池子12个)
     *  public function __construct()
        {
            //从数据库连接池中获取连接对象
            $this->db = \Yaf\Registry::get('mysql_pool')->get();
        }

        public function __destruct()
        {
            //把用完的连接对象放回池子中供下一个连接重复使用
            \Yaf\Registry::get('mysql_pool')->put($this->db);
        }

    */

}