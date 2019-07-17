<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-14
 * Time: 下午2:58
 */

namespace App\Domain\System;

use App\Models\MongoFactory;
use App\Models\Mongo\Monolog;

class Log
{
    /**
     * @var Monolog
     */
    protected $monolog;

    public function __construct()
    {
        $this->monolog = MongoFactory::monolog('baseFrame', 'monolog');
    }

    public function getMongoList(array $data): ?array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }
        $data['where'] = [];

        if (!empty($data['start_time'])) {
            $start_time_int            = strtotime($data['start_time']);
            $end_time_int              = strtotime($data['end_time']);
            $data['where']['datetime'] = [
                '$gte' => new \MongoDB\BSON\UTCDateTime($start_time_int * 1000),
                '$lt'  => new \MongoDB\BSON\UTCDateTime($end_time_int * 1000),
            ];
        }

        if (!empty($data['channel'])) $data['where']['channel'] = $data['channel'];

        $chan = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan, $data) { //获取总数
            try {
                $count = $this->monolog->getMongoCount($data['where']);
                $chan->push(['count' => $count]);
            } catch (\Exception $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = $this->monolog->getMongoList($data);
                $chan->push(['list' => $list]);
            } catch (\Exception $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });

        for ($i = 0; $i < 2; $i++) {
            $res += $chan->pop(7);
            if (isset($res['500'])) {
                co_log(['exception' => $res['500']], 'getUserListAction mysql异常');
                return null;
            }
        }
        return $res;
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午9:38
     *
     * @param string $id
     *
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getMongoById(string $id): array
    {
        return $this->monolog->getMongoInfo($id);
    }

}