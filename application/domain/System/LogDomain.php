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
     * @return array
     * @throws Exception
     */
    public function getMongoById(string $id): array
    {
        return MonologMongo::getInstance()->getMongoInfo($id);
    }

}
