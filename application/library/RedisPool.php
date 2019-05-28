<?php
declare(strict_types=1);

use Swoole\Coroutine\Channel;
use Yaf\Registry;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-11-6
 * Time: 上午10:37
 */
class RedisPool
{
    /**
     * @var Channel
     */
    protected $ch;

    public function __construct()
    {
        $this->ch = new Channel(300);
    }

    /**
     * 获取redis连接，如池子内有连接就取一个，连接不够则新建一个。
     * @return \Redis
     */
    public function get(): \Redis
    {
        //有空闲连接
        if ($this->ch->length() > 0) {
            $db = $this->ch->pop(3);
            /**
             * 判断此空闲连接是否已被断开，已断开就重新请求连接，
             * 这里使用channel的pop功能就实现了一个判断池子中的连接是否超过空闲时间，如超时redis则会自动断开此连接，
             * 当ping检查连接不可用时，就丢弃此连接（pop消息时连接池就没了此连接对象）并重新建立一个新的连接对象，
             * 此功能依赖于redis的timeout参数值。
             */
            if ($db === false or $this->ping($db)) goto EOF;
        } else {
            EOF:
            $db = new \Redis();
            $db->connect(Registry::get('config')->cache->host, (int)Registry::get('config')->cache->port);
            $db->auth(Registry::get('config')->cache->auth);
        }

        //向连接池中存入连接对象，让后面的客户端可以复用则连接。
        defer(function () use ($db) {
            $this->ch->push($db);
        });

        return $db;
    }

    /**
     * 检查连接是否可用
     *
     * @param Redis $dbconn 数据库连接
     *
     * @return bool ping通了返回false,ping不通返回true
     */
    private function ping(Redis $dbconn): bool
    {
        try {
            $dbconn->ping();
        } catch (RedisException $e) {
            co_log($e->getMessage(), "redis pool error getMessage：");
            //co_log($e->getCode(), "redis pool error getCode：");
            return true;
        }
        return false;
    }

}