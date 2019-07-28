<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use Exception;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemUser extends AbstractMysql
{
    protected $table = 'frame_system_user';

    /**
     * @param array $post
     *
     * @return int
     * @throws Exception
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
        if ($datas == false) throw new Exception($this->db->last());
        return (int)$this->db->id();
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
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
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午9:00
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
     * @param int $id
     *
     * @return int
     * @throws Exception
     */
    protected function delUser(int $id): int
    {
        $datas = $data = $this->db->delete($this->table, [
            'id' => $id,
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $data->rowCount();;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    protected function getUser(int $id): array
    {
        $datas = $this->db->select($this->table, [
            'id',
            'status',
            'remark',
        ], ['id' => $id]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * @param array $post
     *
     * @return int
     * @throws Exception
     */
    protected function editUser(array $post): int
    {
        $datas = $this->db->update($this->table, [
            'status' => $post['status'],
            'remark' => $post['remark'],
        ], [
            'id' => (int)$post['id'],
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas->rowCount();
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
        $datas = $this->db->select($this->table, [
            'id',
            'name',
            'status',
            'pwd',
        ], [
            'name' => $name,
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * User: hanhyu
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
        $datas = $this->db->select($this->table, [
            'id',
            'name',
        ], [
            'id' => $uid,
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    protected function testNameById(int $id): ?string
    {
        return $this->db->get($this->table, 'name', ['id' => $id]);
    }


}