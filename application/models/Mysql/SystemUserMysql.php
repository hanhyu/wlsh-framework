<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractMysql;
use App\Library\DI;
use App\Library\ProgramException;
use RuntimeException;

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
class SystemUserMysql extends AbstractMysql
{
    //protected static string $db_schema = 'mysql_user_obj';
    protected string $table = 'frame_system_user';

    /**
     * @param array $post
     *
     * @return int
     */
    protected function setUser(array $post): int
    {
        $datas = $this->db->insert($this->table, [
            'name'   => $post['name'],
            'pwd'    => password_hash($post['pwd'], PASSWORD_DEFAULT),
            'status' => 10,
            'crt_dt' => date('y-m-d H:i:s'),
            'remark' => $post['remark'],
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return (int)$this->db->id();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getUserList(array $data): array
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
     * Time: 下午9:00
     * @return int
     */
    protected function getListCount(): int
    {
        //todo mysql count 性能下降100倍
        $datas = $this->db->count($this->table);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
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
        $datas = $data = $this->db->delete($this->table, [
            'id' => $id,
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $data->rowCount();
    }

    /**
     * @param int $id
     *
     * @return array
     */
    protected function getUser(int $id): array
    {
        $datas = $this->db->select($this->table, [
            'id',
            'status',
            'remark',
        ], ['id' => $id]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
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
        $datas = $this->db->update($this->table, [
            'status' => $post['status'],
            'remark' => $post['remark'],
        ], [
            'id' => (int)$post['id'],
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas->rowCount();
    }

    /**
     * 获取用户基本信息
     *
     * @param string $name 用户名
     *
     * @return array ['id','name','status','pwd']
     */
    protected function getInfo(string $name): array
    {
        $datas = $this->db->select($this->table, [
            'id',
            'name',
            'status',
            'pwd',
        ], [
            'name' => $name,
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:04
     *
     * @param array $uid
     *
     * @return array
     */
    protected function getNameById(array $uid): array
    {
        $datas = $this->db->select($this->table, [
            'id',
            'name',
        ], [
            'id' => $uid,
        ]);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

    protected function testNameById(int $id): ?string
    {
        return $this->db->get($this->table, 'name', ['id' => $id]);
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
     */
    protected function getPwdByUid(int $uid): ?string
    {
        return $this->db->get($this->table, 'pwd', ['id' => $uid]);
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
     */
    protected function editPwd(array $data): int
    {
        $datas = $this->db->update($this->table, [
            'pwd' => password_hash($data['new_pwd'], PASSWORD_DEFAULT),
        ], [
            'id' => $data['uid'],
        ]);
        if ($datas === false) {
            throw new RuntimeException($this->db->last());
        }
        return $datas->rowCount();
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
     */
    protected function existName(string $name): bool
    {
        return $this->db->has($this->table, ['name' => $name]);
    }


}
