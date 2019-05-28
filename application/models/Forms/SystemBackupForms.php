<?php
/**
 * Created by PhpStorm.
 * User: hanhyu
 * Date: 18-12-11
 * Time: 下午10:51
 */

namespace App\Models\Forms;

class SystemBackupForms
{
    /**
     * 表单字段
     * 格式为：array(方法名=>array(字段名=>验证规则))
     *
     */
    public static function del()
    {
        return [
            'id'       => 'Required|IntGe:1',
            'filename' => 'Required|StrLenGeLe:1,50',
        ];
    }

}