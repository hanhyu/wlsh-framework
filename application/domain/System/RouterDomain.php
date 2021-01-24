<?php
declare(strict_types=1);

namespace App\Domain\System;

use App\Models\Mysql\SystemRouterMysql;

class RouterDomain
{
    /**
     * 获取路由列表数据
     *
     * User: hanhyu
     * Date: 19-4-28
     * Time: 上午9:51
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

        $res['count'] = SystemRouterMysql::getInstance()->getListCount();
        $res['list']  = SystemRouterMysql::getInstance()->getList($data);

        return $res;
    }


    /**
     * 添加路由
     * User: hanhyu
     * Date: 19-4-28
     * Time: 上午11:05
     *
     * @param array $data
     *
     * @return int
     */
    public function setRouter(array $data): int
    {
        return SystemRouterMysql::getInstance()->setRouter($data);
    }

    /**
     * 修改路由
     * User: hanhyu
     * Date: 19-4-28
     * Time: 上午11:07
     *
     * @param array $data
     *
     * @return int
     */
    public function editRouter(array $data): int
    {
        return SystemRouterMysql::getInstance()->editRouter($data);
    }


    /**
     * 删除路由
     * User: hanhyu
     * Date: 19-4-28
     * Time: 上午11:06
     *
     * @param int $id
     *
     * @return int
     */
    public function delRouter(int $id): int
    {
        return SystemRouterMysql::getInstance()->delRouter($id);
    }

    public function getInfo(): array
    {
        $res  = SystemRouterMysql::getInstance()->getInfo();
        $list = [];
        foreach ($res as $k => $v) {
            $list[$v['menu_name']][] = $v;
        }
        return $list;
    }

}
