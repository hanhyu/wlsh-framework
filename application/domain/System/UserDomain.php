<?php
declare(strict_types=1);

namespace App\Domain\System;

use App\Library\ProgramException;
use App\Models\Mysql\SystemUserLogMysql;
use App\Models\Mysql\SystemUserMysql;
use App\Models\Mysql\UserLogViewMysql;
use Swoole\Coroutine\Channel;

/**
 * User: hanhyu
 * Date: 19-1-3
 * Time: 下午11:36
 */
class UserDomain
{
    public function getInfoByName(string $name): array
    {
        return SystemUserMysql::getInstance()->getInfo($name);
    }

    public function setUser(array $data): int
    {
        return SystemUserMysql::getInstance()->setUser($data);
    }

    /**
     * 在开启Swoole\Runtime::enableCoroutine的情况下，压测结果，协程并行与不使用并行一样。
     *
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
        $chan          = new Channel(2);
        go(static function () use ($chan) { //获取总数
            try {
                $count = SystemUserMysql::getInstance()->getListCount();
                $chan->push(['count' => $count]);
            } catch (\Throwable $e) {
                $chan->push(['500' => $e->getMessage()]);
            }
        });
        go(static function () use ($chan, $data) { //获取列表数据
            try {
                $list = SystemUserMysql::getInstance()->getUserList($data);
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

        $res['count'] = SystemUserMysql::getInstance()->getListCount();
        $res['list']  = SystemUserMysql::getInstance()->getUserList($data);
        return $res;
    }

    public function delUser(int $id): int
    {
        return SystemUserMysql::getInstance()->delUser($id);
    }

    public function getUserById(int $id): array
    {
        return SystemUserMysql::getInstance()->getUser($id);
    }

    /**
     *
     * @param array $data
     *
     * @return int
     */
    public function editUser(array $data): int
    {
        return SystemUserMysql::getInstance()->editUser($data);
    }

    public function setLoginLog(array $data): void
    {
        SystemUserLogMysql::getInstance()->setLoginLog($data);
    }

    public function setLogoutLog(array $data): void
    {
        SystemUserLogMysql::getInstance()->setLogoutLog($data);
    }

    /**
     * 获取登录日志列表
     *
     * @param array $data
     *
     * @return array
     * @throws ProgramException
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
                date('Y-m-d', strtotime('+1 day', strtotime($data['login_time']))),
            ];
        }

        if (!empty($data['uname'])) {
            $arr_uid = SystemUserMysql::getInstance()->getInfo($data['uname']);
            if (empty($arr_uid)) {
                throw new ProgramException('用户名不存在', 400);
            }
            $data['where']['user_id'] = $arr_uid[0]['id'] ?? 0;
        }

        $res['count'] = SystemUserLogMysql::getInstance()->getListCount($data['where']);
        if (0 === $res['count']) {
            $res['list'] = [];
        } else {
            $list    = SystemUserLogMysql::getInstance()->getList($data);
            $arr_uid = array_column($list, 'user_id');

            $arr_name = SystemUserMysql::getInstance()->getNameById(array_unique($arr_uid));
            $arr_let  = array_column($arr_name, 'name', 'id');

            if (is_iterable($list)) {
                foreach ($list as $k => &$v) {
                    $list[$k]['user_name'] = $arr_let[$v['user_id']];
                    $v['login_ip']         = long2ip((int)$v['login_ip']);
                }
                unset($v);
            } else {
                $list = [];
            }

            $res['list'] = $list;
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
                date('Y-m-d', strtotime('+1 day', strtotime($data['login_time']))),
            ];
        }

        $data['where']['user_name'] = $data['uname'] ?? 0;


        $res['count'] = UserLogViewMysql::getInstance()->getListCount($data['where']);
        $res['list']  = UserLogViewMysql::getInstance()->getList($data);

        return $res;
    }

    /**
     * 压测协程数据结果是否错乱，连接池大小
     * UserDomain: hanhyu
     * Date: 2019/7/27
     * Time: 下午10:16
     */
    public function testName(): void
    {
        go(function () {
            $name = SystemUserMysql::getInstance()->testNameById(1);
            if ('ceshi001' != $name) {
                print_r('name:' . $name);
            }
        });

        go(function () {
            $name = SystemUserMysql::getInstance()->testNameById(7);
            if ('ceshi12' != $name) {
                print_r('name:' . $name);
            }
        });

        go(function () {
            $name2 = SystemUserMysql::getInstance()->getNameById([7]);
            if ('ceshi12' != $name2[0]['name']) {
                print_r('name2:' . $name2);
            }
        });
    }

    public function editPwd(array $data): int
    {
        $pwd = SystemUserMysql::getInstance()->getPwdByUid((int)$data['uid']);

        if (!password_verify($data['old_pwd'], $pwd)) {
            $res = -1;
        } else {
            $res = SystemUserMysql::getInstance()->editPwd($data);
        }
        return $res;
    }

    /**
     * 判断用户名是否已注册
     * UserDomain: hanhyu
     * Date: 2019/8/18
     * Time: 下午7:58
     *
     * @param string $name
     *
     * @return bool
     */
    public function existName(string $name): bool
    {
        return SystemUserMysql::getInstance()->existName($name);
    }
}
