<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-11
 * Time: 下午5:43
 */

namespace App\Domain\System;

use App\Models\Factory;
use App\Models\Redis\Login;

class Menu
{
    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午9:38
     *
     * @param array $data
     *
     * @return array|null
     */
    public function getList(array $data): ?array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }

        $data['where'] = [];

        $chan = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan) { //获取总数
            try {
                $count = Factory::systemMenu()->getListCount();
                $chan->push(['count' => $count]);
            } catch (\Exception $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = Factory::systemMenu()->getMenuList($data);
                $chan->push(['list' => $list]);
            } catch (\Exception $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });

        for ($i = 0; $i < 2; $i++) {
            $res += $chan->pop(7);
            if (isset($res['500'])) {
                co_log(
                    ['message' => $res['500']],
                    '协程mysql异常：' . __FILE__ . ':' . __LINE__,
                    'mysql'
                );
                return null;
            }
        }
        return $res;
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午9:39
     * @return array
     * @throws \Exception
     */
    public function getInfo(): array
    {
        return Factory::systemMenu()->getMenuInfo();
    }

    public function setMenu(array $data): int
    {
        return Factory::systemMenu()->setMenu($data);
    }

    public function getMenuById(int $id): array
    {
        return Factory::systemMenu()->getMenu($id);
    }

    public function editMenu(array $data): int
    {
        return Factory::systemMenu()->editMenu($data);
    }

    public function delMenu(int $id): int
    {
        return Factory::systemMenu()->delMenu($id);
    }

    public function getRedis(string $key): ?string
    {
        $redis = new Login();
        return $redis->getKey($key);
    }

}