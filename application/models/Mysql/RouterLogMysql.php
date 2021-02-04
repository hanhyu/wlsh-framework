<?php declare(strict_types=1);


namespace App\Models\Mysql;


use App\Library\AbstractPdo;
use Envms\FluentPDO\Exception;

class RouterLogMysql extends AbstractPdo
{
    protected string $table = 'router_log';

    /**
     * User: hanhyu
     * Date: 2021/2/4
     * Time: 下午2:28
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    public function setLog(array $data): int
    {
        return (int)self::getDb('log_pool_obj')->insertInto($this->table)
            ->values([
                'level'      => $data['level'],
                'req_method' => $data['req_method'],
                'req_uri'    => $data['req_uri'],
                'req_data'   => json_encode($data['req_data'], JSON_THROW_ON_ERROR | 320),
                'req_ip'     => $data['req_ip'],
                'resp_data'  => $data['resp_data'],
            ])
            ->execute();
    }

    public function getList(): bool|array
    {
        return self::getDb('log_pool_obj')->from($this->table)->fetchAll();
    }

}
