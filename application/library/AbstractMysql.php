<?php declare(strict_types=1);

namespace App\Library;

use Exception;
use Medoo\Medoo;
use PDOException;
use RuntimeException;
use Swoole\Coroutine;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-10-28
 * Time: 下午3:34
 */
abstract class AbstractMysql
{
    private static array $instance = [];
    protected Medoo $db;
    /**
     * 此处使用静态延迟绑定，实现选择不同的数据库
     * @var string
     */
    protected static string $db_schema = 'mysql_pool_obj';

    /**
     * UserDomain: hanhyu
     * Date: 19-7-9
     * Time: 上午10:40
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $args): mixed
    {
        /** @var $mysql_pool_obj PdoPool */
        $mysql_pool_obj = DI::get(static::$db_schema);
        try {
            if (!$mysql_pool_obj->available) return '';

            $this->db = $mysql_pool_obj->get();
            $data     = call_user_func_array([$this, $method], $args);
        } catch (PDOException $e) {
            co_log($e->getMessage(), '连接池中mysql服务端断开连接，重新请求连接。', 'alert');

            /**
             * 判断此空闲连接是否已被断开，已断开就重新请求连接，
             * 当检查连接不可用时，就丢弃此连接（pop消息时连接池就没了此连接对象）并重新建立一个新的连接对象，
             * 此功能依赖于mysql的wait_timeout与interactive_timeout两个参数值。
             */
            if (!empty($e->errorInfo) and ($e->errorInfo[1] === 2006 or $e->errorInfo[1] === 2013)) {
                sleep(3);
                $this->db = $mysql_pool_obj->connect();
                $data     = call_user_func_array([$this, $method], $args);
            } else {
                throw new RuntimeException($e->getMessage(), 500);
            }
        }

        if (APP_DEBUG) {
            $debugInfo['sql'] = $this->db->last();

            if (preg_match("/^(SELECT )/i", $debugInfo['sql'])) {
                $explain[] = $this->db->query('EXPLAIN ' . $debugInfo['sql'])->fetch();

                if (!empty($explain)) {
                    $debugInfo['explain'] = $explain;
                }
            }

            task_log(DI::get('server_obj'), $debugInfo, 'sql', 'mysql');
        }

        //只能使用 __call方法或子协程 实现快速回收连接池资源, 此方法不能放在finally中
        $mysql_pool_obj->put($this->db);

        return $data;
    }

    public static function getInstance(): static
    {
        $class_name = static::class;
        $cid        = Coroutine::getCid();
        if (!isset(static::$instance[$class_name][$cid])) {
            //new static()与new static::class一样，但为了IDE友好提示类中的方法，需要用new static()
            $_instance = static::$instance[$class_name][$cid] = new static();
        } else {
            $_instance = static::$instance[$class_name][$cid];
        }

        defer(static function () use ($class_name, $cid) {
            unset(static::$instance[$class_name][$cid]);
        });

        //为了IDE代码提示功能
        return $_instance;
    }

    private function __construct()
    {
    }

}
