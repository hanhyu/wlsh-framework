<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use Exception;

class SystemRouter extends AbstractMysql
{
    protected $table = 'frame_system_router';

    /**
     * 获取列表信息
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
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
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:55
     * @return int
     * @throws Exception
     */
    protected function getListCount(): int
    {
        $datas = $this->db->count($this->table);
        if ($datas == false) throw new Exception($this->db->last());
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
     * @throws Exception
     */
    protected function setRouter(array $post): int
    {
        $datas = $this->db->insert($this->table, [
            'name'    => $post['name'],
            'url'     => $post['url'],
            'auth'    => $post['auth'],
            'method'  => $post['method'],
            'action'  => $post['action'],
            'type'    => (int)$post['type'],
            'menu_id' => (int)$post['menu_id'],
            'comment' => $post['comment'],
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return (int)$this->db->id();
    }

    /**
     * 修改路由
     *
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    protected function editRouter(array $post): int
    {
        $datas = $this->db->update($this->table, [
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
        if ($datas == false) throw new Exception($this->db->last());
        return $datas->rowCount();
    }

    /**
     * 删除路由
     *
     * @param int $id
     *
     * @return int
     * @throws Exception
     */
    protected function delRouter(int $id): int
    {
        $datas = $this->db->delete($this->table, [
            'id' => $id,
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas->rowCount();
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:58
     * @return array
     * @throws Exception
     */
    protected function getInfo(): array
    {
        $datas = $this->db->select($this->table, ["[>]frame_system_menu" => ["menu_id" => "id"]],
            [
                'frame_system_router.id(id)',
                'frame_system_router.name(name)',
                'frame_system_router.comment(comment)',
                'frame_system_menu.name(menu_name)',
            ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

}