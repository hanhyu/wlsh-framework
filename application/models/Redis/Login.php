<?php
declare(strict_types=1);

namespace App\Models\Redis;
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 19-1-22
 * Time: 下午9:05
 */
class Login extends AbstractRedis
{
    protected function getKey(string $key): ?string
    {
        $datas = null;
        try {
            //$this->db->select(1);
            $datas = $this->db->get($key);
        } catch (\Exception $e) {
            co_log($e->getMessage(), "信息出错：");
        } finally {
            if ($datas === false) co_log('redis查询数据出错' . __LINE__, "信息出错：");
        }
        return $datas;
    }

}