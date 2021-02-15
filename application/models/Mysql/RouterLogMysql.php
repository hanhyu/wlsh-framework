<?php declare(strict_types=1);


namespace App\Models\Mysql;


use App\Library\AbstractPdo;
use App\Library\ProgramException;
use Envms\FluentPDO\Exception;

class RouterLogMysql extends AbstractPdo
{
    protected string $table = 'router_log';
    protected string $db = 'log_pool_obj';

    /**
     * User: hanhyu
     * Date: 2021/2/4
     * Time: 下午2:28
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     * @throws ProgramException|\JsonException
     */
    public function setLog(array $data): int
    {
        return (int)self::getDb($this->db)->insertInto($this->table)
            ->values([
                'trace_id'   => $data['trace_id'],
                'level'      => $data['level'],
                'req_method' => $data['req_method'],
                'req_uri'    => $data['req_uri'],
                'req_data'   => json_encode($data['req_data'], JSON_THROW_ON_ERROR | 320),
                'req_ip'     => $data['req_ip'],
                'fd_time'    => $data['fd_time'],
                'req_time'   => $data['req_time'],
                'resp_time'  => $data['resp_time'],
                'resp_data'  => $data['resp_data'],
            ])
            ->execute();
    }

    /**
     * 路由日志列表
     *
     * User: hanhyu
     * Date: 2021/2/15
     * Time: 下午9:15
     *
     * @param array $data
     *
     * @return bool|array
     * @throws Exception
     * @throws ProgramException
     */
    public function getList(array $data): bool|array
    {
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return self::getDb($this->db)
            ->from($this->table)
            ->where($wheres)
            ->select('id,trace_id,level,req_method,req_uri,req_ip,fd_time,req_time,resp_time,create_time', true)
            ->orderBy('id DESC')
            ->offset($data['curr_data'])
            ->limit($data['page_size'])
            ->fetchAll();
    }

    public function getListCount(array $data): int
    {
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return self::getDb($this->db)->from($this->table)->where($wheres)->count();
    }


}
