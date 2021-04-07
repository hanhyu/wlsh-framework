<?php declare(strict_types=1);

namespace App\Models\Clickhouse;

use App\Library\AbstractClickhouse;
use ClickHouseDB\Statement;

class RouterLogClickhouse extends AbstractClickhouse
{
    protected string $table = 'router_log';

    public function getInfoById(string $trace_id): ?array
    {
        return self::getDb()->select("SELECT * FROM {$this->table} WHERE trace_id = '{$trace_id}'")->fetchOne();
    }

    public function setLog(array $data): Statement
    {
        return self::getDb()->insert($this->table,
            [
                [
                    $data['trace_id'], $data['level'], $data['req_method'], $data['req_uri'],
                    json_encode($data['req_data'], JSON_THROW_ON_ERROR | 320),
                    $data['req_ip'], $data['fd_time'], $data['req_time'], $data['resp_time'], $data['resp_data'],
                ],
            ],
            [
                'trace_id', 'level', 'req_method', 'req_uri', 'req_data', 'req_ip', 'fd_time', 'req_time', 'resp_time', 'resp_data',
            ],
        );
    }

    public function getList(array $data): ?array
    {
        $wheres = !empty($data['where']) ? $this->getWhereInfo($data['where']) : '';
        $sql    = "
                select trace_id,level,req_method,req_uri,req_ip,fd_time,req_time,resp_time,create_time 
                from {$this->table} 
                {$wheres}
                limit {$data['curr_data']}, {$data['page_size']}
                ";
        return self::getDb()->select($sql)->rows();
    }

    private function getWhereInfo(array $where): string
    {
        $data = [];
        foreach ($where as $k => $v) {
            $data[] = "$k = '{$v}'";
        }
        return 'where ' . implode(' and ', $data);
    }

    public function getListCount(array $data): int
    {
        $wheres = !empty($data['where']) ? $this->getWhereInfo($data['where']) : '';
        return self::getDb()->select("select * from {$this->table} {$wheres}")->count();
    }

}
