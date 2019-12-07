<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-2-1
 * Time: 下午5:45
 */

namespace App\Domain\System;

use App\Models\MysqlFactory;

class ProcessDomain
{
    public function getMsgList(array $data): ?array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }

        $data['where'] = [];

        if (!empty($data['id'])) {
            $data['where']['id'] = $data['id'];
        }

        $res['count'] = MysqlFactory::systemMsg()->getListCount($data['where']);
        $res['list']  = MysqlFactory::systemMsg()->getList($data);

        return $res;
    }

    public function setMsg(array $data): int
    {
        return MysqlFactory::systemMsg()->setMsg($data);
    }

}
