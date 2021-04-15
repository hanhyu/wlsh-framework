<?php
declare(strict_types=1);

namespace App\Models\Mysql;

use App\Library\AbstractPdo;
use App\Library\ProgramException;
use Envms\FluentPDO\Exception;

/**
 *
 * 数据库备份
 * UserDomain: hanhyu
 * Date: 18-12-4
 * Time: 上午10:13
 */
class SystemBackupMysql extends AbstractPdo
{
    protected string $table = 'frame_system_backup';

    public static function getPool(): string
    {
        return 'mysql_pool_obj';
    }

    /**
     * 获取数据库中所有表名
     * @return array
     * @throws ProgramException
     */
    public function getTables(): array
    {
        return self::getDb()->getPdo()->query('show tables')->fetchAll();
    }

    /**
     * 保存数据库备份文件信息
     *
     * @param array $data
     *
     * @return int
     * @throws Exception|ProgramException
     */
    public function setBackup(array $data): int
    {
        return (int)self::getDb()->insertInto($this->table)
            ->values([
                'file_name' => $data['filename'],
                'file_size' => $data['size'],
                'file_md5'  => $data['md5'],
                'crt_dt'    => date('y-m-d H:i:s', $data['rand']),
            ])
            ->execute();
    }

    /**
     * UserDomain: hanhyu
     * Date: 2019/8/18
     * Time: 下午8:48
     *
     * @param array $data
     *
     * @return array
     * @throws Exception|ProgramException
     */
    public function getList(array $data): array
    {
        $wheres = !empty($data['where']) ? $data['where'] : null;
        return self::getDb()->from($this->table)
            ->where($wheres)
            ->orderBy('id DESC')
            ->offset($data['curr_data'])
            ->limit($data['page_size'])
            ->select('id,file_name,file_size,crt_dt', true)
            ->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return array|bool
     * @throws Exception|ProgramException
     */
    public function getFileName(int $id): array|bool
    {
        return self::getDb()->from($this->table)
            ->where('id', $id)
            ->select('file_name,file_size，file_md5', true)
            ->fetch();
    }

    /**
     * UserDomain: hanhyu
     * Date: 19-6-16
     * Time: 下午8:46
     *
     * @param int $id
     *
     * @return bool
     * @throws Exception|ProgramException
     */
    public function delBackup(int $id): bool
    {
        return self::getDb()->deleteFrom($this->table, $id)->execute();
    }

    /**
     * User: hanhyu
     * Date: 2021/1/24
     * Time: 上午10:09
     * @return int
     * @throws Exception|ProgramException
     */
    public function getListCount(): int
    {
        return self::getDb()->from($this->table)->count();
    }

}
