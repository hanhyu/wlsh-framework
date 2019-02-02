<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-2-1
 * Time: 下午5:58
 */

namespace App\Models\Mysql;

class SystemMsg extends AbstractMysql
{
    private $table = 'frame_system_msg';

    /**
     * 添加内容
     *
     * @param array $data
     *
     * @return int
     */
    protected function setMsg(array $data): int
    {
        $last_id = 0;
        try {
            $this->db->insert($this->table, [
                'content' => $data['content'],
                'crt_dt'  => $data['crt_dt'],
                'upt_id'  => $data['id'],
            ]);
            $last_id = $this->db->id();
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($last_id === false) {
                co_log($this->db->last(), '信息失败的sql：');
            }
        }
        return (int)$last_id;
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
        $last_id = 0;
        try {
            $data    = $this->db->update($this->table, [
                'logout_dt' => date('Y-m-d H:i:s'),
            ], [
                'user_id'  => (int)$data['id'],
                'login_dt' => date('Y-m-d H:i:s', $data['time']),
            ]);
            $last_id = $data->rowCount();
        } catch (\Exception $e) {
            co_log($this->db->last(), "修改信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($last_id == 0) {
                co_log($this->db->last(), '修改信息失败,失败的sql：');
            }
        }
        return $last_id;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getList(array $data): array
    {
        $datas = [];
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

        try {
            $datas = $this->db->select($this->table, [
                'id',
                'content',
                'crt_dt',
                'upt_dt',
                'upt_id',
            ],
                $wheres);
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

    protected function getListCount(array $where): int
    {
        $datas = 1;
        try {
            $datas = $this->db->count($this->table, $where);
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        }
        return $datas;
    }

}