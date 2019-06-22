<?php
declare(strict_types=1);

//todo 还需完善ping与流程安全测试
use Swoole\Coroutine\{Channel, MySQL};
use Yaf\Registry;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-27
 * Time: 下午9:31
 */
class CoMysqlPool
{
    /**
     * @var Channel
     */
    protected $ch;

    public function __construct()
    {
        $this->ch = new Channel(100);
    }

    /**
     * 获取mysql连接，如池子内有连接就取一个，连接不够则新建一个。
     * @return MySQL
     * @throws Exception
     */
    public function get(): MySQL
    {
        //有空闲连接
        if ($this->ch->length() > 0) {
            $db = $this->ch->pop(3);
            /**
             * 判断此空闲连接是否已被断开，已断开就重新请求连接，
             * 这里使用channel的pop功能就实现了一个判断池子中的连接是否超过空闲时间，如超时mysql则会自动断开此连接，
             * 当ping检查连接不可用时，就丢弃此连接（pop消息时连接池就没了此连接对象）并重新建立一个新的连接对象，
             * 此功能依赖于mysql的wait_timeout与interactive_timeout两个参数值。
             */
            //todo 可以自定义一个定时器来检测空闲连接或连接时间超时操作
            //if ($db === false OR $this->ping($db->pdo)) goto EOF;
        } else {
            EOF:
            $db  = new MySQL();
            $let = $db->connect([
                'host'     => Registry::get('config')->mysql->host,
                'port'     => Registry::get('config')->mysql->port,
                'user'     => Registry::get('config')->mysql->username,
                'password' => Registry::get('config')->mysql->password,
                'database' => Registry::get('config')->mysql->database,
            ]);

            if (!$let) throw new Exception('Coroutine MySQL connect fail', 500);
        }

        defer(function () use ($db) {
            $this->ch->push($db);
        });

        return $db;
    }

}
