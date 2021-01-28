<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use Envms\FluentPDO\Exception;

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
class UserLogViewMysql extends AbstractPdo
{
    protected string $table = 'user_log_view';

    /**
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function getList(array $data): array
    {
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return $this->getDb()->from($this->table)
            ->where($wheres)
            ->select('id,user_name,login_dt,logout_dt,login_ip')
            ->orderBy('id DESC')
            ->offset($data['curr_data'])
            ->limit($data['page_size'])
            ->fetchAll();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午9:12
     *
     * @param array $where
     *
     * @return int
     * @throws Exception
     */
    public function getListCount(array $where): int
    {
        return $this->getDb()->from($this->table)->where($where)->count();
    }

}
