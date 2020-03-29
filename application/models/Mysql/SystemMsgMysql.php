<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractMysql;
use RuntimeException;

/**
 * @property int   setMsg
 * @property int   setLogoutLog
 * @property array getList
 * @property int   getListCount
 *
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-2-1
 * Time: 下午5:58
 */
class SystemMsgMysql extends AbstractMysql
{
    protected string $table = 'frame_system_msg';

    /**
     * 添加内容
     *
     * @param array $data
     *
     * @return int
     */
    protected function setMsg(array $data): int
    {
        $datas = $this->db->insert($this->table, [
            'content' => $data['content'],
            'crt_dt'  => $data['crt_dt'],
            'upt_id'  => $data['id'],
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
            'content',
            'crt_dt',
            'upt_dt',
            'upt_id',
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
     * Time: 下午8:52
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
