<?php
declare(strict_types=1);

namespace App\Models\Mysql;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemUser extends AbstractMysql
{
    private $table = 'frame_system_user';

    /**
     * @param array $post
     *
     * @return int
     */
    protected function setUser(array $post): int
    {
        $last_id = 0;
        try {
            $this->db->insert($this->table, [
                'name'   => $post['name'],
                'pwd'    => password_hash($post['pwd'], PASSWORD_DEFAULT),
                'status' => 10,
                'crt_dt' => date('y-m-d H:i:s'),
                'remark' => $post['remark'],
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
     * @param array $data
     *
     * @return array
     */
    protected function getUserList(array $data): array
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
            $datas = $this->db->select($this->table, '*', $wheres);
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

    protected function getListCount(): int
    {
        $datas = 1;
        try {
            $datas = $this->db->count($this->table);
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        }
        return $datas;
    }

    /**
     * @param int $id
     *
     * @return int
     */
    protected function delUser(int $id): int
    {
        $last_id = 0;
        try {
            $data    = $this->db->delete($this->table, [
                'id' => $id,
            ]);
            $last_id = $data->rowCount();
        } catch (\Exception $e) {
            co_log($this->db->last(), "删除{$id}信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($last_id == 0) {
                co_log($this->db->last(), "删除{$id}信息失败的sql：");
            }
        }
        return $last_id;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    protected function getUser(int $id): array
    {
        $datas = [];
        try {
            $datas = $this->db->select($this->table, [
                'id',
                'status',
                'remark',
            ], ['id' => $id]);
        } catch (\Exception $e) {
            co_log($this->db->last(), "查询信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败,失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

    /**
     * @param array $post
     *
     * @return int
     */
    protected function editUser(array $post): int
    {
        $last_id = 0;
        try {
            $data    = $this->db->update($this->table, [
                'status' => $post['status'],
                'remark' => $post['remark'],
            ], [
                'id' => (int)$post['id'],
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
     * @param string $name
     *
     * @return array
     */
    protected function getInfo(string $name): array
    {
        $datas = [];
        try {
            $datas = $this->db->select($this->table, [
                'id',
                'name',
                'status',
                'pwd',
            ], ['name' => $name]);
        } catch (\Exception $e) {
            co_log($this->db->last(), "查询信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败,失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

    protected function getNameById(array $uid): array
    {
        $datas = [];
        try {
            $datas = $this->db->select($this->table, [
                'id',
                'name',
            ], ['id' => $uid]);
        } catch (\Exception $e) {
            co_log($this->db->last(), "查询信息出错：{$e->getMessage()},出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败,失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

}