<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use App\Library\ProgramException;
use Envms\FluentPDO\Exception;

/**
 *
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemMenuMysql extends AbstractPdo
{
    protected string $table = 'frame_system_menu';

    public static function getPool(): string
    {
        return 'mysql_pool_obj';
    }

    /**
     * 获取菜单列表信息
     *
     * @param array $data
     *
     * @return array
     * @throws Exception|ProgramException
     */
    public function getMenuList(array $data): array
    {
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return self::getDb()->from($this->table)
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
     * @throws Exception|ProgramException
     */
    public function getListCount(): int
    {
        return self::getDb()->from($this->table)->count();
    }

    /**
     * 获取所有菜单指定信息
     *
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:11
     * @return array
     * @throws Exception|ProgramException
     */
    public function getMenuInfo(): array
    {
        return self::getDb()->from($this->table)
            ->select('id,name,icon,url,up_id,level', true)
            ->fetchAll();
    }

    /**
     * 添加菜单
     *
     * @param array $post
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function setMenu(array $post): int
    {
        return (int)self::getDb()->insertInto($this->table)
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
     * @return array|bool
     * @throws Exception|ProgramException
     */
    public function getMenu(int $id): array|bool
    {
        return self::getDb()->from($this->table)
            ->where('id', $id)
            ->select('id,name,icon,url,up_id', true)
            ->fetch();
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:23
     *
     * @param array $post
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function editMenu(array $post): int
    {
        return self::getDb()->update($this->table)
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
     * @return int|bool
     * @throws Exception
     * @throws ProgramException
     */
    public function delMenu(int $id): int|bool
    {
        return self::getDb()->deleteFrom($this->table)
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
     * @throws ProgramException
     */
    public function getVersion(): string
    {
        return self::getDb()->getPdo()->query('SELECT version()')->fetchColumn();
    }

}
