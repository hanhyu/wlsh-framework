<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use App\Library\ProgramException;
use Envms\FluentPDO\Exception;

/**
 * @property  array  $getMenuList
 * @property  int    $getListCount
 * @property  array  $getMenuInfo
 * @property  int    $setMenu
 * @property  array  $getMenu
 * @property  int    $editMenu
 * @property  int    $delMenu
 * @property  string $getVersion
 *
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemMenuMysql extends AbstractPdo
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
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return $this->db->from($this->table)
            ->where($wheres)
            ->orderBy('id DESC')
            ->offset($data['curr_data'])
            ->limit($data['page_size'])
            ->fetchAll();
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:10
     * @return int
     * @throws Exception
     */
    protected function getListCount(): int
    {
        return $this->db->from($this->table)->count();
    }

    /**
     * 获取所有菜单指定信息
     *
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:11
     * @return array
     * @throws Exception
     */
    protected function getMenuInfo(): array
    {
        return $this->db->from($this->table)
            ->select('id,name,icon,url,up_id,level', true)
            ->fetchAll();
    }

    /**
     * 添加菜单
     *
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    protected function setMenu(array $post): int
    {
        return (int)$this->db->insertInto($this->table)
            ->values([
                'name'  => $post['name'],
                'icon'  => $post['icon'],
                'url'   => $post['url'],
                'up_id' => $post['up_id'],
                'level' => $post['level'],
            ])
            ->execute();
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:19
     *
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    protected function getMenu(int $id): array
    {
        return $this->db->from($this->table)
            ->where('id', $id)
            ->select('id,name,icon,url,up_id', true)
            ->fetchAll();
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:23
     *
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    protected function editMenu(array $post): int
    {
        return $this->db->update($this->table)
            ->set([
                'name'  => $post['name'],
                'icon'  => $post['icon'],
                'url'   => $post['url'],
                'up_id' => $post['up_id'],
                'level' => $post['level'],
            ])
            ->where('id', $post['id'])
            ->execute();
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:25
     *
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    protected function delMenu(int $id): bool
    {
        return $this->db->deleteFrom($this->table)
            ->where('id', $id)
            ->execute();
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
        return $this->db->getPdo()->query('SELECT version()')->fetchColumn();
    }

}
