<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-11
 * Time: 下午5:43
 */

namespace App\Domain\System;

use App\Models\Mysql\SystemMenuMysql;
use App\Models\Redis\LoginRedis;

class MenuDomain
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

        $res['count'] = SystemMenuMysql::getInstance()->getListCount();
        $res['list']  = SystemMenuMysql::getInstance()->getMenuList($data);
        return $res;
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午9:39
     * @return array
     */
    public function getInfo(): array
    {
        return SystemMenuMysql::getInstance()->getMenuInfo();
    }

    public function setMenu(array $data): int
    {
        return SystemMenuMysql::getInstance()->setMenu($data);
    }

    public function getMenuById(int $id): array
    {
        return SystemMenuMysql::getInstance()->getMenu($id);
    }

    public function editMenu(array $data): int
    {
        return SystemMenuMysql::getInstance()->editMenu($data);
    }

    public function delMenu(int $id): int
    {
        return SystemMenuMysql::getInstance()->delMenu($id);
    }

    public function getRedis(string $key): ?string
    {
        return LoginRedis::getInstance()->getKey($key);
    }

}
