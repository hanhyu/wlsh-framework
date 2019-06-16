<?php
declare(strict_types=1);

namespace App\Domain\System;

use App\Models\Mysql\{
    SystemUser,
    SystemUserLog as UserLog,
    UserLogView as UserV,
};


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
    protected $user;
    /**
     * @var UserLog
     */
    protected $user_log;
    /**
     * @var UserV
     */
    protected $user_v;

    public function __construct()
    {
        $this->user     = new SystemUser();
        $this->user_log = new UserLog();
        $this->user_v   = new UserV();
    }

    public function getInfoByName(string $name): ?array
    {
        return $this->user->getInfo($name);
    }

    public function setUser(array $data): int
    {
        return $this->user->setUser($data);
    }

    /**
     * 获取用户列表数据
     *
     * @param array $data
     *
     * @return array|null
     */
    public function getInfoList(array $data): ?array
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
                $count = $this->user->getListCount();
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = $this->user->getUserList($data);
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

    public function delUser(int $id): int
    {
        return $this->user->delUser($id);
    }

    public function getUserById(int $id): array
    {
        return $this->user->getUser($id);
    }

    public function editUser(array $data): int
    {
        return $this->user->editUser($data);
    }

    public function setLoginLog(array $data): void
    {
        go(function () use ($data) {
            $this->user_log->setLoginLog($data);
        });
    }

    public function setLogoutLog(array $data): void
    {
        go(function () use ($data) {
            $this->user_log->setLogoutLog($data);
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
            $arr_uid                  = $this->user->getInfo($data['uname']);
            $data['where']['user_id'] = $arr_uid[0]['id'] ?? 0;
        }

        $chan = new \Swoole\Coroutine\Channel(2);
        go(function () use ($chan, $data) { //获取总数
            try {
                $count = $this->user_log->getListCount($data['where']);
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list    = $this->user_log->getList($data);
                $arr_uid = array_column($list, 'user_id');

                $arr_name = $this->user->getNameById(array_unique($arr_uid));
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
                $count = $this->user_v->getListCount($data['where']);
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage() . __LINE__]);
            }
        });
        go(function () use ($chan, $data) { //获取列表数据
            try {
                $list = $this->user_v->getList($data);
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