<?php
declare(strict_types=1);

namespace App\Models\Redis;

use Exception;

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
        //$this->db->select(1);
        $datas = $this->db->get($key);
        if ($datas == false) $datas = null;
        return $datas;
    }

}