<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractMysql;
use RuntimeException;

/**
 * @property array getList
 * @property int   getListCount
 *
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 19-1-16
 * Time: 上午10:09
 *
 */
class UserLogViewMysql extends AbstractMysql
{
    protected string $table = 'user_log_view';

    /**
     *
     * @param array $data
     *
     * @return array
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
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:12
     *
     * @param array $where
     *
     * @return int
     */
    protected function getListCount(array $where): int
    {
        $datas = $this->db->count($this->table, $where);
        if (false === $datas) {
            throw new RuntimeException($this->db->last());
        }
        return $datas;
    }

}
