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
                '$gte' => new \MongoDB\BSON\UTCDateTime($start_time_int * 1000),
                '$lt'  => new \MongoDB\BSON\UTCDateTime($end_time_int * 1000),
            ];
        }

        if (!empty($data['channel'])) $data['where']['channel'] = $data['channel'];

        $res['count'] = $this->monolog->getMongoCount($data['where']);
        if (0 == $res['count']) {
            $res['list'] = [];
        } else {
            $res['list'] = $this->monolog->getMongoList($data);
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