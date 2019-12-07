<?php
declare(strict_types=1);

namespace App\Library;

use PDO;
use PDOException;
use Swoole\Coroutine\Channel;
use Medoo\Medoo;

/**
 * Created by PhpStorm.
 * UserDomain: hanhui
 * Date: 18-2-13
 * Time: 下午9:11
 */
class PdoPool
{
    /**
     * @var Channel
     */
    protected Channel $ch;
    private array $config;

    /**
     * 每个进程默认生成5个长连接对象,运行中不够则自动扩容
     * PdoPool constructor.
     *
     * @param string $db_type
     *
     * @throws PDOException
     */
    public function __construct(string $db_type)
    {
        $this->ch     = new Channel(300);
        $this->config = DI::get('config_arr')[$db_type];
        try {
            for ($i = 0; $i < 5; $i++) {
                $this->ch->push($this->connect());
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * 获取mysql连接，如池子内有连接就取一个，连接不够则新建一个。
     * @return Medoo
     */
    public function get(): Medoo
    {
        $db = $this->ch->pop(3);
        /**
         * 判断此空闲连接是否已被断开，已断开就重新请求连接，
         * 这里使用channel的pop功能就实现了一个判断池子中的连接是否超过空闲时间，如超时mysql则会自动断开此连接，
         * 当ping检查连接不可用时，就丢弃此连接（pop消息时连接池就没了此连接对象）并重新建立一个新的连接对象，
         * 此功能依赖于mysql的wait_timeout与interactive_timeout两个参数值。
         */
        if ($db === false) {
            $db = $this->connect();
        }

        /*
         * 这种合并写法，池子性能降低10%
         if ($db != false and $this->ping($db->pdo)) {
            $db = $this->connect();
        }
        */

        //延迟向连接池中存入连接对象，让后面的客户端可以复用此连接。
        /* defer(function () use ($db) {
             $this->ch->push($db);
         });*/

        return $db;
    }

    public function put(Medoo $db): void
    {
        $this->ch->push($db);
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-7-5
     * Time: 上午10:52
     * @return Medoo
     */
    public function connect(): Medoo
    {
        try {
            $db = new Medoo([
                'database_type' => $this->config['driver'],
                'database_name' => $this->config['database'],
                'server'        => $this->config['host'],
                'port'          => $this->config['port'],
                //'socket' => '/var/run/mysqld/mysqld.sock',
                'username'      => $this->config['username'],
                'password'      => $this->config['password'],
                'charset'       => $this->config['charset'],
                'prefix'        => $this->config['prefix'],
                //此参数在连接池功能中必须设置为false，否则会造成内存泄露。
                'logging'       => false,
                'option'        => [
                    PDO::ATTR_CASE                     => PDO::CASE_NATURAL,
                    PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::ATTR_ORACLE_NULLS             => PDO::NULL_TO_STRING,
                    PDO::ATTR_TIMEOUT                  => 3,
                    PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
                    //PDO::ATTR_PERSISTENT => true
                ],
                'command'       => [
                    'SET SQL_MODE=ANSI_QUOTES',
                ],
            ]);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }

        return $db;
    }

    /**
     * 获取连接池使用状态
     * UserDomain: hanhyu
     * Date: 19-7-17
     * Time: 上午11:09
     * @return array
     */
    public function getStatus(): array
    {
        return $this->ch->stats();
    }

    /**
     * 检查连接是否可用
     *
     * @param PDO $dbconn 数据库连接
     *
     * @return bool ping通了返回false,ping不通返回true
     */
    public function ping(PDO $dbconn): bool
    {
        try {
            $dbconn->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            co_log($e->getMessage(), 'pdo pool error getMessage：');
            if (!empty($e->errorInfo) and ($e->errorInfo[1] === 2006 or $e->errorInfo[1] === 2013)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 连接池销毁, 置不可用状态, 防止新的客户端进入常驻连接池, 导致服务器无法平滑退出
     *
     * public function destruct()
     * {
     * echo 'destruct1';
     * $this->available = false;
     * var_dump($this->available);
     * while (!$this->ch->isEmpty()) {
     * $this->ch->pop();
     * }
     * }
     *
     *
     */

}
