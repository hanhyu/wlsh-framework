<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use App\Library\ProgramException;
use Envms\FluentPDO\Exception;
use phpDocumentor\Reflection\Types\Self_;

/**
 *
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemUserMysql extends AbstractPdo
{
    protected string $table = 'frame_system_user';

    /**
     * @param array $post
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function setUser(array $post): int
    {
        return (int)self::getDb()->insertInto($this->table)
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
     * @throws Exception|ProgramException
     */
    public function getUserList(array $data): array
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
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:00
     * @return int
     * @throws Exception|ProgramException
     * @todo mysql count 性能下降100倍
     */
    public function getListCount(): int
    {
        //return self::getDb()->from($this->table)->count();
        return (int)self::getDb()
            ->from('information_schema.`TABLES`')
            ->where('TABLE_NAME', $this->table)
            ->select('TABLE_ROWS', true)
            ->fetchColumn();
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws Exception|ProgramException
     */
    public function delUser(int $id): bool
    {
        return self::getDb()->deleteFrom($this->table)->where('id', $id)->execute();
    }

    /**
     * @param int $id
     *
     * @return array|bool
     * @throws Exception|ProgramException
     */
    public function getUser(int $id): array|bool
    {
        return self::getDb()->from($this->table)
            ->where('id', $id)
            ->select('id,status,remark', true)
            ->fetch();
    }

    /**
     * @param array $post
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function editUser(array $post): int
    {
        return self::getDb()->update($this->table)
            ->set(['status' => $post['status'], 'remark' => $post['remark']])
            ->where('id', $post['id'])
            ->execute();
    }

    /**
     * 获取用户基本信息
     *
     * @param string $name 用户名
     *
     * @return array|bool ['id','name','status','pwd']
     * @throws Exception|ProgramException
     */
    public function getInfo(string $name): array|bool
    {
        return self::getDb()->from($this->table)
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
     * @throws Exception|ProgramException
     */
    public function getNameById(array $uid): array
    {
        return self::getDb()->from($this->table)
            ->where('id', $uid)
            ->select('id,name', true)
            ->fetchAll();
    }

    public function testNameById(int $id): array|bool
    {
        return self::getDb()->from($this->table)
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
     * @return string
     * @throws Exception|ProgramException
     */
    public function getPwdByUid(int $uid): string
    {
        return self::getDb()->from($this->table)
            ->where('id', $uid)
            ->select('pwd', true)
            ->fetchColumn();
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
     * @throws Exception|ProgramException
     */
    public function editPwd(array $data): int
    {
        return self::getDb()->update($this->table)
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
     * @throws Exception|ProgramException
     */
    public function existName(string $name): bool
    {
        $id = self::getDb()->from($this->table)
            ->where(['name' => $name])
            ->select('id', true)
            ->fetch();

        return $id !== false;
    }

}
