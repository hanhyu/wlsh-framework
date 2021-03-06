<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use App\Library\ProgramException;
use Envms\FluentPDO\Exception;

/**
 *
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-2-1
 * Time: 下午5:58
 */
class SystemMsgMysql extends AbstractPdo
{
    protected string $table = 'frame_system_msg';

    public static function getPool(): string
    {
        return 'mysql_pool_obj';
    }

    /**
     * 添加内容
     *
     * @param array $data
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function setMsg(array $data): int
    {
        return (int)self::getDb()->insertInto($this->table)
            ->values([
                'content' => $data['content'],
                'crt_dt'  => $data['crt_dt'],
                'upt_id'  => $data['id'],
            ])
            ->execute();
    }

    /**
     * 添加用户退出记录
     *
     * @param array $data
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function setLogoutLog(array $data): int
    {
        return self::getDb()->update($this->table)
            ->set('logout_dt', date('Y-m-d H:i:s'))
            ->where([
                'user_id'  => (int)$data['id'],
                'login_dt' => date('Y-m-d H:i:s', $data['time']),
            ])
            ->execute();
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws Exception|ProgramException
     */
    public function getList(array $data): array
    {
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return self::getDb()->from($this->table)
            ->where($wheres)
            ->orderBy('id DESC')
            ->offset($data['curr_data'])
            ->limit($data['page_size'])
            ->select('id,content,crt_dt,upt_dt,upt_id', true)
            ->fetchAll();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:52
     *
     * @param array $where
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function getListCount(array $where): int
    {
        return self::getDb()->from($this->table)->where($where)->count();
    }

}
