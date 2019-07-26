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
    /**
     * @var SystemUser
     */
    private $system_user;

    public function __construct()
    {
        $this->system_user = new SystemUser();
    }

    public function getInfoByName(string $name): array
    {
        return MysqlFactory::systemUser()->getInfo($name);
    }

    public function setUser(array $data): int
    {
        return MysqlFactory::systemUser()->setUser($data);
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

        $res['count'] = $this->system_user->getListCount();
        $res['list']  = $this->system_user->getUserList($data);

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
        go(function () use ($data) {
            MysqlFactory::systemUserLog()->setLoginLog($data);
        });
    }

    public function setLogoutLog(array $data): void
    {
        go(function () use ($data) {
            MysqlFactory::systemUserLog()->setLogoutLog($data);
        });
    }

    /**
     * 获取登录日志列表
     *
     * @param array $data
     *
     * @return array|null
     * @throws \Exception
     */
    public function getLogList(array $data): ?array
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

        if (!empty($data['uname'])) {
            $arr_uid                  = MysqlFactory::systemUser()->getInfo($data['uname']);
            $data['where']['user_id'] = $arr_uid[0]['id'] ?? 0;
        }

        $chan = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan, $data) { //获取总数
            try {
                $count = MysqlFactory::systemUserLog()->getListCount($data['where']);
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list    = MysqlFactory::systemUserLog()->getList($data);
                $arr_uid = array_column($list, 'user_id');

                $arr_name = MysqlFactory::systemUser()->getNameById(array_unique($arr_uid));
                $arr_let  = array_column($arr_name, 'name', 'id');

                foreach ($list as $k => &$v) {
                    $list[$k]['user_name'] = $arr_let[$v['user_id']];
                    $v['login_ip']         = long2ip((int)$v['login_ip']);
                }
                unset($v);

                $chan->push(['list' => $list]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });

        for ($i = 0; $i < 2; $i++) {
            $res += $chan->pop(7);
            if (isset($res['500'])) {
                co_log(['exception' => $res['500']], 'getLogList mysql异常');
                return null;
            }
        }

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

        $chan = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan, $data) { //获取总数
            try {
                $count = MysqlFactory::userLogView()->getListCount($data['where']);
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = MysqlFactory::userLogView()->getList($data);
                $chan->push(['list' => $list]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });

        for ($i = 0; $i < 2; $i++) {
            $res += $chan->pop(7);
            if (isset($res['500'])) {
                co_log(['exception' => $res['500']], 'getLogList mysql异常');
                return null;
            }
        }

        return $res;
    }

}