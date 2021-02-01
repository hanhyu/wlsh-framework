<?php
declare(strict_types=1);

namespace App\Models\Mongo;

use App\Library\AbstractMongo;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\Exception;

/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-9-26
 * Time: 下午3:09
 */
class MonologMongo extends AbstractMongo
{
    /**
     * 此处使用静态延迟绑定，实现选择不同的实例,如不设置默认为配置文件中的collection值
     * @var string
     */
    protected static string $col = 'monolog';

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
        return self::getDb()->find($data['where'], $options)->toArray();
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
        return self::getDb()->countDocuments($where);
    }

    /**
     * @param string $id
     *
     * @return object|null
     */
    public function getMongoInfo(string $id): ?object
    {
        return self::getDb()->findOne(['_id' => new ObjectId($id)]);
    }

}
