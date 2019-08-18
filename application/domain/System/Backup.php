<?php
declare(strict_types=1);


namespace App\Domain\System;


use App\Models\MysqlFactory;
use Exception;

class Backup
{

    /**
     * User: hanhyu
     * Date: 19-6-22
     * Time: 下午10:12
     * @return array
     * @throws Exception
     */
    public function getTables(): array
    {
        return MysqlFactory::systemBackup()->getTables();
    }

    /**
     * User: hanhyu
     * Date: 19-6-22
     * Time: 下午10:13
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    public function setBackup(array $data): int
    {
        return MysqlFactory::systemBackup()->setBackup($data);
    }

    /**
     * User: hanhyu
     * Date: 2019/8/18
     * Time: 下午8:33
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public function getList(array $data): array
    {
        $res = [];
        if ($data['curr_page'] > 0) {
            $data['curr_data'] = ($data['curr_page'] - 1) * $data['page_size'];
        } else {
            $data['curr_data'] = 0;
        }
        $data['where'] = [];

        $res['count'] = MysqlFactory::systemBackup()->getListCount();
        $res['list']  = MysqlFactory::systemBackup()->getList($data);
        return $res;
    }

    /**
     * User: hanhyu
     * Date: 19-6-22
     * Time: 下午10:13
     *
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    public function getFileName(int $id): array
    {
        return MysqlFactory::systemBackup()->getFileName($id);
    }

    /**
     * User: hanhyu
     * Date: 19-6-22
     * Time: 下午10:13
     *
     * @param int $id
     *
     * @return int
     * @throws Exception
     */
    public function delBackup(int $id): int
    {
        return MysqlFactory::systemBackup()->delBackup($id);
    }

}