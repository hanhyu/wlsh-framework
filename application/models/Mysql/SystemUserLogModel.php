<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use RuntimeException;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemUserLogModel extends AbstractMysql
{
    protected string $table = 'frame_system_user_log';

    /**
     * 添加用户登录记录
     *
     * @param array $data
     *
     * @return int
     */
    protected function setLoginLog(array $data): int
    {
        $datas = $this->db->insert($this->table, [
            'user_id'  => $data['id'],
            'login_dt' => date('Y-m-d H:i:s', $data['time']),
            'login_ip' => $data['ip'],
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return (int)$this->db->id();
    }

    /**
     * 添加用户退出记录
     *
     * @param array $data
     *
     * @return int
     */
    protected function setLogoutLog(array $data): int
    {
        $datas = $this->db->update($this->table, [
            'logout_dt' => date('Y-m-d H:i:s'),
        ], [
            'user_id'  => (int)$data['id'],
            'login_dt' => date('Y-m-d H:i:s', $data['time']),
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas->rowCount();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getList(array $data): array
    {
        if (!empty($data['where'])) {
            $wheres = [
                'AND'   => $data['where'],
                'ORDER' => ['id' => 'DESC'],
                'LIMIT' => [$data['curr_data'], $data['page_size']],
            ];
        } else {
            $wheres = [
                'ORDER' => ['id' => 'DESC'],
                'LIMIT' => [$data['curr_data'], $data['page_size']],
            ];
        }

        $datas = $this->db->select($this->table, [
            'id',
            'user_id',
            'login_dt',
            'logout_dt',
            'login_ip',
            //'login_ip'=>\Medoo\Medoo::raw('INET_NTOA(<login_ip>)'),
        ],
            $wheres);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:10
     *
     * @param array $where
     *
     * @return int
     */
    protected function getListCount(array $where): int
    {
        $datas = $this->db->count($this->table, $where);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

}
