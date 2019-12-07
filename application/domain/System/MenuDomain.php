<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-11
 * Time: 下午5:43
 */

namespace App\Domain\System;

use App\Models\MysqlFactory;
use App\Models\Redis\LoginModel;

class MenuDomain
{
    /**
     * UserDomain: hanhyu
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

        $res['count'] = MysqlFactory::systemMenu()->getListCount();
        $res['list']  = MysqlFactory::systemMenu()->getMenuList($data);

        return $res;
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:39
     * @return array
     */
    public function getInfo(): array
    {
        return MysqlFactory::systemMenu()->getMenuInfo();
    }

    public function setMenu(array $data): int
    {
        return MysqlFactory::systemMenu()->setMenu($data);
    }

    public function getMenuById(int $id): array
    {
        return MysqlFactory::systemMenu()->getMenu($id);
    }

    public function editMenu(array $data): int
    {
        return MysqlFactory::systemMenu()->editMenu($data);
    }

    public function delMenu(int $id): int
    {
        return MysqlFactory::systemMenu()->delMenu($id);
    }

    public function getRedis(string $key): ?string
    {
        $redis = new LoginModel();
        return $redis->getKey($key);
    }

}
