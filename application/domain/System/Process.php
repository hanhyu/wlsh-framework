<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-2-1
 * Time: 下午5:45
 */

namespace App\Domain\System;

use App\Models\MysqlFactory;

class Process
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

        if (!empty($data['id'])) $data['where']['id'] = $data['id'];

        $chan = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan, $data) { //获取总数
            try {
                $count = MysqlFactory::systemMsg()->getListCount($data['where']);
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = MysqlFactory::systemMsg()->getList($data);
                $chan->push(['list' => $list]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });

        for ($i = 0; $i < 2; $i++) {
            $res += $chan->pop(7);
            if (isset($res['500'])) {
                co_log(['exception' => $res['500']], 'getMsgList mysql异常');
                return null;
            }
        }

        return $res;
    }

    public function getMongoById(string $id): array
    {
        return MysqlFactory::monolog()->getMongoInfo($id);
    }

    public function setMsg(array $data): int
    {
        return MysqlFactory::systemMsg()->setMsg($data);
    }

}