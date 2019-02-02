<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: hanhui
 * Date: 18-2-13
 * Time: 下午9:11
 */
class PdoPool
{
    protected $available = true;
    /**
     * @var \Swoole\Coroutine\Channel
     */
    protected $ch;
    private $config;

    public function __construct(string $db_type)
    {
        $this->ch     = new \Swoole\Coroutine\Channel(300);
        $this->config = \Yaf\Registry::get('config')->$db_type;
    }

    /**
     * 向连接池中存入连接对象，让后面的客户端可以复用则连接。
     *
     * @param \Medoo\Medoo $mysql
     */
    public function put(\Medoo\Medoo $mysql): void
    {
        $this->ch->push($mysql);
    }

    /**
     * 获取mysql连接，如池子内有连接就取一个，连接不够则新建一个。
     * @return \Medoo\Medoo
     */
    public function get(): \Medoo\Medoo
    {
        //有空闲连接
        if ($this->available and $this->ch->stats()['queue_num'] > 0) {
            $db = $this->ch->pop(3);
            /**
             * 判断此空闲连接是否已被断开，已断开就重新请求连接，
             * 这里使用channel的pop功能就实现了一个判断池子中的连接是否超过空闲时间，如超时mysql则会自动断开此连接，
             * 当ping检查连接不可用时，就丢弃此连接（pop消息时连接池就没了此连接对象）并重新建立一个新的连接对象，
             * 此功能依赖于mysql的wait_timeout与interactive_timeout两个参数值。
             */
            //todo 可以自定义一个定时器来检测空闲连接或连接时间超时操作
            if ($db === false or $this->ping($db->pdo)) goto EOF;
        } else {
            EOF:
            $db = new \Medoo\Medoo([
                'database_type' => $this->config['driver'],
                'database_name' => $this->config['database'],
                'server'        => $this->config['host'],
                'port'          => $this->config['port'],
                //'socket' => '/var/run/mysqld/mysqld.sock',
                'username'      => $this->config['username'],
                'password'      => $this->config['password'],
                'charset'       => $this->config['charset'],
                'prefix'        => $this->config['prefix'],
                'logging'       => true,
                'option'        => [
                    PDO::ATTR_CASE                     => PDO::CASE_NATURAL,
                    PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::ATTR_ORACLE_NULLS             => PDO::NULL_TO_STRING,
                    PDO::ATTR_TIMEOUT                  => 3,
                    //PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    //PDO::ATTR_PERSISTENT => true
                ],
            ]);

        }
        return $db;
    }

    /**
     * 检查连接是否可用
     *
     * @param PDO $dbconn 数据库连接
     *
     * @return Boolean ping通了返回false,ping不通返回true
     */
    private function ping(PDO $dbconn): bool
    {
        try {
            $dbconn->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {
            co_log($e->getMessage(), "pdo pool error getMessage：");
            if (!empty($e->errorInfo) AND ($e->errorInfo[1] == 2006 OR $e->errorInfo[1] == 2013)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 连接池销毁, 置不可用状态, 防止新的客户端进入常驻连接池, 导致服务器无法平滑退出
     */
    public function destruct()
    {
        $this->available = false;
        while (!$this->ch->isEmpty()) {
            $this->ch->pop();
        }
    }

}
