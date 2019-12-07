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
class SystemMenuModel extends AbstractMysql
{
    protected string $table = 'frame_system_menu';

    /**
     * 获取菜单列表信息
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    protected function getMenuList(array $data): array
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

        $datas = $this->db->select($this->table, '*', $wheres);

        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }

        return $datas;
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:10
     * @return int
     */
    protected function getListCount(): int
    {
        $datas = $this->db->count($this->table);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

    /**
     * 获取所有菜单指定信息
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:11
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
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
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
        $datas = $this->db->insert($this->table, [
            'name'  => $post['name'],
            'icon'  => $post['icon'],
            'url'   => $post['url'],
            'up_id' => $post['up_id'],
            'level' => $post['level'],
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return (int)$this->db->id();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:19
     *
     * @param int $id
     *
     * @return array
     */
    protected function getMenu(int $id): array
    {
        $datas = $this->db->select($this->table, [
            'id',
            'name',
            'icon',
            'url',
            'up_id',
        ], ['id' => $id]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:23
     *
     * @param array $post
     *
     * @return int
     */
    protected function editMenu(array $post): int
    {
        $datas = $this->db->update($this->table, [
            'name'  => $post['name'],
            'icon'  => $post['icon'],
            'url'   => $post['url'],
            'up_id' => $post['up_id'],
            'level' => $post['level'],
        ], [
            'id' => (int)$post['id'],
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas->rowCount();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:25
     *
     * @param int $id
     *
     * @return int
     */
    protected function delMenu(int $id): int
    {
        $datas = $this->db->delete($this->table, [
            'id' => $id,
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas->rowCount();
    }

    /**
     * 获取mysql版本信息
     *
     * User: hanhyu
     * Date: 2019/12/7
     * Time: 下午10:50
     * @return string
     */
    protected function getVersion(): string
    {
        return $this->db->info()['version'];
    }

}
