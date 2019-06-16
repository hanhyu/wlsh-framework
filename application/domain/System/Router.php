<?php
declare(strict_types=1);

namespace App\Domain\System;

use App\Models\Mysql\SystemRouter;

class Router
{
    /**
     * @var SystemRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = new SystemRouter();
    }

    /**
     * 获取路由列表数据
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

        $data['where'] = [];

        $chan = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan) { //获取总数
            try {
                $count = $this->router->getListCount();
                $chan->push(['count' => $count]);
            } catch (\Exception $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = $this->router->getList($data);
                $chan->push(['list' => $list]);
            } catch (\Exception $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });

        for ($i = 0; $i < 2; $i++) {
            $res += $chan->pop(7);
            if (isset($res['500'])) {
                co_log(['exception' => $res['500']], '获取系统用户路由异常');
                return null;
            }
        }
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
     * @throws \Exception
     */
    public function setRouter(array $data): int
    {
        return $this->router->setRouter($data);
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
     * @throws \Exception
     */
    public function editRouter(array $data): int
    {
        return $this->router->editRouter($data);
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
     * @throws \Exception
     */
    public function delRouter(int $id): int
    {
        return $this->router->delRouter($id);
    }

    public function getInfo(): array
    {
        $res  = $this->router->getInfo();
        $list = [];
        foreach ($res as $k => $v) {
            $list[$v['menu_name']][] = $v;
        }
        return $list;
    }

}