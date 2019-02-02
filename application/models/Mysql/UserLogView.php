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


class UserLogView extends AbstractMysql
{
    private $table = 'user_log_view';

    /**
     *
     * @param array $data
     *
     * @return array
     */
    protected function getList(array $data): array
    {
        $datas = [];
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

        try {
            $datas = $this->db->select($this->table, [
                'id',
                'user_name',
                'login_dt',
                'logout_dt',
                'login_ip',
            ],
                $wheres);
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

    protected function getListCount(array $where): int
    {
        $datas = 1;
        try {
            $datas = $this->db->count($this->table, $where);
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        }
        return $datas;
    }

}