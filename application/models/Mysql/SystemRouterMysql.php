<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use Envms\FluentPDO\Exception;

/**
 *
 * Class SystemRouterModel
 * @package App\Models\Mysql
 */
class SystemRouterMysql extends AbstractPdo
{
    protected string $table = 'frame_system_router';

    /**
     * 获取列表信息
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function getList(array $data): array
    {
        return self::getDb()->from("{$this->table} r")
            ->leftJoin('frame_system_menu m ON r.menu_id=m.id')
            ->select([
                'r.id AS id',
                'r.name AS name',
                'r.url AS url',
                'r.auth AS auth',
                'r.method AS method',
                'r.action AS action',
                'r.type AS type',
                'r.menu_id AS menu_id',
                'r.comment AS comment',
                'm.name AS menu_name',
            ], true)
            ->orderBy('id DESC')
            ->offset($data['curr_data'])
            ->limit($data['page_size'])
            ->fetchAll();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:55
     * @return int
     * @throws Exception
     */
    public function getListCount(): int
    {
        return self::getDb()->from($this->table)->count();
    }


    /**
     * 添加路由信息
     * UserDomain: hanhyu
     * Date: 19-4-28
     * Time: 上午11:05
     *
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    public function setRouter(array $post): int
    {
        return (int)self::getDb()->insertInto($this->table)
            ->values([
                'name'    => $post['name'],
                'url'     => $post['url'],
                'auth'    => $post['auth'],
                'method'  => $post['method'],
                'action'  => $post['action'],
                'type'    => (int)$post['type'],
                'menu_id' => (int)$post['menu_id'],
                'comment' => $post['comment'],
            ])
            ->execute();
    }

    /**
     * 修改路由
     *
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    public function editRouter(array $post): int
    {
        return self::getDb()->update($this->table)
            ->set([
                'name'    => $post['name'],
                'url'     => $post['url'],
                'auth'    => (int)$post['auth'],
                'method'  => $post['method'],
                'action'  => $post['action'],
                'type'    => (int)$post['type'],
                'menu_id' => (int)$post['menu_id'],
                'comment' => $post['comment'],
            ])
            ->where('id', $post['id'])
            ->execute();
    }

    /**
     * 删除路由
     *
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    public function delRouter(int $id): bool
    {
        return self::getDb()->deleteFrom($this->table, $id)->execute();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:58
     * @return array
     * @throws Exception
     */
    public function getInfo(): array
    {
        return self::getDb()->from("{$this->table} r")
            ->leftJoin('frame_system_menu m ON r.menu_id=m.id')
            ->select([
                'r.id AS id',
                'r.name AS name',
                'r.comment AS comment',
                'm.name AS menu_name',
            ])
            ->fetchAll();
    }

}
