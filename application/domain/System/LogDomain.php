<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-14
 * Time: 下午2:58
 */

namespace App\Domain\System;

use App\Models\Mongo\MonologMongo;
use App\Models\Mysql\RouterLogMysql;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Exception\Exception;

class LogDomain
{

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:28
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function getMongoList(array $data): array
    {
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
                '$gte' => new UTCDateTime($start_time_int * 1000),
                '$lt'  => new UTCDateTime($end_time_int * 1000),
            ];
        }

        if (!empty($data['log_level'])) {
            $data['where']['level_name'] = $data['log_level'];
        }

        if (!empty($data['channel'])) {
            $data['where']['channel'] = $data['channel'];
        }

        $res['count'] = MonologMongo::getInstance()->getMongoCount($data['where']);

        if (0 === $res['count']) {
            $res['list'] = [];
        } else {
            $res['list'] = MonologMongo::getInstance()->getMongoList($data);
        }

        return $res;
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:38
     *
     * @param string $id
     *
     * @return object|null
     */
    public function getMongoById(string $id): ?object
    {
        return MonologMongo::getInstance()->getMongoInfo($id);
    }

    /**
     * 获取路由日志列表
     *
     * User: hanhyu
     * Date: 2021/2/15
     * Time: 下午9:15
     *
     * @param array $data
     *
     * @return array|null
     */
    public function getRouterList(array $data): ?array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }

        if (!empty($data['log_level'])) {
            $data['where']['level'] = strtolower($data['log_level']);
        }

        if (!empty($data['start_time'])) {
            $data['where']['fd_time > ?'] = strtotime($data['start_time']);
            $data['where']['fd_time < ?'] = strtotime($data['end_time']);
        }

        if (!empty($data['trace_id'])) {
            $data['where']['trace_id'] = $data['trace_id'];
        }

        if (!empty($data['req_uri'])) {
            $data['where']['req_uri'] = $data['req_uri'];
        }

        if (!empty($data['req_ip'])) {
            $data['where']['req_ip'] = $data['req_ip'];
        }

        $res['count'] = RouterLogMysql::getInstance()->getListCount($data);
        $res['list']  = RouterLogMysql::getInstance()->getList($data);
        return $res;
    }

}
