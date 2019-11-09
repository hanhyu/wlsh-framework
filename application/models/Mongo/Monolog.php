<?php
declare(strict_types=1);

namespace App\Models\Mongo;

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\Exception;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class Monolog extends AbstractMongo
{
    /**
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function getMongoList(array $data): array
    {
        /*$filter = [
              //'level'=> 200,
          ];*/
        $options = [
            'sort'  => ['datetime' => -1],
            'skip'  => $data['curr_data'],
            'limit' => (int)$data['page_size'],
            //'projection' => ['_id'=>0],
        ];
        $res     = $this->col->find($data['where'], $options);
        return $res->toArray();
    }

    /**
     *
     * @param array $where
     *
     * @return int
     * @throws Exception
     */
    public function getMongoCount(array $where): int
    {
        return $this->col->countDocuments($where);
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws Exception
     */
    public function getMongoInfo(string $id): array
    {
        $id = new ObjectId($id);
        return $this->col->findOne(['_id' => $id])->toArray();
    }

}
