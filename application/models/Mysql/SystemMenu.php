<?php
declare(strict_types=1);

namespace App\Models\Mysql;
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemMenu extends AbstractMysql
{
    private $table = 'frame_system_menu';

    /**
     * 获取菜单列表信息
     *
     * @param array $data
     *
     * @return array
     */
    protected function getMenuList(array $data): array
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

        /* try {
             $datas = $this->db->select($this->table, '*', $wheres);
         } catch (\Throwable $e) {
             co_log($this->db->last(), "查询信息出错：{$e->getMessage()},,出错的sql：");
         } finally {
             if ($datas === false) {
                 co_log($this->db->last(), '查询信息失败的sql：');
                 $datas = [];
             }
         }*/

        $datas = $this->db->select($this->table, '*', $wheres);

        if ($datas == false) throw new \Exception($this->db->last());

        return $datas;
    }

    protected function getListCount(): int
    {
        $datas = 1;
        try {
            $datas = $this->db->count($this->table);
        } catch (\Throwable $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        }
        return $datas;
    }

    /**
     * 获取所有菜单指定信息
     * @return array
     */
    protected function getMenuInfo(): array
    {
        $datas = $this->db->select($this->table, [
            'id',
            'name',
            'icon',
            'url',
            'up_id',
            'level',
        ]);
        if ($datas === false) {
            co_log($this->db->last(), '查询信息失败的sql：', 'mysql');
            $datas = [];
        }
        return $datas;
    }

    /**
     * 添加菜单
     *
     * @param array $post
     *
     * @return int
     */
    protected function setMenu(array $post): int
    {
        $last_id = 0;
        try {
            $this->db->insert($this->table, [
                'name'  => $post['name'],
                'icon'  => $post['icon'],
                'url'   => $post['url'],
                'up_id' => $post['up_id'],
                'level' => $post['level'],
            ]);
            $last_id = $this->db->id();
        } catch (\Exception $e) {
            co_log($this->db->last(), "添加信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($last_id === false) {
                co_log($this->db->last(), '添加信息失败的sql：');
            }
        }
        return (int)$last_id;
    }

    /**
     * @param $id
     *
     * @return array|bool
     */
    protected function getMenu(int $id): array
    {
        $datas = [];
        try {
            $datas = $this->db->select($this->table, [
                'id',
                'name',
                'icon',
                'url',
                'up_id',
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
    protected function editMenu(array $post): int
    {
        $last_id = 0;
        try {
            $data    = $this->db->update($this->table, [
                'name'  => $post['name'],
                'icon'  => $post['icon'],
                'url'   => $post['url'],
                'up_id' => $post['up_id'],
                'level' => $post['level'],
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
     * @param int $id
     *
     * @return int
     */
    protected function delMenu(int $id): int
    {
        $last_id = 0;
        try {
            $data    = $this->db->delete($this->table, [
                'id' => $id,
            ]);
            $last_id = $data->rowCount();
        } catch (\Exception $e) {
            co_log($this->db->last(), "删除信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($last_id == 0) {
                co_log($this->db->last(), "删除{$id}信息失败的sql：");
            }
        }
        return $last_id;
    }

}