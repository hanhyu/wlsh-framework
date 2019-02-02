<?php
declare(strict_types=1);

/**
 * 数据库备份
 * User: hanhyu
 * Date: 18-12-4
 * Time: 上午10:13
 */

namespace App\Models\Mysql;


class SystemBackup extends AbstractMysql
{
    /**
     * 获取数据库中所有表名
     * @return array
     */
    protected function getTables(): array
    {
        $datas = [];
        try {
            $datas = $this->db->query("show  tables ")->fetchAll();
        } catch (\Exception $e) {
            co_log($this->db->last(), "查询信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败,失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

    /**
     * 保存数据库备份文件信息
     *
     * @param array $data
     *
     * @return int
     */
    protected function setBackup(array $data): int
    {
        $last_id = 0;
        try {
            $this->db->insert('frame_system_backup', [
                'file_name' => $data['filename'],
                'file_size' => $data['size'],
                'file_md5'  => $data['md5'],
                'crt_dt'    => date('y-m-d H:i:s', $data['rand']),
            ]);
            $last_id = $this->db->id();
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($last_id === false) {
                co_log($this->db->last(), '信息失败的sql：');
            }
        }
        return (int)$last_id;
    }

    /**
     * @return array
     */
    protected function getList(): array
    {
        $datas = [];
        try {
            $datas = $this->db->select('frame_system_backup', [
                'id',
                'file_name',
                'file_size',
                'crt_dt',
            ]);
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    protected function getFileName(int $id): array
    {
        $datas = [];
        try {
            $datas = $this->db->select('frame_system_backup', [
                'file_name',
                'file_size',
                'file_md5',
            ], [
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            co_log($this->db->last(), "信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($datas === false) {
                co_log($this->db->last(), '查询信息失败的sql：');
                $datas = [];
            }
        }
        return $datas;
    }

    protected function delBackup(int $id): int
    {
        $last_id = 0;
        try {
            $data    = $this->db->delete('frame_system_backup', [
                'id' => $id,
            ]);
            $last_id = $data->rowCount();
        } catch (\Exception $e) {
            co_log($this->db->last(), "删除信息出错：{$e->getMessage()},,出错的sql：");
        } finally {
            if ($last_id == 0) {
                co_log($this->db->last(), "删除信息失败的sql：");
            }
        }
        return $last_id;
    }


}