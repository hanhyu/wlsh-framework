<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-16
 * Time: 上午10:09
 *
 */

namespace App\Models\Mysql;


use Exception;

class UserLogView extends AbstractMysql
{
    protected $table = 'user_log_view';

    /**
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    protected function getList(array $data): array
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

        $datas = $this->db->select($this->table, [
            'id',
            'user_name',
            'login_dt',
            'logout_dt',
            'login_ip',
        ],
            $wheres);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午9:12
     *
     * @param array $where
     *
     * @return int
     * @throws Exception
     */
    protected function getListCount(array $where): int
    {
        $datas = $this->db->count($this->table, $where);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

}