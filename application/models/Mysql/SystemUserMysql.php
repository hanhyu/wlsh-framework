<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use Envms\FluentPDO\Exception;

/**
 * @property int         setUser
 * @property array       getUserList
 * @property int         getListCount
 * @property int         delUser
 * @property array       getUser
 * @property int         editUser
 * @property array       getInfo
 * @property array       getNameById
 * @property null|string testNameById
 * @property null|string getPwdByUid
 * @property int         editPwd
 * @property bool        existName
 *
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemUserMysql extends AbstractPdo
{
    //protected static string $db_schema = 'mysql_user_obj';
    protected string $table = 'frame_system_user';

    /**
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    protected function setUser(array $post): int
    {
        return (int)$this->db->insertInto($this->table)
            ->values([
                'name'   => $post['name'],
                'pwd'    => password_hash($post['pwd'], PASSWORD_DEFAULT),
                'status' => 10,
                'crt_dt' => date('y-m-d H:i:s'),
                'remark' => $post['remark'],
            ])
            ->execute();
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    protected function getUserList(array $data): array
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
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:00
     * @return int
     * @throws Exception
     * @todo mysql count 性能下降100倍
     */
    protected function getListCount(): int
    {
        return $this->db->from($this->table)->count();
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    protected function delUser(int $id): bool
    {
        return $this->db->deleteFrom($this->table)->where('id', $id)->execute();
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    protected function getUser(int $id): array
    {
        return $this->db->from($this->table)
            ->where('id', $id)
            ->select('id,status,remark', true)
            ->fetch();
    }

    /**
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    protected function editUser(array $post): int
    {
        return $this->db->update($this->table)
            ->set(['status' => $post['status'], 'remark' => $post['remark']])
            ->where('id', $post['id'])
            ->execute();
    }

    /**
     * 获取用户基本信息
     *
     * @param string $name 用户名
     *
     * @return array ['id','name','status','pwd']
     * @throws Exception
     */
    protected function getInfo(string $name): array
    {
        return $this->db->from($this->table)
            ->where(['name' => $name])
            ->select('id,name,status,pwd', true)
            ->fetch();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:04
     *
     * @param array $uid
     *
     * @return array
     * @throws Exception
     */
    protected function getNameById(array $uid): array
    {
        return $this->db->from($this->table)
            ->where('id', $uid)
            ->select('id,name', true)
            ->fetchAll();
    }

    protected function testNameById(int $id): ?string
    {
        return $this->db->from($this->table)
            ->where('id', $id)
            ->select('name', true)
            ->fetch();
    }

    /**
     * 根据用户uid获取密码
     * UserDomain: hanhyu
     * Date: 2019/8/14
     * Time: 下午4:10
     *
     * @param int $uid
     *
     * @return string|null
     * @throws Exception
     */
    protected function getPwdByUid(int $uid): ?string
    {
        return $this->db->from($this->table)
            ->where('id', $uid)
            ->select('pwd', true)
            ->fetch();
    }

    /**
     * 用户修改密码
     * UserDomain: hanhyu
     * Date: 2019/8/14
     * Time: 下午4:11
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    protected function editPwd(array $data): int
    {
        return $this->db->update($this->table)
            ->set(['pwd' => password_hash($data['new_pwd'], PASSWORD_DEFAULT)])
            ->where('id', $data['uid'])
            ->execute();
    }

    /**
     * 判断用户名是否存在
     * UserDomain: hanhyu
     * Date: 2019/8/18
     * Time: 下午7:58
     *
     * @param string $name
     *
     * @return bool
     * @throws Exception
     */
    protected function existName(string $name): bool
    {
        $id = $this->db->from($this->table)
            ->where(['name' => $name])
            ->select('id', true)
            ->fetch();

        return $id !== false;
    }


}
