<?php
declare(strict_types=1);

namespace App\Models\Mysql;

class SystemRouter extends AbstractMysql
{
    private $table = 'frame_system_router';

    /**
     * 获取列表信息
     *
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
            $datas = $this->db->select($this->table, ["[>]frame_system_menu" => ["menu_id" => "id"]],
                [
                    'frame_system_router.id(id)',
                    'frame_system_router.name(name)',
                    'frame_system_router.url(url)',
                    'frame_system_router.auth(auth)',
                    'frame_system_router.method(method)',
                    'frame_system_router.action(action)',
                    'frame_system_router.type(type)',
                    'frame_system_router.menu_id(menu_id)',
                    'frame_system_router.comment(comment)',
                    'frame_system_menu.name(menu_name)',
                ], $wheres);
        } catch (\Throwable $e) {
            co_log($this->db->last(), "查询信息出错：{$e->getMessage()},,出错的sql：");
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
        } catch (\Throwable $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        }
        return $datas;
    }


    /**
     * 添加路由信息
     * User: hanhyu
     * Date: 19-4-28
     * Time: 上午11:05
     *
     * @param array $post
     *
     * @return int
     */
    protected function setRouter(array $post): int
    {
        $last_id = 0;
        try {
            $this->db->insert($this->table, [
                'name'    => $post['name'],
                'url'     => $post['url'],
                'auth'    => $post['auth'],
                'method'  => $post['method'],
                'action'  => $post['action'],
                'type'    => (int)$post['type'],
                'menu_id' => (int)$post['menu_id'],
                'comment' => $post['comment'],
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
     * 修改路由
     *
     * @param array $post
     *
     * @return int
     */
    protected function editRouter(array $post): int
    {
        $last_id = 0;
        try {
            $data    = $this->db->update($this->table, [
                'name'    => $post['name'],
                'url'     => $post['url'],
                'auth'    => (int)$post['auth'],
                'method'  => $post['method'],
                'action'  => $post['action'],
                'type'    => (int)$post['type'],
                'menu_id' => (int)$post['menu_id'],
                'comment' => $post['comment'],
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
     * 删除路由
     *
     * @param int $id
     *
     * @return int
     */
    protected function delRouter(int $id): int
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

    protected function getInfo(): array
    {
        $datas = [];
        try {
            $datas = $this->db->select($this->table, ["[>]frame_system_menu" => ["menu_id" => "id"]],
                [
                    'frame_system_router.id(id)',
                    'frame_system_router.name(name)',
                    'frame_system_router.comment(comment)',
                    'frame_system_menu.name(menu_name)',
                ]);
        } catch (\Throwable $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

}