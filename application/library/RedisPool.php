<?php
declare(strict_types=1);

namespace App\Library;

use RedisException;
use Swoole\Coroutine\Channel;
use Redis;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-11-6
 * Time: 上午10:37
 */
class RedisPool
{
    /**
     * @var Channel
     */
    protected Channel $ch;

    /**
     * 每个进程默认生成5个长连接对象,运行中不够则自动扩容
     * RedisPool constructor.
     *
     * @param int $pool_min 启动的单进程中初始化默认最小连接池为5
     * @param int $pool_max 启动的单进程中初始化默认最大连接池为100
     *
     * @throws RedisException
     */
    public function __construct(int $pool_min = 5, int $pool_max = 100)
    {
        $this->ch = new Channel($pool_max);
        try {
            for ($i = 0; $i < $pool_min; $i++) {
                $this->ch->push($this->connect());
            }
        } catch (RedisException $e) {
            throw new RedisException($e->getMessage());
        }
    }

    /**
     * 获取redis连接，如池子内有连接就取一个，连接不够则新建一个。
     *
     * @return Redis
     * @throws RedisException
     */
    public function get(): Redis
    {
        $db = $this->ch->pop(1);
        /**
         * 判断此空闲连接是否已被断开，已断开就重新请求连接，
         * 这里使用channel的pop功能就实现了一个判断池子中的连接是否超过空闲时间，如超时redis则会自动断开此连接，
         * 当ping检查连接不可用时，就丢弃此连接（pop消息时连接池就没了此连接对象）并重新建立一个新的连接对象，
         * 此功能依赖于redis的timeout参数值。
         */
        if ($db === false) {
            $db = $this->connect();
        }

        /*
         * 每次提前检测一下该池子中的连接是否可用，压测性能：
         * 1、在使用单例工厂模式下不影响性能
         * 2、普通模式下性能降低20%
         */
        //if ($this->ping($db)) $db = $this->connect();

        /*
         * 这种合并写法，池子性能降低10%
         if ($db != false and $this->ping($db)) {
            $db = $this->connect();
        }
        */

        //延迟向连接池中存入连接对象，让后面的客户端可以复用此连接。
        /* defer(function () use ($db) {
             $this->ch->push($db);
         });*/

        return $db;
    }

    public function put(Redis $db): void
    {
        $this->ch->push($db);
    }


    /**
     * UserDomain: hanhyu
     * Date: 19-7-5
     * Time: 上午11:29
     * @return Redis
     * @throws RedisException
     */
    public function connect(): Redis
    {
        $redis_conf = DI::get('config_arr')['redis'];
        $db         = new Redis();
        $res        = $db->connect($redis_conf['host'], $redis_conf['port']);
        if (!$res) {
            throw new RedisException('redis数据连接异常');
        }
        $db->auth($redis_conf['auth']);

        return $db;
    }

    /**
     * 检查连接是否可用
     *
     * @param Redis $dbconn 数据库连接
     *
     * @return bool ping通了返回false,ping不通返回true
     */
    public function ping(Redis $dbconn): bool
    {
        try {
            $dbconn->ping();
        } catch (RedisException $e) {
            co_log($e->getMessage(), 'redis pool error getMessage：');
            //co_log($e->getCode(), "redis pool error getCode：");
            return true;
        }
        return false;
    }

}
