<?php
declare(strict_types=1);

/**
 * 数据库备份
 * User: hanhyu
 * Date: 18-12-4
 * Time: 上午10:13
 */

namespace App\Models\Mysql;

use Exception;

class SystemBackup extends AbstractMysql
{
    protected $table = 'frame_system_backup';

    /**
     * 获取数据库中所有表名
     * @return array
     * @throws Exception
     */
    protected function getTables(): array
    {
        $datas = $this->db->query("show  tables ")->fetchAll();
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * 保存数据库备份文件信息
     *
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    protected function setBackup(array $data): int
    {
        $datas = $this->db->insert($this->table, [
            'file_name' => $data['filename'],
            'file_size' => $data['size'],
            'file_md5'  => $data['md5'],
            'crt_dt'    => date('y-m-d H:i:s', $data['rand']),
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return (int)$this->db->id();
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getList(): array
    {
        $datas = $this->db->select($this->table, [
            'id',
            'file_name',
            'file_size',
            'crt_dt',
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws Exception
     */
    protected function getFileName(int $id): array
    {
        $datas = $this->db->select($this->table, [
            'file_name',
            'file_size',
            'file_md5',
        ], [
            'id' => $id,
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas;
    }

    /**
     * User: hanhyu
     * Date: 19-6-16
     * Time: 下午8:46
     *
     * @param int $id
     *
     * @return int
     * @throws Exception
     */
    protected function delBackup(int $id): int
    {
        $datas = $this->db->delete($this->table, [
            'id' => $id,
        ]);
        if ($datas == false) throw new Exception($this->db->last());
        return $datas->rowCount();
    }


}