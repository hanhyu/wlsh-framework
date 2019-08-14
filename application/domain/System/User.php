<?php
declare(strict_types=1);

namespace App\Domain\System;

use App\Models\Mysql\SystemUser;
use App\Models\MysqlFactory;
use Exception;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-3
 * Time: 下午11:36
 */
class User
{

    public function getInfoByName(string $name): array
    {
        return MysqlFactory::systemUser()->getInfo($name);
    }

    public function setUser(array $data): int
    {
        return MysqlFactory::systemUser()->setUser($data);
    }

    /**
     * 在开启Swoole\Runtime::enableCoroutine的情况下，压测结果，协程并行与不使用并行一样。
     * User: hanhyu
     * Date: 2019/8/2
     * Time: 下午3:33
     *
     * @param array $data
     *
     * @return array|null
     */
    public function getInfoList_back(array $data): ?array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }
        $data['where'] = [];
        $chan          = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan) { //获取总数
            try {
                $count = MysqlFactory::systemUser()->getListCount();
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = MysqlFactory::systemUser()->getUserList($data);
                $chan->push(['list' => $list]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });
        for ($i = 0; $i < 2; $i++) {
            $res += $chan->pop(7);
            if (isset($res['500'])) {
                co_log(['exception' => $res['500']], 'getUserListAction mysql异常');
                return null;
            }
        }
        return $res;
    }

    public function getInfoList(array $data): ?array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }
        $data['where'] = [];

        $res['count'] = MysqlFactory::systemUser()->getListCount();
        $res['list']  = MysqlFactory::systemUser()->getUserList($data);

        return $res;
    }

    public function delUser(int $id): int
    {
        return MysqlFactory::systemUser()->delUser($id);
    }

    public function getUserById(int $id): array
    {
        return MysqlFactory::systemUser()->getUser($id);
    }

    /**
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    public function editUser(array $data): int
    {
        return MysqlFactory::systemUser()->editUser($data);
    }

    public function setLoginLog(array $data): void
    {
        MysqlFactory::systemUserLog()->setLoginLog($data);
    }

    public function setLogoutLog(array $data): void
    {
        MysqlFactory::systemUserLog()->setLogoutLog($data);
    }

    /**
     * 获取登录日志列表
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function getLogList(array $data): array
    {
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }

        $data['where'] = [];

        if (!empty($data['login_time'])) {
            $data['where']['login_dt[<>]'] = [
                $data['login_time'],
                date("Y-m-d", strtotime("+1 day", strtotime($data['login_time']))),
            ];
        }

        if (!empty($data['uname'])) {
            $arr_uid                  = MysqlFactory::systemUser()->getInfo($data['uname']);
            $data['where']['user_id'] = $arr_uid[0]['id'] ?? 0;
        }

        $res['count'] = MysqlFactory::systemUserLog()->getListCount($data['where']);

        $list    = MysqlFactory::systemUserLog()->getList($data);
        $arr_uid = array_column($list, 'user_id');

        $arr_name = MysqlFactory::systemUser()->getNameById(array_unique($arr_uid));
        $arr_let  = array_column($arr_name, 'name', 'id');

        foreach ($list as $k => &$v) {
            $list[$k]['user_name'] = $arr_let[$v['user_id']];
            $v['login_ip']         = long2ip((int)$v['login_ip']);
        }
        unset($v);

        $res['list'] = $list;
        return $res;
    }

    public function getLogViewList(array $data): ?array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }

        $data['where'] = [];

        if (!empty($data['login_time'])) {
            $data['where']['login_dt[<>]'] = [
                $data['login_time'],
                date("Y-m-d", strtotime("+1 day", strtotime($data['login_time']))),
            ];
        }

        $data['where']['user_name'] = $data['uname'] ?? 0;


        $res['count'] = MysqlFactory::userLogView()->getListCount($data['where']);
        $res['list']  = MysqlFactory::userLogView()->getList($data);

        return $res;
    }

    /**
     * 压测协程数据结果是否错乱，连接池大小
     * User: hanhyu
     * Date: 2019/7/27
     * Time: 下午10:16
     */
    public function testName(): void
    {
        go(function () {
            $name = MysqlFactory::systemUser()->testNameById(1);
            if ('ceshi001' != $name) print_r('name:' . $name);
        });

        go(function () {
            $name = MysqlFactory::systemUser()->testNameById(7);
            if ('ceshi12' != $name) print_r('name:' . $name);
        });

        go(function () {
            $name2 = MysqlFactory::systemUser()->getNameById([7]);
            if ('ceshi12' != $name2[0]['name']) print_r('name2:' . $name2);
        });

        /*go(function (){
            $name2 = MysqlFactory::systemMenu()->getMenu(5);
        });*/

    }

    public function editPwd(array $data): int
    {
        $pwd = MysqlFactory::systemUser()->getPwdByUid($data['uid']);

        if (!password_verify($data['old_pwd'], $pwd)) {
            $res = -1;
        } else {
            $res = MysqlFactory::systemUser()->editPwd($data);
        }
        return $res;
    }


}