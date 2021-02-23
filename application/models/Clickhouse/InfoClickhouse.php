<?php declare(strict_types=1);


namespace App\Models\Clickhouse;


use App\Library\AbstractClickhouse;

class InfoClickhouse extends AbstractClickhouse
{
    protected string $table = 'wlsh_log';

    public function setInfo($data)
    {
        return self::getDb()->insert(
            $this->table,
            $data,
            ['id', 'req_data', 'resp_data']
        );
    }

    public function getInfoById(int $id)
    {
        return self::getDb()->select("SELECT * FROM {$this->table} WHERE id = {$id}")->fetchOne();
    }

}
