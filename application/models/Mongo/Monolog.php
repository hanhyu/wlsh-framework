<?php
declare(strict_types=1);

namespace App\Models\Mongo;

/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class Monolog extends AbstractMongo
{
    private $col = 'baseFrame.monolog';

    /**
     * @param array $data
     *
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getMongoList(array $data): array
    {
        /*$filter = [
              //'level'=> 200,
          ];*/
        $options = [
            'sort' => ['datetime' => -1],
            'skip' => $data['curr_data'],
            'limit' => $data['page_size'],
            //'projection' => ['_id'=>0],
        ];
        $query = new \MongoDB\Driver\Query($data['where'], $options);
        return $this->db->executeQuery($this->col, $query)->toArray();
    }

    /**
     *
     * @param array $where
     *
     * @return int
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getMongoCount(array $where): int
    {
        $query = new \MongoDB\Driver\Query($where, []);
        $cursor = $this->db->executeQuery($this->col, $query);
        return count($cursor->toArray());
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function getMongoInfo(string $id): array
    {
        $id = new \MongoDB\BSON\ObjectId($id);
        $query = new \MongoDB\Driver\Query(['_id' => $id], []);
        return $this->db->executeQuery($this->col, $query)->toArray();
    }

}