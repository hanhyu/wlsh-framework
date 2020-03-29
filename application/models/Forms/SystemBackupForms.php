<?php
/**
 * Created by PhpStorm.
 * UserDomain: hanhyu
 * Date: 18-12-11
 * Time: 下午10:51
 */

namespace App\Models\Forms;

class SystemBackupForms
{
    public static array $del = [
        'id'       => 'Required|IntGe:1|Alias:文件id',
        'filename' => 'Required|StrLenGeLe:1,50|Alias:文件名称',
    ];

}
