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
    public static $del = [
        'id'       => 'Required|IntGe:1',
        'filename' => 'Required|StrLenGeLe:1,50',
    ];

}