<?php declare(strict_types=1);


namespace App\Models\Es;


use App\Library\AbstractEs;

class InfoEs extends AbstractEs
{

    public function getInfoById(int $id): array
    {
        $res = self::getDb()->search([
            'index'   => 'index_info',
            'body'    => ['query' => ['term' => ['id' => $id]]],
            "_source" => ['id', 'name',],
        ]);
        return $res['hits']['hits'][0]['_source'] ?? [];
    }

}
