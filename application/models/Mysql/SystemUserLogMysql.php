<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use Envms\FluentPDO\Exception;

/**
 * @property int   setLogingLog
 * @property int   setLogoutLog
 * @property array getList
 * @property int   getListCount
 *
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class SystemUserLogMysql extends AbstractPdo
{
    protected string $table = 'frame_system_user_log';

    /**
     * 添加用户登录记录
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    protected function setLoginLog(array $data): int
    {
        return (int)$this->db->insertInto($this->table)
            ->values([
                'user_id'  => $data['id'],
                'login_dt' => date('Y-m-d H:i:s', $data['time']),
                'login_ip' => $data['ip'],
            ])
            ->execute();
    }

    /**
     * 添加用户退出记录
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    protected function setLogoutLog(array $data): int
    {
        return $this->db->update($this->table)
            ->set(['logout_dt' => date('Y-m-d H:i:s')])
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
     * @throws Exception
     */
    protected function getList(array $data): array
    {
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return $this->db->from($this->table)
            ->where($wheres)
            ->select('id,user_id,login_dt,logout_dt,login_ip', true)
            ->orderBy('id DESC')
            ->offset($data['curr_data'])
            ->limit($data['page_size'])
            ->fetchAll();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:10
     *
     * @param array $where
     *
     * @return int
     * @throws Exception
     */
    protected function getListCount(array $where): int
    {
        return $this->db->from($this->table)->where($where)->count();
    }

}
